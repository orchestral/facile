<?php

namespace Orchestra\Facile\Template\Composers;

use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Support\Arrayable;

trait Xml
{
    /**
     * Compose XML.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function composeXml(array $data = [], $status = 200, array $config = [])
    {
        return Response::make($this->createCallbackToXml($data, $config)(), $status, [
            'Content-Type' => 'text/xml',
        ]);
    }

    /**
     * Compose XML.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function streamXml(array $data = [], $status = 200, array $config = [])
    {
        return Response::stream($this->createCallbackToXml($data, $config), $status, [
            'Content-Type' => 'text/xml',
        ]);
    }

    /**
     * Convert content to XML.
     *
     * @param  array  $data
     * @param  array  $config
     *
     * @return \Closure
     */
    protected function createCallbackToXml(array $data, array $config)
    {
        if (! is_null($root = $config['root'] ?? null)) {
            $data = $data[$root] ?? [];
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = array_map([$this, 'transformToArray'], $data);

        return function () use ($data, $config) {
            return ArrayToXml::convert($data, $config['document-root'] ?? $root);
        };
    }
}
