<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\Manager;

class DbExporterManager extends Manager
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function getDefaultDriver()
    {
    }
}
