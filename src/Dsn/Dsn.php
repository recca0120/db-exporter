<?php

namespace Recca0120\DbExporter\Dsn;

use Illuminate\Support\Arr;

abstract class Dsn
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;

        $writeHost = Arr::get($this->config, 'write.host');
        if (empty($writeHost) === false) {
            $this->config['host'] = $writeHost;
        }
    }

    public function __toString()
    {
        return $this->toString();
    }

    abstract public function toString();
}
