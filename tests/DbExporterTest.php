<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;
use Recca0120\DbExporter\DumperFactory;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class DbExporterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $app = [];

    protected function setUp()
    {
        parent::setUp();
        $this->app['config']['database'] = require __DIR__.'/../config/database.php';
        $this->app['config']['db-exporter'] = require __DIR__.'/../config/db-exporter.php';
    }

    /** @test */
    public function test_it_should_get_instance()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $files = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));

        $this->assertInstanceOf(DbExporter::class, new DbExporter($dumperFactory, $files, $config));
    }

    public function test_it_should_dump_sql()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $files = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));

        $dbExporter = new DbExporter($dumperFactory, $files, $config);

        $dumperFactory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($config) {
            return $arguments === array_merge($config['settings'], [
                'compress' => Mysqldump::NONE,
            ]);
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->andReturnUsing(function () {
            echo 'output';
        });

        $this->assertSame('output', $dbExporter->dump());
    }

    /** @test */
    public function test_it_should_store_sql()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $files = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));
        $config['settings']['compress'] = Mysqldump::NONE;

        $dbExporter = new DbExporter($dumperFactory, $files, $config);

        $dumperFactory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($config) {
            return $arguments === array_merge($config['settings'], [
                'compress' => Mysqldump::NONE,
            ]);
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->andReturnUsing(function () {
            echo 'output';
        });

        $filename = sprintf('db-export/%s-%s.sql', Arr::get($config, 'connection.database'), Carbon::now()->format('YmdHis'));
        $files->shouldReceive('disk')->with($config['disk'])->andReturn($disk = m::mock(stdClass::class));
        $disk->shouldReceive('put')->with($filename, 'output');

        $disk->shouldReceive('files')->once()->andReturnUsing(function () {
            return [
                '1.sql',
                '2.sql',
                '3.sql',
            ];
        });

        $disk->shouldReceive('lastModified')->times(3)->andReturnUsing(function ($filename) {
            switch ($filename) {
                case '1.sql':
                    return strtotime('now');
                case '2.sql':
                    return strtotime('-5day');
                case '3.sql':
                    return strtotime('-10day');
            }
        });

        $disk->shouldReceive('delete')->once()->with(m::on(function ($files) {
            return array_values($files) === [
                '2.sql', '3.sql',
            ];
        }));

        $dbExporter->store();
    }

    /** @test */
    public function test_it_should_store_sql_dot_gz()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $files = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));
        $config['settings']['compress'] = Mysqldump::GZIP;

        $dbExporter = new DbExporter($dumperFactory, $files, $config);

        $dumperFactory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($config) {
            return $arguments === array_merge($config['settings'], [
                'compress' => Mysqldump::NONE,
            ]);
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->andReturnUsing(function () {
            echo 'output';
        });

        $filename = sprintf('db-export/%s-%s.sql.gz', Arr::get($config, 'connection.database'), Carbon::now()->format('YmdHis'));
        $files->shouldReceive('disk')->with($config['disk'])->andReturn($disk = m::mock(stdClass::class));
        $disk->shouldReceive('put')->with($filename, gzencode('output'));

        $disk->shouldReceive('files')->once()->andReturnUsing(function () {
            return [
                '1.sql.gz',
                '2.sql.gz',
                '3.sql.gz',
            ];
        });

        $disk->shouldReceive('lastModified')->times(3)->andReturnUsing(function ($filename) {
            switch ($filename) {
                case '1.sql.gz':
                    return strtotime('now');
                case '2.sql.gz':
                    return strtotime('-5day');
                case '3.sql.gz':
                    return strtotime('-10day');
            }
        });

        $disk->shouldReceive('delete')->once()->with(m::on(function ($files) {
            return array_values($files) === [
                '2.sql.gz', '3.sql.gz',
            ];
        }));

        $dbExporter->store();
    }

    /** @test */
    public function test_it_should_store_sql_dot_bzip2()
    {
        if (function_exists('bzcompress') === false) {
            $this->markTestSkipped();
        }

        $dumperFactory = m::mock(DumperFactory::class);
        $files = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));
        $config['settings']['compress'] = Mysqldump::BZIP2;

        $dbExporter = new DbExporter($dumperFactory, $files, $config);

        $dumperFactory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($config) {
            return $arguments === array_merge($config['settings'], [
                'compress' => Mysqldump::NONE,
            ]);
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->andReturnUsing(function () {
            echo 'output';
        });

        $filename = sprintf('db-export/%s-%s.sql.bz2', Arr::get($config, 'connection.database'), Carbon::now()->format('YmdHis'));
        $files->shouldReceive('disk')->with($config['disk'])->andReturn($disk = m::mock(stdClass::class));
        $disk->shouldReceive('put')->with($filename, bzcompress('output'));

        $disk->shouldReceive('files')->once()->andReturnUsing(function () {
            return [
                '1.sql.bz2',
                '2.sql.bz2',
                '3.sql.bz2',
            ];
        });

        $disk->shouldReceive('lastModified')->times(3)->andReturnUsing(function ($filename) {
            switch ($filename) {
                case '1.sql.bz2':
                    return strtotime('now');
                case '2.sql.bz2':
                    return strtotime('-5day');
                case '3.sql.bz2':
                    return strtotime('-10day');
            }
        });

        $disk->shouldReceive('delete')->once()->with(m::on(function ($files) {
            return array_values($files) === [
                '2.sql.bz2', '3.sql.bz2',
            ];
        }));

        $dbExporter->store();
    }

    /** @test */
    public function test_it_should_change_settings()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $files = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));

        $dbExporter = new DbExporter($dumperFactory, $files, $config);

        $dbExporter->lockTables(false);

        $dumperFactory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($config) {
            return $arguments['lock-tables'] === false;
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->andReturnUsing(function () {
            echo 'output';
        });

        $this->assertSame('output', $dbExporter->dump());
    }
}
