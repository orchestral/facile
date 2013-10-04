<?php namespace Orchestra\Facile\Template;

use RuntimeException;
use Illuminate\Container\Container;
use Illuminate\Http\Response as IlluminateResponse;
use Orchestra\Facile\Transformable;

abstract class Driver {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app = null;

	/**
	 * Transformable instance.
	 *
	 * @var \Orchestra\Facile\Transformable
	 */
	protected $transformable = null;

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $formats = array('html');

	/**
	 * Default format.
	 *
	 * @var string
	 */
	protected $defaultFormat = 'html';

	/**
	 * Construct a new Facile service.
	 * 
	 * @param  \Illuminate\Container\Container  $app
	 * @param  \Orchestra\Facile\Transformable  $transformable
	 */
	public function __construct(Container $app, Transformable $tranformable = null) 
	{
		$this->setContainer($app);
		$this->transformable = $tranformable ?: new Transformable;
	}

	/**
	 * Get app container.
	 *
	 * @return \Illuminate\Container\Container
	 */
	public function getContainer()
	{
		return $this->app;
	}


	/**
	 * Set app container.
	 * 
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function setContainer($app)
	{
		$this->app = $app;
	}

	/**
	 * Detect current format.
	 *
	 * @return string
	 */
	public function format()
	{
		return $this->app['request']->format($this->defaultFormat);
	}

	/**
	 * Compose requested format.
	 *
	 * @param  string   $format
	 * @param  array    $compose
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public function compose($format, $compose = array())
	{
		if ( ! in_array($format, $this->formats))
		{
			return $this->composeError(null, null, 406);
		}
		elseif ( ! method_exists($this, 'compose'.ucwords($format)))
		{
			throw new RuntimeException("Call to undefine method [compose".ucwords($format)."].");
		}

		return call_user_func(
			array($this, 'compose'.ucwords($format)), 
			$compose['view'], 
			$compose['data'], 
			$compose['status']
		);
	}

	/**
	 * Compose an error template.
	 *
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer 	$status
	 * @return \Illuminate\Http\Response  
	 */
	public function composeError($view, $data = array(), $status = 404)
	{
		$engine = $this->app['view'];

		$view = "{$status} Error";

		if ($engine->exists("error.{$status}")) 
		{
			$view = $engine->make("error.{$status}", $data);
		}

		return new IlluminateResponse($view, $status);
	}

	/**
	 * Transform given data.
	 *
	 * @param  array    $data
	 * @return array
	 */
	public function transform($data)
	{
		return $this->transformable->run($data);
	}
}
