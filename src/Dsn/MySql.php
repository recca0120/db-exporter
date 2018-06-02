<?php

namespace Recca0120\DbExporter\Dsn;

class MySql
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getDsn()
    {
        extract($this->config, EXTR_SKIP);

        return "mysql:host={$host};port={$port};dbname={$database}";
    }

    public function __toString()
    {
        return $this->getDsn();
    }
}