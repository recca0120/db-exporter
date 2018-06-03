<?php

namespace Recca0120\DbExporter;

use Illumintate\Support\Arr;
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
            Arr::get($connection, 'username', ''),
            Arr::get($connection, 'password', ''),
            $settings,
            $pdoAttributes
        );
    }
}
