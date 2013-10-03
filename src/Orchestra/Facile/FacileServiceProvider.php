<?php namespace Orchestra\Facile;

use Illuminate\Support\ServiceProvider;

class FacileServiceProvider extends ServiceProvider {

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
		$this->app['orchestra.facile'] = $this->app->share(function($app)
		{
			$env = new Environment($app);
			$env->template('default', new Template\Base);

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
