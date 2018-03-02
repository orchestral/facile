<?php

namespace Orchestra\Facile;

use Illuminate\Http\Request;
use InvalidArgumentException;
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
     * List of parsers.
     *
     * @var array
     */
    protected $parsers = [];

    /**
     * Construct a new Facile service.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->parsers = [];
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
     *          ->parser(Orchestra\Facile\Template\Parser::class)
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
        return (new Facile($this, 'simple'))
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
     *          ->parser(Orchestra\Facile\Template\Parser::class)
     *          ->format('html');
     * </code>
     *
     * @param  mixed  $data
     *
     * @return \Orchestra\Facile\Facile
     */
    public function with($data)
    {
        return (new Facile($this, 'simple'))
                        ->with(...func_get_args());
    }

    /**
     * Register a named parser.
     *
     * @param  string  $name
     * @param  string|\Orchestra\Facile\Template\Parser  $parser
     *
     * @return void
     */
    public function name($name, $parser)
    {
        if (is_string($parser) && (class_exists($parser, false) || $this->app->bound($parser))) {
            $parser = $this->app->make($parser);
        }

        $this->parsers[$name] = $parser;
    }

    /**
     * Get request format.
     *
     * @param  \Orchestra\Facile\Template\Parser  $parser
     *
     * @return string
     */
    protected function getPrefersFrom($parser)
    {
        return $this->request->prefers(
            $parser->getSupportedFormats()
        );
    }

    /**
     * Resolve parser from factory.
     *
     * @return \Orchestra\Facile\Template\Parser
     */
    public function resolve($name, $format, array $data, $method = 'compose')
    {
        $parser = $this->parse($name);
        $format = $format ?? $this->getPrefersFrom($parser);

        return $parser->compose($format, $data, $method);
    }

    /**
     * Get the parser.
     *
     * @param  string|\Orchestra\Facile\Template\Parser  $name
     *
     * @throws \InvalidArgumentException if parser is not defined
     *
     * @return \Orchestra\Facile\Template\Parser
     */
    public function parse($name)
    {
        if ($name instanceof Template\Parser) {
            return $name;
        } elseif (! isset($this->parsers[$name]) && is_string($name)) {
            $this->name($name, $name);
        }

        $parser = $this->parsers[$name] ?? $name;

        if (! $parser instanceof Template\Parser) {
            throw new InvalidArgumentException(
                "Expected \$parser to be instanceof Orchestra\Facile\Template\Parser."
            );
        }

        return $parser;
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }
}
