<?php namespace Orchestra\Facile;

use Illuminate\Support\Facades\Input;

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
}