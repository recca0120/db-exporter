<?php

namespace Recca0120\DbExporter\Dsn;

class MySql extends Dsn
{
    public function getDsn()
    {
        return $this->hasSocket() === true
            ? $this->getSocketDsn()
            : $this->getHostDsn();
    }

    private function hasSocket()
    {
        return isset($this->config['unix_socket']) && ! empty($this->config['unix_socket']);
    }

    private function getSocketDsn()
    {
        return "mysql:unix_socket={$this->config['unix_socket']};dbname={$this->config['database']}";
    }

    private function getHostDsn()
    {
        return isset($this->config['port'])
            ? "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']}"
            : "mysql:host={$this->config['host']};dbname={$this->config['database']}";
    }
}
