<?php namespace Orchestra\Facile;

use InvalidArgumentException,
	RuntimeException;

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
	 * Create a new Facile instance.
	 *
	 * <code>
	 * 		// Using provided Facile.
	 * 		
	 * 		$users  = User::paginate(30);
	 * 		$facile = Facile::make('default', array(
	 * 			'view'   => 'home.index',
	 * 			'data'   => array(
	 * 				'users' => $users,
	 * 			),
	 * 			'status' => 200,
	 * 		));
	 *
	 * 		// Alternatively
	 * 		$facile = Facile::make('default')
	 * 			->view('home.index')
	 * 			->with(array(
	 * 				'users' => $users,
	 * 			))
	 * 			->status(200)
	 * 			->template(new Orchestra\Facile\Template)
	 * 			->format('html');
	 * </code>
	 *
	 * @static
	 * @access public			
	 * @param  string   $name   Name of template
	 * @param  array    $data
	 * @param  string   $format
	 * @return self
	 */
	public function make($name, $data = array(), $format = null)
	{
		return new Response($this, $this->get($name), $data, $format);
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

	/**
	 * Get the template.
	 *
	 * @access public
	 * @param  string   $name
	 * @return Orchestra\Facile\TemplateDriver
	 * @throws InvalidArgumentException     If template is not defined.
	 */
	public function get($name)
	{
		if ( ! isset($this->templates[$name]))
		{
			throw new InvalidArgumentException(
				"Template [{$name}] is not available."
			);
		}

		return $this->templates[$name];
	}
}