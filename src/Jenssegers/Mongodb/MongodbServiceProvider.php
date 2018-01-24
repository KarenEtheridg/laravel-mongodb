<?php namespace Jenssegers\Mongodb;

use Illuminate\Support\ServiceProvider;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Queue\MongoConnector;

class MongodbServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);

        $s = explode('.', \Illuminate\Foundation\Application::VERSION);
        define('SHOULD_RETURN_COLLECTION', (10 * $s[0] + $s[1]) >= 53);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Add database driver.
        $this->app->resolving('db', function ($db) {
            $db->extend('mongodb', function ($config) {
                return new Connection($config);
            });
        });

        // Add connector for queue support.
        $this->app->resolving('queue', function ($queue) {
            $queue->addConnector('mongodb', function () {
                return new MongoConnector($this->app['db']);
            });
        });
    }
}
