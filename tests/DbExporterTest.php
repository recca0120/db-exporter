<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporter;

class DbExporterTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_it_should_get_instance()
    {
        $this->assertInstanceOf(DbExporter::class, new DbExporter([]));
    }
}
