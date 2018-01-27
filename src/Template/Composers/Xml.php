<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Http\Response;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Contracts\Support\Arrayable;

trait Xml
{
    /**
     * Compose XML.
     *
     * @param  mixed   $view
     * @param  array   $data
     * @param  int     $status
     * @param  array   $config
     *
     * @return \Illuminate\Http\Response
     */
    public function composeXml($view, array $data = [], int $status = 200, array $config = []): Response
    {
        unset($view);

        if (! is_null($uses = $config['root'] ?? null)) {
            $data = $data[$uses] ?? [];
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = array_map([$this, 'transformToArray'], $data);

        $headers = [
            'Content-Type' => 'text/xml',
        ];

        return new Response(ArrayToXml::convert($data, $root), $status, $headers);
    }
}
