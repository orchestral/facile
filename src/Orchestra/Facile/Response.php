<?php namespace Orchestra\Facile;

use Illuminate\Support\Contracts\RenderableInterface;
use Orchestra\Support\Str;

class Response implements RenderableInterface
{
    /**
     * Factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Template instance.
     *
     * @var string
     */
    protected $template = 'default';

    /**
     * View format.
     *
     * @var string
     */
    protected $format = null;

    /**
     * View data.
     *
     * @var array
     */
    protected $data = array(
        'view'   => null,
        'data'   => array(),
        'status' => 200,
    );

    /**
     * Construct a new Response instance.
     *
     * @param  Factory $factory
     * @param  string  $template
     * @param  array   $data
     * @param  string  $format
     */
    public function __construct(Factory $factory, $template, array $data = array(), $format = null)
    {
        $this->factory = $factory;
        $this->data    = array_merge($this->data, $data);
        $this->format  = $format;

        $this->template($template);
    }

    /**
     * Nest a view to Facile.
     *
     * @param  string   $view
     * @return Response
     */
    public function view($view)
    {
        $this->data['view'] = $view;

        return $this;
    }

    /**
     * Nest a data or dataset to Facile.
     *
     * @param  mixed    $key
     * @param  mixed    $value
     * @return Response
     */
    public function with($key, $value = null)
    {
        $data = is_array($key) ? $key : array($key => $value);

        $this->data['data'] = array_merge($this->data['data'], $data);

        return $this;
    }

    /**
     * Set HTTP status to Facile.
     *
     * @param  integer  $status
     * @return Response
     */
    public function status($status = 200)
    {
        $this->data['status'] = $status;

        return $this;
    }

    /**
     * Set a template for Facile.
     *
     * @param  mixed    $name
     * @return Response
     */
    public function template($name)
    {
        if ($name instanceof Template\Driver) {
            $template = $name;
            $name = sprintf('template-%d-%s', time(), Str::random());

            $this->factory->template($name, $template);
        }

        $this->template = $name;

        return $this;
    }

    /**
     * Get or set facile format.
     *
     * @param  string   $format
     * @return Response
     */
    public function format($format = null)
    {
        if (! is_null($format) && ! empty($format)) {
            $this->setFormat($format);
        }

        return $this;
    }

    /**
     * Set Output Format.
     *
     * @param  string   $format
     * @return Response
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get Output Format.
     *
     * @return string
     */
    public function getFormat()
    {
        if (is_null($this->format)) {
            $this->format = $this->factory->getRequestFormat($this->template);
        }

        return $this->format;
    }

    /**
     * Render facile by selected format.
     *
     * @return string
     */
    public function __toString()
    {
        $content = $this->render();

        if ($content instanceof RenderableInterface) {
            return $content->render();
        }

        return $content;
    }

    /**
     * Render facile by selected format.
     *
     * @return mixed
     */
    public function render()
    {
        $renderer = $this->factory->getTemplate($this->template);

        return $renderer->compose($this->getFormat(), $this->data);
    }
}
