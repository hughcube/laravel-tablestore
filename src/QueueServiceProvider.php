<?php

namespace HughCube\TableStore;

use Illuminate\Support\Facades\DB;
use Illuminate\Queue\QueueServiceProvider as BaseQueueServiceProvider;
use HughCube\TableStore\Queue\Failed\FailedJobProvider;

class QueueServiceProvider extends BaseQueueServiceProvider
{
    /**
     * @inheritdoc
     */
    protected function registerFailedJobServices()
    {
        // Add compatible queue failer if mongodb is configured.
        if (DB::connection(config('queue.failed.database'))->getDriverName() == 'mongodb') {
            $this->app->singleton('queue.failer', function ($app) {
                return new FailedJobProvider($app['db'], config('queue.failed.database'), config('queue.failed.table'));
            });
        } else {
            parent::registerFailedJobServices();
        }
    }
}
