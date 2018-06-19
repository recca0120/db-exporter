<?php

namespace Recca0120\DbExporter;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class DbExporter
{
    private $dumperFactory;

    private $files;

    private $config = [];

    private $directory = 'db-export';

    public function __construct(DumperFactory $dumperFactory, FilesystemFactory $files, $config)
    {
        $this->dumperFactory = $dumperFactory;
        $this->files = $files;
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

            return;
        }

        ob_start();
        $dumper->start();

        return ob_get_clean();
    }

    public function store($filename = null, $disk = null)
    {
        $extension = Arr::get([
            strtolower(Mysqldump::GZIP) => '.gz',
            strtolower(Mysqldump::BZIP2) => '.bz2',
            strtolower(Mysqldump::NONE) => '',
        ], strtolower(Arr::get($this->config, 'settings.compress')), '');

        $filename = $filename ?: sprintf('%s/%s-%s.sql', $this->directory, Arr::get($this->config, 'connection.database'), Carbon::now()->format('YmdHis'));
        if (empty($extension) === false && (bool) preg_match('/\.'.$extension.'$/', $filename) === false) {
            $filename .= $extension;
        }

        $disk = $this->files->disk($disk ?: Arr::get($this->config, 'disk'));
        $disk->put($filename, $this->compressContents($this->dump(false), $extension));

        $this->cleanup($disk);
    }

    private function compressContents($contents, $extension)
    {
        if ($extension === '.gz') {
            return gzencode($contents);
        }

        if ($extension === '.bz2') {
            return bzcompress($contents);
        }

        return $contents;
    }

    private function cleanup($disk)
    {
        $expireDate = Carbon::now()->subDays('3');

        $disk->delete(array_filter($disk->files($this->directory), function ($file) use ($disk, $expireDate) {
            return $expireDate->gt(Carbon::createFromTimestamp($disk->lastModified($file)));
        }));
    }

    private function boolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
