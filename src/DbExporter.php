<?php

namespace Recca0120\DbExporter;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class DbExporter
{
    private $dumperFactory;

    private $filesystemFactory;

    private $config = [];

    private $directory = 'db-export';

    public function __construct(DumperFactory $dumperFactory, FilesystemFactory $filesystemFactory, $config)
    {
        $this->dumperFactory = $dumperFactory;
        $this->filesystemFactory = $filesystemFactory;
        $this->config = $config;
    }

    public function lockTables($lockTables = true)
    {
        return $this->set('lock-tables', $this->boolean($lockTables));
    }

    public function set($key, $value = null)
    {
        Arr::set($this->config, 'settings.'.$key, $value);

        return $this;
    }

    public function dump($output = false)
    {
        if (function_exists('set_time_limit') === true) {
            set_time_limit(0);
        }

        $dumper = $this->dumperFactory->create(
            Arr::get($this->config, 'connection'),
            array_merge(Arr::get($this->config, 'settings'), [
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

    public function store($disk = null)
    {
        $file = sprintf('%s-%s.sql.gz', Arr::get($this->config, 'connection.database'), Carbon::now()->format('YmdHis'));
        $disk = $this->filesystemFactory->disk($disk ?: Arr::get($this->config, 'disk'));
        $disk->put($this->directory.'/'.$file, gzencode($this->dump(false)));

        $this->cleanup($disk);
    }

    private function cleanup($disk)
    {
        $expireDate = Carbon::now()->subDays('3');
        $files = array_filter($disk->files($this->directory), function ($file) use ($disk, $expireDate) {
            return $expireDate->gt(Carbon::createFromTimestamp($disk->lastModified($file)));
        });

        $disk->delete($files);
    }

    private function boolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
