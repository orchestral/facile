<?php namespace Orchestra\Facile;

use Orchestra\Facile\Template\Api;
use Orchestra\Facile\Template\Simple;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

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
        $this->app->singleton('orchestra.facile', function (Application $app) {
            $factory   = new Factory($app, $app->make('request'));
            $view      = $app->make('view');
            $transform = new Transformable();

            $factory->name('api', new Api($view, $transform));
            $factory->name('default', new Simple($view, $transform));

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
