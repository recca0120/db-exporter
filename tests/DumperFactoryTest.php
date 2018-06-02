<?php

namespace Recca0120\DbExporter\Tests;

use PDO;
use Mockery as m;
use Ifsnop\Mysqldump\Mysqldump;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DumperFactory;

class DumperFactoryTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_it_should_receive_ifsnop_mysqldump()
    {
        $dumperFactory = new DumperFactory();

        $connectionConfig = [
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
        $dumpSettings = [];
        $pdoSettings = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
        ];

        $this->assertInstanceOf(Mysqldump::class, $dumperFactory->create($connectionConfig, $dumpSettings, $pdoSettings));
    }
}
