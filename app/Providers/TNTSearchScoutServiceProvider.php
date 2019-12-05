<?php
namespace App\Providers;

use TeamTNT\Scout\Console\ImportCommand;
use Laravel\Scout\EngineManager;
use TeamTNT\TNTSearch\TNTSearch;
use TeamTNT\Scout\Engines\TNTSearchEngine;

class TNTSearchScoutServiceProvider extends \TeamTNT\Scout\TNTSearchScoutServiceProvider
{
    public function boot()
    {
        $this->app[EngineManager::class]->extend('tntsearch', function ($app) {
            $tnt = new TNTSearch();

            $driver = config('database.default');
            $config = config('scout.tntsearch') + config("database.connections.{$driver}");

            $tnt->loadConfig($config);
            $tnt->setDatabaseHandle(app('db')->connection()->getPdo());

            $this->setFuzziness($tnt);
            $this->setAsYouType($tnt);

            return new TNTSearchEngine($tnt);
        });

        //if ($this->app->runningInConsole()) {
        //    $this->commands([
        //        ImportCommand::class,
        //    ]);
        //}

        // To allow us run commands if we're not running in the console
        $this->commands([
            ImportCommand::class,
        ]);
    }
}

