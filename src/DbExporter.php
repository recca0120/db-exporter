<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;

class DbExporter
{
    private $connection;

    private $settings = [];

    private $factory;

    private $compress = [
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

    public function store($filename = null, $storagePath = null)
    {
        if (empty($filename) === true) {
            $compress = ucfirst(Arr::get($this->settings, 'compress', Mysqldump::NONE));
            $extension = Arr::get(array_flip($this->compress), $compress, '');
            $extension = empty($extension) === true ? '' : '.'.$extension;
            $database = Arr::get($this->connection, 'database');
            $filename = sprintf('%s-%s.sql%s', $database, date('YmdHis'), $extension);
        }

        return $this->dump($filename, $storagePath);
    }

    public function dump($filename = null, $storgePath = null)
    {
        $settings = $this->settings($filename);

        $storgePath = $storgePath ?: Arr::get($settings, 'storage_path');

        if (empty($storgePath) === false) {
            $storgePath = rtrim($storgePath, '/').'/';
        }

        $dumper = $this->factory->create($this->connection, $settings);
        $dumper->start($storgePath.$filename);

        return true;
    }

    private function extension($filename)
    {
        $lastestDotPosition = strrpos($filename, '.');

        return $lastestDotPosition !== false
            ? strtolower(substr($filename, strrpos($filename, '.') + 1))
            : '';
    }

    private function settings($filename)
    {
        $settings = $this->settings;
        $extension = $this->extension($filename);
        if (empty($extension) === false) {
            $settings['compress'] = Arr::get($this->compress, $extension, Mysqldump::NONE);
        }

        return $settings;
    }
}
