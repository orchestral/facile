<?php

namespace Orchestra\Facile;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FacileServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.facile', static function (Container $app) {
            return \tap(new Factory($app, $app->make('request')), static function ($factory) use ($app) {
                $template = new Template\Simple($app->make('view'), new Transformable());

                $factory->name('default', $template);
                $factory->name('simple', $template);
            });
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
