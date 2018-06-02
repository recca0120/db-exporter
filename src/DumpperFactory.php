<?php

namespace Recca0120\DbExporter;

use Ifsnop\Mysqldump\Mysqldump;
use Recca0120\DbExporter\Dsn\Factory;

class DumpperFactory
{
    private $config;

    private $factory;

    public function __construct($config, Factory $factory = null)
    {
        $this->config = $config;
        $this->factory = $factory ?: new Factory;
    }

    public function create()
    {
        return new Mysqldump(
            (string) $this->factory->create($this->config),
            $this->config['username'],
            $this->config['password']
        );
    }
}