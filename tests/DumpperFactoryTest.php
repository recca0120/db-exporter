<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DumpperFactory;
use Ifsnop\Mysqldump\Mysqldump;

class DumpperFactoryTest extends TestCase
{
    private $config = [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'forge',
        'username' => 'forge',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
        'timezone' => '+08:00',
    ];

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_it_should_receive_ifsnop_mysqldump()
    {
        $dumpperFactory = new DumpperFactory();

        $this->assertInstanceOf(Mysqldump::class, $dumpperFactory->create($this->config));
    }
}
