<?php namespace Orchestra\Facile\Template;

use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\View\View;

class Base extends Driver {

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $formats = array('html', 'json');

	/**
	 * Default format.
	 *
	 * @var string
	 */
	protected $defaultFormat = 'html';

	/**
	 * Compose HTML.
	 *
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer  $status
	 * @return \Illuminate\Http\Response
	 */
	public function composeHtml($view = null, array $data = array(), $status = 200)
	{
		if ( ! isset($view))
		{
			throw new InvalidArgumentException("Missing [\$view].");
		}

		if ( ! ($view instanceof View)) $view = $this->app['view']->make($view);

		return new IlluminateResponse($view->with($data), $status);
	}

	/**
	 * Compose JSON.
	 *
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer  $status
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function composeJson($view, array $data = array(), $status = 200)
	{
		unset($view);

		$data = array_map(array($this, 'transform'), $data);

		return new JsonResponse($data, $status);
	}
}
