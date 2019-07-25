<?php

namespace Orchestra\Facile;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;

class FacileServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.facile', static function (Application $app) {
            $factory = new Factory($app, $app->make('request'));
            $template = new Template\Simple($app->make('view'), new Transformable());

            $factory->name('default', $template);
            $factory->name('simple', $template);

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
