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
        $connection = $this->getConnection($driver);

        return new DbExporter(
            $this->factory->create(
                $connection,
                Arr::get($this->app['config'], 'db-exporter', [])
            )
        );
    }

    private function getConnection($driver)
    {
        return Arr::get($this->app['config'], 'database.connections.'.$driver);
    }
}
