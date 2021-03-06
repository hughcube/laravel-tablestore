<?php

namespace HughCube\TableStore;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use HughCube\TableStore\Eloquent\Model;
use HughCube\TableStore\Queue\MongoConnector;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Add database driver.
        $this->app->resolving('db', function ($db) {
            $db->extend('tableStore', function ($config, $name) {
                $config['name'] = $name;
                return new Connection($config);
            });
        });

        // Add connector for queue support.
        $this->app->resolving('queue', function ($queue) {
            $queue->addConnector('tableStore', function () {
                return new MongoConnector($this->app['db']);
            });
        });
    }
}
