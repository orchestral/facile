<?php namespace Orchestra\Facile;

use RuntimeException;

class Environment {

	/**
	 * List of templates.
	 *
	 * @var array
	 */
	protected $templates = array();
	
	/**
	 * Construct a new Facile instance.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() 
	{
		$this->templates = array();
	}

	/**
	 * Register a template.
	 *
	 * @access public							
	 * @param  string                           $name
	 * @param  Orchestra\Facile\TemplateDriver  $callback
	 * @return void
	 * @throws RuntimeException     If $callback not instanceof 
	 *                              Orchestra\Facile\TemplateDriver
	 */
	public function template($name, $template)
	{
		$resolve = value($template);

		if ( ! ($resolve instanceof TemplateDriver))
		{
			throw new RuntimeException(
				"Expected \$template to be instanceof Orchestra\Facile\Driver."
			);
		}

		$this->templates[$name] = $resolve;
	}
}