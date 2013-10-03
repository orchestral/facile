<?php namespace Orchestra\Facile\Template;

use RuntimeException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Orchestra\Facile\Transformable;

abstract class Driver {

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
	 * Detect current format.
	 *
	 * @return string
	 */
	public function format()
	{
		return Request::format($this->defaultFormat);
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
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer 	$status
	 * @return \Illuminate\Http\Response  
	 */
	public function composeError($view, $data = array(), $status = 404)
	{
		$view = "{$status} Error";

		if (View::exists("error.{$status}")) $view = View::make("error.{$status}", $data);

		return Response::make($view, $status);
	}

	/**
	 * Transform given data.
	 *
	 * @param  array    $data
	 * @return array
	 */
	public function transform($data)
	{
		return with(new Transformable)->run($data);
	}
}
