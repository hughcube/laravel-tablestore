<?php

namespace HughCube\TableStore;

use HughCube\TableStore\Eloquent\Model;
use HughCube\TableStore\Queue\Connector;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
//        if ($this->app instanceof LumenApplication) {
//            $this->app->configure('database');
//        }

        #Model::setConnectionResolver($this->app['db']);

        #Model::setEventDispatcher($this->app['events']);
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
        //$this->app->resolving('queue', function ($queue) {
        //    $queue->addConnector('tableStore', function () {
        //        return new Connector($this->app['db']);
        //    });
        //});
    }
}
