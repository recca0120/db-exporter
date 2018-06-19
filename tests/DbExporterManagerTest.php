<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;
use Recca0120\DbExporter\DumperFactory;
use Recca0120\DbExporter\DbExporterManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class DbExporterManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $app = [];

    protected function setUp()
    {
        parent::setUp();
        $this->app['config']['database'] = require __DIR__.'/../config/database.php';
    }

    /** @test */
    public function test_it_should_be_instance()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $filesystemFactory = m::mock(FilesystemFactory::class);

        $this->assertInstanceOf(DbExporterManager::class, new DbExporterManager($this->app, $dumperFactory, $filesystemFactory));
    }

    /** @test */
    public function test_it_should_get_default_driver()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $filesystemFactory = m::mock(FilesystemFactory::class);

        $manager = new DbExporterManager($this->app, $dumperFactory, $filesystemFactory);

        $this->assertSame('mysql', $manager->getDefaultDriver());
    }

    /** @test */
    public function it_should_get_mysql_driver()
    {
        $dumperFactory = m::mock(DumperFactory::class);
        $filesystemFactory = m::mock(FilesystemFactory::class);

        $manager = new DbExporterManager($this->app, $dumperFactory, $filesystemFactory);

        $this->assertInstanceOf(DbExporter::class, $manager->driver());
    }
}
