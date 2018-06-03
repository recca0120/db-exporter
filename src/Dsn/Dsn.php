<?php

namespace Recca0120\DbExporter\Dsn;

use PDO;

abstract class Dsn
{
    protected $config;

    protected $pdoAttributes = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getPdoAttributes()
    {
        return $this->pdoAttributes;
    }

    abstract public function toString();
}
