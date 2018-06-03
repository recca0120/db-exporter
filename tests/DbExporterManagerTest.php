<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;
use Recca0120\DbExporter\DbExporterManager;

class DbExporterManagerTest extends TestCase
{
    private $app = [];

    protected function setUp()
    {
        parent::setUp();
        $this->app['config']['database'] = require __DIR__.'/../config/database.php';
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_it_should_be_instance()
    {
        $this->assertInstanceOf(DbExporterManager::class, new DbExporterManager($this->app));
    }

    /** @test */
    public function test_it_should_get_default_driver()
    {
        $manager = new DbExporterManager($this->app);

        $this->assertSame('mysql', $manager->getDefaultDriver());
    }

    /** @test */
    public function it_should_get_mysql_driver()
    {
        $manager = new DbExporterManager($this->app);

        $this->assertInstanceOf(DbExporter::class, $manager->driver());
    }
}
