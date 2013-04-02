<?php namespace Orchestra\Facile;

use InvalidArgumentException,
	Illuminate\Support\Facades\Response as ResponseFacade,
	Illuminate\Support\Facades\View;

class Template extends TemplateDriver {

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

		return ResponseFacade::make($view->with($data), $status);
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
	public function composeJson($view = null, $data = array(), $status = 200)
	{
		$data = array_map(array($this, 'transform'), $data);

		return ResponseFacade::json($data, $status);
	}
}