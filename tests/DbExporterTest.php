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
        $settings = $this->app['config']['db-exporter'];

        $this->assertInstanceOf(DbExporter::class, new DbExporter($connection, $settings));
    }

    public function test_it_should_store_sql()
    {
        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = $this->app['config']['db-exporter'];
        $factory = m::mock(DumperFactory::class);

        $dbExporter = new DbExporter($connection, $settings, $factory);

        $factory->shouldReceive('create')->once()->with($connection, $settings)->andReturn(
            $dumper = m::mock(Mysqldump::class)
        );

        $dumper->shouldReceive('start')->once()->with('dump.sql');

        $this->assertTrue($dbExporter->store('dump.sql'));
    }
}
