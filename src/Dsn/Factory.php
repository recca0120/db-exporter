<?php

namespace Recca0120\DbExporter\Dsn;

class Factory
{
    private $drivers = [
        'mysql' => MySql::class,
    ];

    public function create($config)
    {
        return new $this->drivers[$config['driver']]($config);
    }
}
