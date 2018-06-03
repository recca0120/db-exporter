<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;
use Illuminate\Filesystem\Filesystem;
use Recca0120\DbExporter\DumperFactory;

class DbExporterTest extends TestCase
{
    private $app = [];

    protected function setUp()
    {
        parent::setUp();
        $this->app['config']['database'] = require __DIR__.'/../config/database.php';
        $this->app['config']['db-exporter'] = require __DIR__.'/../config/db-exporter.php';
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_it_should_get_instance()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter'];

        $this->assertInstanceOf(DbExporter::class, new DbExporter($connection, $settings));
    }

    public function test_it_should_store_sql()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter'];
        $factory = m::mock(DumperFactory::class);
        $files = m::mock(Filesystem::class);
        $dbExporter = new DbExporter($connection, $settings, $factory, $files);
        $dumpFile = 'dump.sql';

        $files->shouldReceive('exists')->once()->andReturn(false);
        $files->shouldReceive('makeDirectory')->once();

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
            Arr::forget($settings, 'storage_path');
            $settings['compress'] = Mysqldump::NONE;

            return $arguments === $settings;
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->with($this->storagePath($dumpFile));

        $this->assertTrue($dbExporter->store($dumpFile));
    }

    public function test_it_should_store_gzip()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter'];
        $factory = m::mock(DumperFactory::class);
        $files = m::mock(Filesystem::class);
        $dbExporter = new DbExporter($connection, $settings, $factory, $files);
        $dumpFile = 'dump.sql.gz';

        $files->shouldReceive('exists')->once()->andReturn(false);
        $files->shouldReceive('makeDirectory')->once();

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
            Arr::forget($settings, 'storage_path');
            $settings['compress'] = Mysqldump::GZIP;

            return $arguments === $settings;
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->with($this->storagePath($dumpFile));

        $this->assertTrue($dbExporter->store($dumpFile));
    }

    public function test_it_should_store_bzip2()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter'];
        $factory = m::mock(DumperFactory::class);
        $files = m::mock(Filesystem::class);
        $dbExporter = new DbExporter($connection, $settings, $factory, $files);
        $dumpFile = 'dump.sql.bzip2';

        $files->shouldReceive('exists')->once()->andReturn(false);
        $files->shouldReceive('makeDirectory')->once();

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
            Arr::forget($settings, 'storage_path');
            $settings['compress'] = Mysqldump::BZIP2;

            return $arguments === $settings;
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->with($this->storagePath($dumpFile));

        $this->assertTrue($dbExporter->store($dumpFile));
    }

    public function test_it_should_store_empty_filename()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter'];
        $settings['compress'] = Mysqldump::BZIP2;
        $factory = m::mock(DumperFactory::class);
        $files = m::mock(Filesystem::class);
        $dbExporter = new DbExporter($connection, $settings, $factory, $files);
        $dumpFile = '';

        $files->shouldReceive('exists')->once()->andReturn(false);
        $files->shouldReceive('makeDirectory')->once();

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
            Arr::forget($settings, 'storage_path');

            return $arguments === $settings;
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->with($this->storagePath($connection['database'].'-'.date('YmdHis').'.sql.bzip2'));

        $this->assertTrue($dbExporter->store($dumpFile));
    }

    /** @test */
    public function test_it_should_change_settings()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = array_merge($this->app['config']['db-exporter'], [
            'compress' => Mysqldump::NONE,
        ]);
        $factory = m::mock(DumperFactory::class);
        $files = m::mock(Filesystem::class);
        $dbExporter = new DbExporter($connection, $settings, $factory, $files);
        $dumpFile = 'dump.sql';

        $dbExporter->lockTables(false);

        $files->shouldReceive('exists')->once()->andReturn(false);
        $files->shouldReceive('makeDirectory')->once();

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
            Arr::forget($settings, 'storage_path');

            return $arguments['lock-tables'] === false;
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->with($this->storagePath($dumpFile));

        $this->assertTrue($dbExporter->store($dumpFile));
    }

    private function storagePath($path)
    {
        return storage_path('db-exporter/'.$path);
    }
}
