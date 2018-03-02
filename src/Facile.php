<?php

namespace Orchestra\Facile;

use Illuminate\Contracts\Support\Renderable;

class Facile implements Renderable
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Factory instance.
     *
     * @var \Orchestra\Facile\Factory
     */
    protected $factory;

    /**
     * Template instance.
     *
     * @var string
     */
    protected $parser = Template\Simple::class;

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
    protected $data = [
        'view' => null,
        'data' => [],
        'on' => [
            'csv' => ['only' => null, 'except' => null, 'uses' => 'data'],
            'html' => ['only' => null, 'except' => null],
            'json' => ['only' => null, 'except' => null],
            'xml' => ['only' => null, 'except' => null, 'root' => null],
        ],
        'status' => 200,
    ];

    /**
     * Construct a new Response instance.
     *
     * @param  \Orchestra\Facile\Factory  $factory
     * @param  string  $parser
     * @param  array  $data
     * @param  string  $format
     */
    public function __construct(Factory $factory, $parser, array $data = [], $format = null)
    {
        $this->factory = $factory;
        $this->data = array_merge($this->data, $data);
        $this->setFormat($format);

        $this->parser($parser);
    }

    /**
     * Nest a view to Facile.
     *
     * @param  string  $view
     *
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
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return $this
     */
    public function with($key, $value = null)
    {
        $data = is_array($key) ? $key : [$key => $value];

        $this->data['data'] = array_merge($this->data['data'], $data);

        return $this;
    }

    /**
     * Setup on format configuration.
     *
     * @param  string  $type
     * @param  array   $config
     *
     * @return $this
     */
    public function when($type, array $config = [])
    {
        if (! isset($this->data['on'][$type])) {
            $this->data['on'][$type] = [];
        }

        $this->data['on'][$type] = array_merge($this->data['on'][$type], $config);

        return $this;
    }

    /**
     * Set HTTP status to Facile.
     *
     * @param  int  $status
     *
     * @return $this
     */
    public function status($status = 200)
    {
        $this->data['status'] = $status;

        return $this;
    }

    /**
     * Set a parser for Facile.
     *
     * @param  mixed  $name
     *
     * @return $this
     */
    public function parser($name)
    {
        $this->parser = $name;

        return $this;
    }

    /**
     * Set a parser for Facile.
     *
     * @param  mixed  $name
     *
     * @return $this
     */
    public function template($name)
    {
        return $this->parser($name);
    }

    /**
     * Get or set facile format.
     *
     * @param  string  $format
     * @param  array   $config
     *
     * @return $this
     */
    public function format($format = null, array $config = [])
    {
        if (! is_null($format) && ! empty($format)) {
            $this->setFormat($format);

            if (! empty($config)) {
                $this->when($format, $config);
            }
        }

        return $this;
    }

    /**
     * Set Output Format.
     *
     * @param  string|null  $format
     *
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
     * @return string|null
     */
    public function getFormat()
    {
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

        return $content instanceof Renderable
                    ? $content->render()
                    : $content;
    }

    /**
     * Render facile by selected format.
     *
     * @return mixed
     */
    public function render()
    {
        return $this->factory->resolve(
            $this->parser,
            $this->getFormat(),
            $this->data,
            'compose'
        );
    }

    /**
     * Render facile by selected format.
     *
     * @return mixed
     */
    public function stream()
    {
        return $this->factory->resolve(
            $this->parser,
            $this->getFormat(),
            $this->data,
            'stream'
        );
    }
}
