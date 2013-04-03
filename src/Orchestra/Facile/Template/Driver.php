<?php namespace Orchestra\Facile\Template;

use RuntimeException,
	Illuminate\Database\Eloquent\Model,
	Illuminate\Support\Contracts\ArrayableInterface,
	Illuminate\Support\Contracts\RenderableInterface,
	Illuminate\Support\Facades\Input,
	Illuminate\Pagination\Paginator,
	Illuminate\Support\Facades\Response as ResponseFacade,
	Illuminate\Support\Facades\View;

abstract class Driver {

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

		return ResponseFacade::make($view, $status);
	}

	/**
	 * Transform given data
	 *
	 * @access public
	 * @param  array    $data
	 * @return array
	 */
	public function transform($item)
	{
		switch (true)
		{
			case ($item instanceof Eloquent) :
				// passthru
			case ($item instanceof ArrayableInterface) :
				return $item->toArray();

			case ($item instanceof RenderableInterface) :
				return e($item->render());

			case ($item instanceof Paginator) :
				$results = $item->getItems();

				is_array($results) and $results = array_map(array($this, 'transform'), $results);

				return array(
					'results' => $results,
					'links'   => e($item->links()),
				);

						
			case (is_array($item)) :
				return array_map(array($this, 'transform'), $item);

			default :
				return $item;
		}
	}
}