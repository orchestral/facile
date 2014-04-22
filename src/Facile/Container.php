<?php namespace Orchestra\Facile;

use Illuminate\Support\Contracts\RenderableInterface;
use Orchestra\Support\Str;

class Container implements RenderableInterface
{
    /**
     * Environment instance.
     *
     * @var Environment
     */
    protected $env;

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
     * @param  Environment $env
     * @param  string      $template
     * @param  array       $data
     * @param  string      $format
     */
    public function __construct(Environment $env, $template, array $data = array(), $format = null)
    {
        $this->env    = $env;
        $this->data   = array_merge($this->data, $data);
        $this->format = $format;

        $this->template($template);
    }

    /**
     * Nest a view to Facile.
     *
     * @param  string   $view
     * @return Container
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
     * @return Container
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
     * @return Container
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
     * @return Container
     */
    public function on($type, array $config = array())
    {
        return $this->when($type, $config);
    }

    /**
     * Set HTTP status to Facile.
     *
     * @param  integer  $status
     * @return Container
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
     * @return Container
     */
    public function template($name)
    {
        if ($name instanceof Template\Driver) {
            $template = $name;
            $name = sprintf('template-%d-%s', time(), Str::random());

            $this->env->template($name, $template);
        }

        $this->template = $name;

        return $this;
    }

    /**
     * Get or set facile format.
     *
     * @param  string   $format
     * @return Container
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
     * @return Container
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
            $this->format = $this->env->getRequestFormat($this->template);
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
        $renderer = $this->env->getTemplate($this->template);

        return $renderer->compose($this->getFormat(), $this->data);
    }
}
