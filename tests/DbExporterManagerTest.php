<?php

namespace Recca0120\DbExporter\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DbExporterManager;

class DbExporterManagerTest extends TestCase
{
    private $app = [
        'config' => [
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'sqlite' => [
                        'driver' => 'sqlite',
                        'database' => 'database.sqlite',
                        'prefix' => '',
                    ],

                    'mysql' => [
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
                    ],

                    'pgsql' => [
                        'driver' => 'pgsql',
                        'host' => '127.0.0.1',
                        'port' => '5432',
                        'database' => 'forge',
                        'username' => 'forge',
                        'password' => '',
                        'charset' => 'utf8',
                        'prefix' => '',
                        'schema' => 'public',
                        'sslmode' => 'prefer',
                    ],

                    'sqlsrv' => [
                        'driver' => 'sqlsrv',
                        'host' => 'localhost',
                        'port' => '1433',
                        'database' => 'forge',
                        'username' => 'forge',
                        'password' => '',
                        'charset' => 'utf8',
                        'prefix' => '',
                    ],
                ],
            ],
        ],
    ];

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
}
