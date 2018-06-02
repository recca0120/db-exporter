<?php

namespace Recca0120\DbExporter\Tests\DSN;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\Dsn\MySql;

class MySqlTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_get_host_dsn()
    {
        $config = [
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

        $dsn = new MySql($config);
        $this->assertSame((string) $dsn, "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}");
    }

    public function test_get_host_dsn_without_port()
    {
        $config = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
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

        $dsn = new MySql($config);
        $this->assertSame((string) $dsn, "mysql:host={$config['host']};dbname={$config['database']}");
    }

    public function test_get_socket_dsn()
    {
        $config = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'forge',
            'username' => 'forge',
            'password' => '',
            'unix_socket' => '/var/run/mysqld.socket',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'timezone' => '+08:00',
        ];

        $dsn = new MySql($config);
        $this->assertSame((string) $dsn, "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}");
    }
}
