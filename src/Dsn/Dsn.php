<?php

namespace Recca0120\DbExporter\Dsn;

abstract class Dsn
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function __toString()
    {
        return $this->toString();
    }

    abstract public function toString();
}
