<?php

namespace Orchestra\Facile;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Orchestra\Facile\Template\Template;
use Illuminate\Contracts\Foundation\Application;

class Factory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

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
    protected $names = [];

    /**
     * Construct a new Facile service.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app     = $app;
        $this->request = $request;
        $this->names   = [];
    }

    /**
     * Create a new Facile instance.
     *
     * <code>
     *      // Using provided facade for Facile.
     *
     *      $users  = User::paginate(30);
     *      $facile = Facile::make('simple', array(
     *          'view'   => 'home.index',
     *          'data'   => array(
     *              'users' => $users,
     *          ),
     *          'status' => 200,
     *      ));
     *
     *      // Alternatively
     *      $facile = Facile::make('simple')
     *          ->view('home.index')
     *          ->with(array(
     *              'users' => $users,
     *          ))
     *          ->status(200)
     *          ->format('html');
     * </code>
     *
     * @param  string  $name   Name of template
     * @param  array   $data
     * @param  string  $format
     *
     * @return \Orchestra\Facile\Facile
     */
    public function make($name, array $data = [], $format = null)
    {
        return new Facile($this, $name, $data, $format);
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
     *          ->template(Orchestra\Facile\Template\Template::class)
     *          ->format('html');
     * </code>
     *
     * @param  string  $view
     * @param  array   $data
     *
     * @return \Orchestra\Facile\Facile
     */
    public function view($view, array $data = [])
    {
        return (new Facile($this, 'simple'))->view($view)->with($data);
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
     *          ->template(Orchestra\Facile\Template\Template::class)
     *          ->format('html');
     * </code>
     *
     * @param  mixed  $data
     *
     * @return \Orchestra\Facile\Facile
     */
    public function with($data)
    {
        $data      = func_get_args();
        $container = new Facile($this, 'simple');

        return call_user_func([$container, 'with'], ...$data);
    }

    /**
     * Register a named template.
     *
     * @param  string  $name
     * @param  string|\Orchestra\Facile\Template\Template  $template
     *
     * @return void
     */
    public function name($name, $template)
    {
        $template = is_string($template) ? $this->app->make($template) : $template;

        $this->names[$name] = $template;
    }

    /**
     * Get request format.
     *
     * @param  string  $name
     *
     * @return string
     */
    public function getRequestFormat($name)
    {
        return $this->request->prefers(
            $this->getTemplate($name)->getSupportedFormats()
        );
    }

    /**
     * Get the template.
     *
     * @param  string|\Orchestra\Facile\Template\Template  $name
     *
     * @return \Orchestra\Facile\Template\Template
     *
     * @throws \InvalidArgumentException if template is not defined.
     */
    public function getTemplate($name)
    {
        if ($name instanceof Template) {
            return $name;
        } elseif (! isset($this->names[$name]) && is_string($name)) {
            $this->name($name, $name);
        }

        $template = isset($this->names[$name]) ? $this->names[$name] : $name;

        if (! $template instanceof Template) {
            throw new InvalidArgumentException(
                "Expected \$template to be instanceof Orchestra\Facile\Template\Template."
            );
        }

        return $template;
    }
}
