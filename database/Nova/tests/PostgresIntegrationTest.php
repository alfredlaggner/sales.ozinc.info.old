<?php

namespace Laravel\Nova\Tests;

use Illuminate\Foundation\Application;

abstract class PostgresIntegrationTest extends IntegrationTest
{
    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'pgsql',
            '--realpath' => realpath(__DIR__.'/Migrations'),
        ]);
    }

    /**
     * Define environment.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'pgsql');

        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => 5432,
            'database' => 'nova_test',
            'username' => $_ENV['POSTGRES_USERNAME'] ?? 'taylor',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ]);
    }
}
