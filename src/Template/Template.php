<?php

namespace Orchestra\Facile\Template;

use RuntimeException;
use Illuminate\Support\Arr;
use Orchestra\Facile\Transformable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response as IlluminateResponse;

abstract class Template
{
    /**
     * View instance.
     *
     * @var \Illuminate\Contracts\View\Factory
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
    protected $formats = ['html'];

    /**
     * Construct a new Facile service.
     *
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @param  \Orchestra\Facile\Transformable  $transformable
     */
    public function __construct(Factory $view, Transformable $transformable = null)
    {
        $this->view = $view;
        $this->transformable = $transformable ?: new Transformable();
    }

    /**
     * Compose requested format.
     *
     * @param  string  $format
     * @param  array   $compose
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function compose($format, array $compose = [])
    {
        if (! in_array($format, $this->formats)) {
            return $this->composeError(null, [], 406);
        } elseif (! method_exists($this, 'compose'.ucwords($format))) {
            throw new RuntimeException('Call to undefine method [compose'.ucwords($format).'].');
        }

        $config = $compose['on'][$format] ?? [];

        return $this->{'compose'.ucwords($format)}(
            $compose['view'],
            $this->prepareDataValue($config, $compose['data']),
            $compose['status'],
            $config
        );
    }

    /**
     * Compose an error template.
     *
     * @param  mixed  $view
     * @param  array  $data
     * @param  int    $status
     *
     * @return \Illuminate\Http\Response
     */
    public function composeError($view, array $data = [], $status = 404)
    {
        $engine = $this->view;
        $file = "errors.{$status}";
        $view = $engine->exists($file) ? $engine->make($file, $data) : "{$status} Error";

        return new IlluminateResponse($view, $status);
    }

    /**
     * Get supported format.
     *
     * @return array
     */
    public function getSupportedFormats()
    {
        return $this->formats;
    }

    /**
     * Transform given data.
     *
     * @param  mixed  $data
     *
     * @return array
     */
    public function transformToArray($data)
    {
        return $this->transformable->run($data);
    }

    /**
     * Prepare data to be seen to template.
     *
     * @param  array  $config
     * @param  array  $data
     *
     * @return mixed
     */
    protected function prepareDataValue(array $config, array $data)
    {
        $only = $config['only'] ?? null;
        $except = $config['except'] ?? null;

        if (! is_null($only)) {
            return Arr::only($data, $only);
        } elseif (! is_null($except)) {
            return Arr::except($data, $except);
        }

        return $data;
    }
}
