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

    public function create($connection, $settings = [], $pdoAttributes = [])
    {
        return new Mysqldump(
            (string) $this->factory->create($connection),
            $connection['username'],
            $connection['password'],
            $settings,
            $pdoAttributes
        );
    }
}
