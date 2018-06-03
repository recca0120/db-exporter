<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;

class DbExporter
{
    private $connection;

    private $settings = [];

    private $factory;

    private $extensions = [
        'gz' => Mysqldump::GZIP,
        'gzip' => Mysqldump::GZIP,
        'bzip2' => Mysqldump::BZIP2,
    ];

    public function __construct($connection, $settings = [], DumperFactory $factory = null)
    {
        $this->connection = $connection;
        $this->settings = $settings;
        $this->factory = $factory ?: new DumperFactory();
    }

    public function store($filename = null)
    {
        return $this->dump($filename);
    }

    public function dump($filename = null)
    {
        $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
        $settings = $this->settings;
        $settings['compress'] = Arr::get($this->extensions, $extension, Mysqldump::NONE);

        $dumper = $this->factory->create($this->connection, $settings);
        $dumper->start($filename);

        return true;
    }
}
