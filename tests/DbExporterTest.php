<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;
use Recca0120\DbExporter\DumperFactory;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

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
        $dumperFactory = m::mock(DumperFactory::class);
        $filesystemFactory = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));

        $this->assertInstanceOf(DbExporter::class, new DbExporter($dumperFactory, $filesystemFactory, $config));
    }

    public function test_it_should_store_sql()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $filesystemFactory = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));

        $dbExporter = new DbExporter($dumperFactory, $filesystemFactory, $config);

        $dumperFactory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($config) {
            $config['settings']['compress'] = Mysqldump::NONE;

            return $arguments === $config['settings'];
        }))->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->andReturnUsing(function () {
            echo 'output';
        });

        $this->assertSame('output', $dbExporter->dump());
    }

    /** @test */
    public function test_it_should_change_settings()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $filesystemFactory = m::mock(FilesystemFactory::class);
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $config = array_merge($this->app['config']['db-exporter'], compact('connection'));

        $dbExporter = new DbExporter($dumperFactory, $filesystemFactory, $config);

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

    /** @test */
    public function test_it_should_store_to_file()
    {
        // assertions
    }
}
