<?php

namespace Recca0120\DbExporter\Dsn;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class Factory
{
    private $drivers = [
        'mysql' => Mysql::class,
    ];

    public function create($config)
    {
        $driver = Arr::get($this->drivers, $config['driver']);

        if (is_null($driver) === true) {
            throw new InvalidArgumentException("Dsn [$driver] not supported.");
        }

        return new $driver($config);
    }
}
