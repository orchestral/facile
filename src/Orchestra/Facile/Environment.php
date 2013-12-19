<?php namespace Orchestra\Facile;

use InvalidArgumentException;
use RuntimeException;
use Illuminate\Container\Container;

class Environment
{
    /**
     * List of templates.
     *
     * @var array
     */
    protected $templates = array();

    /**
     * Construct a new Facile service.
     */
    public function __construct()
    {
        $this->templates = array();
    }

    /**
     * Create a new Facile instance.
     *
     * <code>
     *      // Using provided facade for Facile.
     *
     *      $users  = User::paginate(30);
     *      $facile = Facile::make('default', array(
     *          'view'   => 'home.index',
     *          'data'   => array(
     *              'users' => $users,
     *          ),
     *          'status' => 200,
     *      ));
     *
     *      // Alternatively
     *      $facile = Facile::make('default')
     *          ->view('home.index')
     *          ->with(array(
     *              'users' => $users,
     *          ))
     *          ->status(200)
     *          ->template(new Orchestra\Facile\Template\Driver)
     *          ->format('html');
     * </code>
     *
     * @param  string   $name   Name of template
     * @param  array    $data
     * @param  string   $format
     * @return Response
     */
    public function make($name, array $data = array(), $format = null)
    {
        return new Response($this, $this->get($name), $data, $format);
    }

    /**
     * Create a new Facile instance helper via view.
     *
     * <code>
     *      // Using provided facade for Facile.
     *
     *      $users  = User::paginate(30);
     *      $facile = Facile::view('home.index', array(
     *              'users' => $users,
     *          ))
     *          ->status(200)
     *          ->template(new Orchestra\Facile\Template\Driver)
     *          ->format('html');
     * </code>
     *
     * @param  string   $view
     * @param  array    $data
     * @return Response
     */
    public function view($view, array $data = array())
    {
        return with(new Response($this, $this->get('default')))
            ->view($view)
            ->with($data);
    }

    /**
     * Create a new Facile instance helper via with.
     *
     * <code>
     *      // Using provided facade for Facile.
     *
     *      $users  = User::paginate(30);
     *      $facile = Facile::with(array(
     *              'users' => $users,
     *          ))
     *          ->view('home.index')
     *          ->status(200)
     *          ->template(new Orchestra\Facile\Template\Driver)
     *          ->format('html');
     * </code>
     *
     * @param  mixed    $data
     * @return Response
     */
    public function with($data)
    {
        $data     = func_get_args();
        $response = new Response($this, $this->get('default'));

        return call_user_func_array(array($response, 'with'), $data);
    }

    /**
     * Register a template.
     *
     * @param  string               $name
     * @param  Template\Driver      $template
     * @return void
     * @throws \RuntimeException if `$template` not instanceof
     *                           `Orchestra\Facile\Template\Driver`.
     */
    public function template($name, $template)
    {
        $resolve = value($template);

        if (! ($resolve instanceof Template\Driver)) {
            throw new RuntimeException(
                "Expected \$template to be instanceof Orchestra\Facile\Template\Driver."
            );
        }

        $this->templates[$name] = $resolve;
    }

    /**
     * Get the template.
     *
     * @param  string   $name
     * @return Template\Driver
     * @throws \InvalidArgumentException if template is not defined.
     */
    public function get($name)
    {
        if (! isset($this->templates[$name])) {
            throw new InvalidArgumentException("Template [{$name}] is not available.");
        }

        return $this->templates[$name];
    }
}
