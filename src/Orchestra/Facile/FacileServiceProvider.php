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
		$this->registerEnvironment();
		$this->registerTemplate();
	}

	/**
	 * Register Facile environment.
	 *
	 * @return void
	 */
	protected function registerEnvironment()
	{
		$this->app['orchestra.facile'] = $this->app->share(function()
		{
			return new Environment;
		});
	}

	/**
	 * Register Facile environment.
	 *
	 * @return void
	 */
	protected function registerTemplate()
	{
		$this->app['orchestra.facile']->template('default', new Template\Base);
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
