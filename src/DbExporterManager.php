<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;

class DbExporterManager extends Manager
{
    private $factory;

    public function __construct($app, DumperFactory $factory = null)
    {
        parent::__construct($app);
        $this->factory = $factory ?: new DumperFactory;
    }

    public function getDefaultDriver()
    {
        return Arr::get($this->app['config'], 'database.default');
    }

    protected function createDriver($driver)
    {
        $connection = $this->connection($driver);

        return new DbExporter(
            $connection,
            $this->settings($connection),
            $this->factory
        );
    }

    private function connection($driver)
    {
        return Arr::get($this->app['config'], 'database.connections.'.$driver);
    }

    private function settings($connection)
    {
        $settings = Arr::get($this->app['config'], 'db-exporter.settings', []);

        if (empty($connection['charset']) === false) {
            $settings['default-character-set'] = $connection['charset'];
        }

        return $settings;
    }
}
