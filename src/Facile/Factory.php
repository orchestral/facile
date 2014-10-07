<?php namespace Orchestra\Facile;

use RuntimeException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Orchestra\Facile\Template\Driver as Template;

class Factory
{
    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * List of templates.
     *
     * @var array
     */
    protected $templates = array();

    /**
     * Construct a new Facile service.
     *
     * @param  \Illuminate\Http\Request    $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
     * @return Container
     */
    public function make($name, array $data = array(), $format = null)
    {
        return new Container($this, $name, $data, $format);
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
     * @return Container
     */
    public function view($view, array $data = array())
    {
        return with(new Container($this, 'default'))
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
     * @return Container
     */
    public function with($data)
    {
        $data = func_get_args();
        $container = new Container($this, 'default');

        return call_user_func_array(array($container, 'with'), $data);
    }

    /**
     * Register a template.
     *
     * @param  string                                       $name
     * @param  \Orchestra\Facile\Template\Driver|\Closure   $template
     * @return void
     * @throws \RuntimeException if `$template` not instanceof
     *                           `Orchestra\Facile\Template\Driver`.
     */
    public function template($name, $template)
    {
        $resolve = value($template);

        if (! ($resolve instanceof Template)) {
            throw new RuntimeException(
                "Expected \$template to be instanceof Orchestra\Facile\Template\Driver."
            );
        }

        $this->templates[$name] = $resolve;
    }

    /**
     * Get request format.
     *
     * @param  string  $name
     * @return string
     */
    public function getRequestFormat($name)
    {
        return $this->request->format(
            $this->getTemplate($name)->getDefaultFormat()
        );
    }

    /**
     * Get the template.
     *
     * @param  string   $name
     * @return \Orchestra\Facile\Template\Driver
     * @throws \InvalidArgumentException if template is not defined.
     */
    public function getTemplate($name)
    {
        if (! isset($this->templates[$name])) {
            throw new InvalidArgumentException("Template [{$name}] is not available.");
        }

        return $this->templates[$name];
    }
}
