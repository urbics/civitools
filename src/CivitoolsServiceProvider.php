<?php

namespace Urbics\Civitools;

use Illuminate\Support\ServiceProvider;
use Urbics\Civitools\Console\Installers\Environment;

class CivitoolsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        (new Environment())->setEnvironment();
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\CiviDbBackup::class,
                Console\Commands\CiviMakeDb::class,
                Console\Commands\CiviMakeMigration::class,
                Console\Commands\CiviMakeModel::class,
                Console\Commands\CiviMakeSeeder::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
