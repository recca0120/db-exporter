<?php

namespace Recca0120\DbExporter;

class DbExporter
{
    private $connection;

    private $settings = [];

    public function __construct($connection, $settings = [], DumperFactory $factory = null)
    {
        $this->connection = $connection;
        $this->settings = array_merge($this->settings, $settings);
        $this->factory = $factory ?: new DumperFactory;
    }
}