<?php

namespace CatWrench\DbLogs;

use Illuminate\Support\ServiceProvider;

class LogProvider extends ServiceProvider
{

    public function register()
    {
        // register to laravel service container as singleton
        $this->app->singleton('dblogs', function () {
            return new Log;
        });
    }

    public function boot()
    {
        // migrations
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }
}
