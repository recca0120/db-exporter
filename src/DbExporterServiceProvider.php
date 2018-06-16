<?php

namespace Recca0120\DbExporter;

use Illuminate\Support\ServiceProvider;
use Recca0120\DbExporter\Console\Commands\DbExport;
use Recca0120\DbExporter\Dsn\Factory as DsnFactory;

class DbExporterServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/db-exporter.php', 'db-exporter');

        $this->app->singleton(DbExporterManager::class, function ($app) {
            return new DbExporterManager(
                $app,
                new DumperFactory(new DsnFactory()),
                $app['filesystem']
            );
        });

        $this->commands([DbExport::class]);
    }
}
