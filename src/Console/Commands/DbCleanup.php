<?php

namespace Recca0120\DbExporter\Console\Commands;

use Illuminate\Console\Command;
use Recca0120\DbExporter\DbExporterManager;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DbCleanup extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db-export:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'db export cleanup';

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
        $this->manager->cleanup($this->option('disk'));
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
            ['disk', null, InputOption::VALUE_OPTIONAL, 'Storage'],
            ['connection', null, InputOption::VALUE_OPTIONAL, 'Connection Name'],
            ['lock-tables', null, InputOption::VALUE_OPTIONAL, 'Lock Tables', true],
        ];
    }
}
