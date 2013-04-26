<?php namespace Orchestra\Facile\Template;

use InvalidArgumentException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class Base extends Driver {

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $formats = array('html', 'json');

	/**
	 * Default format
	 *
	 * @var string
	 */
	protected $defaultFormat = 'html';

	/**
	 * Compose HTML
	 *
	 * @access public
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer  $status
	 * @return string
	 */
	public function composeHtml($view = null, $data = array(), $status = 200)
	{
		if ( ! isset($view))
		{
			throw new InvalidArgumentException("Missing [\$view].");
		}

		if ( ! ($view instanceof View)) $view = View::make($view);

		return Response::make($view->with($data), $status);
	}

	/**
	 * Compose json
	 *
	 * @access public
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer  $status
	 * @return string
	 */
	public function composeJson($view, $data = array(), $status = 200)
	{
		$data = array_map(array($this, 'transform'), $data);

		return Response::json($data, $status);
	}
}
