<?php namespace Orchestra\Facile;

use Illuminate\Support\ServiceProvider;
use Orchestra\Facile\Template\Base;

class FacileServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('orchestra.facile', function ($app) {
            $env = new Environment($app['request']);

            $env->template('default', function () use ($app) {
                return new Base($app['view']);
            });

            return $env;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('orchestra.facile');
    }
}
