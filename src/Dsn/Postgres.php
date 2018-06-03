<?php

namespace Recca0120\DbExporter\Dsn;

class Postgres extends Dsn
{
    public function getDsn()
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
