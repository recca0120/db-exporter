<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;

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

    public function lockTables($lockTables = true)
    {
        return $this->set('lock-tables', $this->boolean($lockTables));
    }

    public function set($key, $value = null)
    {
        Arr::set($this->settings, $key, $value);

        return $this;
    }

    public function dump($output = false)
    {
        if (function_exists('set_time_limit') === true) {
            set_time_limit(0);
        }

        $dumper = $this->factory->create(
            $this->connection,
            array_merge($this->settings, [
                'compress' => Mysqldump::NONE,
            ])
        );

        if ($output === true) {
            $dumper->start();

            return '';
        }

        ob_start();
        $dumper->start();

        return ob_get_clean();
    }

    public function getDatabaseName()
    {
        return Arr::get($this->connection, 'database');
    }

    private function boolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
