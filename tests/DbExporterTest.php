<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;
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
        $options = $this->app['config']['db-exporter'];

        $this->assertInstanceOf(DbExporter::class, new DbExporter($connection, $options));
    }

    public function test_it_should_store_sql()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $options = $this->app['config']['db-exporter'];
        $settings = $this->app['config']['db-exporter']['settings'];
        $factory = m::mock(DumperFactory::class);
        $dbExporter = new DbExporter($connection, $settings, $factory);

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
            $settings['compress'] = Mysqldump::NONE;

            return $arguments === $settings;
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
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter']['settings'];
        $factory = m::mock(DumperFactory::class);
        $dbExporter = new DbExporter($connection, $settings, $factory);

        $dbExporter->lockTables(false);

        $factory->shouldReceive('create')->once()->with($connection, m::on(function ($arguments) use ($settings) {
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
