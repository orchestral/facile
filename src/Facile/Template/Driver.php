<?php namespace Orchestra\Facile\Template;

use RuntimeException;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\View\Factory;
use Orchestra\Facile\Transformable;

abstract class Driver
{
    /**
     * View instance.
     *
     * @var \Illuminate\View\Environment
     */
    protected $view;

    /**
     * Transformable instance.
     *
     * @var \Orchestra\Facile\Transformable
     */
    protected $transformable;

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
     * Construct a new Facile service.
     *
     * @param  \Illuminate\View\Factory        $view
     * @param  \Orchestra\Facile\Transformable $transformable
     */
    public function __construct(Factory $view, Transformable $tranformable = null)
    {
        $this->view = $view;
        $this->transformable = $tranformable ?: new Transformable;
    }

    /**
     * Get default format.
     *
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * Compose requested format.
     *
     * @param  string   $format
     * @param  array    $compose
     * @return mixed
     * @throws \RuntimeException
     */
    public function compose($format, array $compose = array())
    {
        if (! in_array($format, $this->formats)) {
            return $this->composeError(null, array(), 406);
        } elseif (! method_exists($this, 'compose'.ucwords($format))) {
            throw new RuntimeException("Call to undefine method [compose".ucwords($format)."].");
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
     * @param  integer  $status
     * @return \Illuminate\Http\Response
     */
    public function composeError($view, array $data = array(), $status = 404)
    {
        $engine = $this->view;

        $view = "{$status} Error";

        if ($engine->exists("error.{$status}")) {
            $view = $engine->make("error.{$status}", $data);
        }

        return new IlluminateResponse($view, $status);
    }

    /**
     * Transform given data.
     *
     * @param  mixed    $data
     * @return array
     */
    public function transform($data)
    {
        return $this->transformable->run($data);
    }
}
