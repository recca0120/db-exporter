<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class DbExporterManager extends Manager
{
    private $dumperFactory;

    private $filesystemFactory;

    public function __construct($app, DumperFactory $dumperFactory, FilesystemFactory $filesystemFactory)
    {
        parent::__construct($app);
        $this->dumperFactory = $dumperFactory;
        $this->filesystemFactory = $filesystemFactory;
    }

    public function getDefaultDriver()
    {
        return Arr::get($this->app['config'], 'database.default');
    }

    protected function createDriver($driver)
    {
        $connection = $this->connection($driver);

        return new DbExporter(
            $this->dumperFactory,
            $this->filesystemFactory,
            $this->config($driver)
        );
    }

    private function connection($driver)
    {
        return Arr::get($this->app['config'], 'database.connections.'.$driver);
    }

    private function config($driver)
    {
        $connection = Arr::get($this->app['config'], 'database.connections.'.$driver);
        $config = Arr::get($this->app['config'], 'db-exporter', []);
        if (empty($connection['charset']) === false) {
            Arr::set($config, 'settings.default-character-set', $connection['charset']);
        }

        return array_merge($config, compact('connection'));
    }
}
