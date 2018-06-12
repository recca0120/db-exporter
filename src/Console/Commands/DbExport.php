<?php

namespace Recca0120\DbExporter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Recca0120\DbExporter\DbExporterManager;
use Illuminate\Filesystem\FilesystemManager;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DbExport extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db-export:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sqldump';

    private $manager;

    private $files;

    public function __construct(DbExporterManager $manager, FilesystemManager $files)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->files = $files;
    }

    /**
     * Handle the command.
     */
    public function handle()
    {
        $dumper = $this->manager
            ->driver($this->option('connection'))
            ->lockTables($this->option('lock-tables'));

        $databaseName = $dumper->getDatabaseName();
        $file = sprintf('%s-%s.sql.gz', $databaseName, date('YmdHis'));

        $disk = $this->files->disk($this->option('storage') ?: 'local');
        $disk->put('sqldump/'.$file, gzencode($dumper->dump()));

        $this->cleanup($disk);
    }

    /**
     * Fire the command.
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['file', InputArgument::OPTIONAL, 'file'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['storage', 's', InputOption::VALUE_OPTIONAL, 'Storage'],
            ['connection', 'c', InputOption::VALUE_OPTIONAL, 'Connection Name'],
            ['lock-tables', null, InputOption::VALUE_OPTIONAL, 'Lock Tables', true],
        ];
    }

    private function cleanup($disk)
    {
        $timeLimit = Carbon::now()->subDays('3');
        $files = array_filter($disk->files('sqldump'), function ($file) use ($disk, $timeLimit) {
            return $timeLimit->gt(Carbon::createFromTimestamp($disk->lastModified($file)));
        });

        $disk->delete($files);
    }
}
