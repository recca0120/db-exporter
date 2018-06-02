<?php

namespace Recca0120\DbExporter;

use Ifsnop\Mysqldump\Mysqldump;
use Recca0120\DbExporter\Dsn\Factory;

class DumperFactory
{
    private $factory;

    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory ?: new Factory;
    }

    public function create($config)
    {
        return new Mysqldump(
            (string) $this->factory->create($config),
            $config['username'],
            $config['password']
        );
    }
}