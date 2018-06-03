<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Arr;
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
        $dsn = $this->factory->create($connection);

        return new Mysqldump(
            (string) $dsn,
            Arr::get($connection, 'username', ''),
            Arr::get($connection, 'password', ''),
            $settings,
            array_merge($dsn->getPdoAttributes(), $pdoAttributes)
        );
    }
}
