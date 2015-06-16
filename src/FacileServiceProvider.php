<?php namespace Orchestra\Facile;

use Orchestra\Facile\Template\Simple;
use Illuminate\Support\ServiceProvider;

class FacileServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.facile', function ($app) {
            $factory = new Factory($app->make('request'));

            $factory->template('default', function () use ($app) {
                return new Simple($app->make('view'));
            });

            return $factory;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['orchestra.facile'];
    }
}
