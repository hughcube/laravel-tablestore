<?php

namespace HughCube\TableStore;

use Illuminate\Support\Facades\DB;
use Illuminate\Queue\QueueServiceProvider as BaseQueueServiceProvider;
use HughCube\TableStore\Queue\Failed\MongoFailedJobProvider;

class QueueServiceProvider extends BaseQueueServiceProvider
{
    /**
     * @inheritdoc
     */
    protected function registerFailedJobServices()
    {
        // Add compatible queue failer if tableStore is configured.
        if (DB::connection(config('queue.failed.database'))->getDriverName() == 'tableStore') {
            $this->app->singleton('queue.failer', function ($app) {
                return new MongoFailedJobProvider($app['db'], config('queue.failed.database'), config('queue.failed.table'));
            });
        } else {
            parent::registerFailedJobServices();
        }
    }
}
