<?php

namespace Recca0120\DbExporter\Console\Commands;

use Illuminate\Console\Command;
use Recca0120\DbExporter\DbExporterManager;
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

    public function __construct(DbExporterManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * Handle the command.
     */
    public function handle()
    {
        $settings = [];
        $dumper = $this->manager->driver($this->option('connection'));
        $lockTables = $this->boolean($this->option('lock-tables'));

        $dumper
            ->lockTables($this->boolean($this->option('lock-tables')))
            ->store($this->argument('file'), $this->option('path'));
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
            ['file', InputArgument::OPTIONAL, 'file'],
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
            ['path', 'o', InputOption::VALUE_OPTIONAL, 'Storage Path'],
            ['connection', 'c', InputOption::VALUE_OPTIONAL, 'Connection Name'],
            ['lock-tables', null, InputOption::VALUE_OPTIONAL, 'Lock Tables', true],
        ];
    }
}
