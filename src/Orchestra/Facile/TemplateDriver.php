<?php namespace Orchestra\Facile;

use RuntimeException,
	Illuminate\Support\Facades\Input,
	Illuminate\Support\Facades\Response,
	Illuminate\Support\Facades\View;

abstract class TemplateDriver {

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $formats = array('html');

	/**
	 * Default format
	 *
	 * @var string
	 */
	protected $defaultFormat = 'html';

	/**
	 * Detect current format.
	 *
	 * @access public
	 * @return string
	 */
	public function format()
	{
		return Input::get('format', $this->defaultFormat);
	}

	/**
	 * Compose requested format.
	 *
	 * @access public
	 * @return mixedd
	 */
	public function compose($format, $compose = array())
	{
		if ( ! in_array($format, $this->formats))
		{
			return call_user_func(array($this, "composeError"), null, null, 406);
		}
		elseif ( ! method_exists($this, 'compose'.ucwords($format)))
		{
			throw new RuntimeException("Call to undefine method [compose_{$format}].");
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
	 * @access public 	
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer 	$status
	 * @return Response  
	 */
	public function composeError($view, $data = array(), $status = 404)
	{
		$view = "{$status} Error";

		if (View::exists("error.{$status}")) $view = View::make("error.{$status}");

		return Response::make($view, $status);
	}
}