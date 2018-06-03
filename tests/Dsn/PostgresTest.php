<?php

namespace Recca0120\DbExporter\Tests\Dsn;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\Dsn\Postgres;

class PostgresTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_get_dsn()
    {
        $config = [
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
            'sslcert' => 'sslcert',
            'sslkey' => 'sslkey',
            'sslrootcert' => 'sslrootcert',
        ];

        $dsn = new Postgres($config);
        $this->assertSame(
            (string) $dsn,
            "pgsql:host={$config['host']};dbname={$config['database']};port={$config['port']};sslmode={$config['sslmode']};sslcert={$config['sslcert']};sslkey={$config['sslkey']};sslrootcert={$config['sslrootcert']}"
        );
    }
}
