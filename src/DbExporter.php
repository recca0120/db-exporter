<?php

namespace Recca0120\DbExporter;

class DbExporter
{
    private $connection;

    private $settings = [];

    private $factory;

    public function __construct($connection, $settings = [], DumperFactory $factory = null)
    {
        $this->connection = $connection;
        $this->settings = $settings;
        $this->factory = $factory ?: new DumperFactory();
    }

    public function store($filename = '')
    {
    }
}
