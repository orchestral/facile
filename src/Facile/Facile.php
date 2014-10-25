<?php namespace Orchestra\Facile;

use Orchestra\Support\Str;
use Orchestra\Facile\Template\Template;
use Illuminate\Contracts\Support\Renderable;

class Facile implements Renderable
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
        'on'     => array(
            'html' => array('only' => null, 'except' => null),
            'json' => array('only' => null, 'except' => null),
            'csv'  => array('uses' => 'data'),
        ),
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
     * @return $this
     */
    public function view($view)
    {
        $this->data['view'] = $view;

        return $this;
    }

    /**
     * Nest a data or data-set to Facile.
     *
     * @param  mixed    $key
     * @param  mixed    $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        $data = is_array($key) ? $key : array($key => $value);

        $this->data['data'] = array_merge($this->data['data'], $data);

        return $this;
    }

    /**
     * Setup on format configuration.
     *
     * @param  string  $type
     * @param  array   $config
     * @return $this
     */
    public function when($type, array $config = array())
    {
        if (! isset($this->data['on'][$type])) {
            $this->data['on'][$type] = array();
        }

        $this->data['on'][$type] = array_merge($this->data['on'][$type], $config);

        return $this;
    }

    /**
     * Setup on format configuration.
     *
     * @deprecated
     * @param  string  $type
     * @param  array   $config
     * @return $this
     * @see    self::when()
     */
    public function on($type, array $config = array())
    {
        return $this->when($type, $config);
    }

    /**
     * Set HTTP status to Facile.
     *
     * @param  integer  $status
     * @return $this
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
     * @return $this
     */
    public function template($name)
    {
        if ($name instanceof Template) {
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
     * @param  array    $config
     * @return $this
     */
    public function format($format = null, array $config = array())
    {
        if (! is_null($format) && ! empty($format)) {
            $this->setFormat($format);

            ! empty($config) && $this->when($format, $config);
        }

        return $this;
    }

    /**
     * Set Output Format.
     *
     * @param  string   $format
     * @return $this
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

        if ($content instanceof Renderable) {
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
