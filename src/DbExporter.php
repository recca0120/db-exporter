<?php

namespace Recca0120\DbExporter;

class DbExporter
{
    private $connectionConfig;

    private $dumpSettings = [];

    public function __construct($connectionConfig, $dumpSettings = [], DumperFactory $factory = null)
    {
        $this->connectionConfig = $connectionConfig;
        $this->dumpSettings = array_merge($this->dumpSettings, $dumpSettings);
        $this->factory = $factory ?: new DumperFactory;
    }
}
