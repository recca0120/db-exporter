<?php

namespace Recca0120\DbExporter\Dsn;

use PDO;

class Postgres extends Dsn
{
    protected $pdoAttributes = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    public function toString()
    {
        $host = isset($this->config['host']) ? "host={$this->config['host']};" : '';

        $dsn = "pgsql:{$host}dbname={$this->config['database']}";

        if (isset($this->config['port'])) {
            $dsn .= ";port={$this->config['port']}";
        }

        return $this->addSslOptions($dsn);
    }

    private function addSslOptions($dsn)
    {
        foreach (['sslmode', 'sslcert', 'sslkey', 'sslrootcert'] as $option) {
            if (isset($this->config[$option])) {
                $dsn .= ";{$option}={$this->config[$option]}";
            }
        }

        return $dsn;
    }
}
