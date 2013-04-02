<?php namespace Orchestra\Facile;

use Illuminate\Support\ServiceProvider;

class FacileServiceProvider extends ServiceProvider {

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
	 * @access protected
	 * @return void
	 */
	protected function registerEnvironment()
	{
		$this->app['orchestra.facile'] = $this->app->share(function($app)
		{
			return new Environment;
		});
	}

	/**
	 * Register Facile environment.
	 *
	 * @access protected
	 * @return void
	 */
	protected function registerTemplate()
	{
		$this->app['orchestra.facile']->template('default', new Template);
	}
}