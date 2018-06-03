<?php

namespace Recca0120\DbExporter\Tests;

use PDO;
use Mockery as m;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump\Mysqldump;
use PHPUnit\Framework\TestCase;
use Recca0120\DbExporter\DumperFactory;

class DumperFactoryTest extends TestCase
{
    private $app = [];

    protected function setUp()
    {
        parent::setUp();
        $this->app['config']['database'] = require __DIR__ . '/../config/database.php';
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /** @test */
    public function test_it_should_receive_ifsnop_mysqldump()
    {
        $dumperFactory = new DumperFactory();

        $connection = Arr::get($this->app['config'], 'database.connections.mysql');
        $settings = [];
        $pdoAttributes = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
        ];

        $this->assertInstanceOf(Mysqldump::class, $dumperFactory->create($connection, $settings, $pdoAttributes));
    }
}
