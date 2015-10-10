<?php namespace Orchestra\Facile\Template\Composers;

use Illuminate\Http\Response;
use Spatie\ArrayToXml\ArrayToXml;

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
    public function composeXml($view, array $data = [], $status = 200, array $config = [])
    {
        unset($view);

        $root = Arr::get($config, 'root');

        $data = array_map([$this, 'transformToArray'], $data);

        $headers = [
            'Content-Type' => 'text/xml',
        ];

        return new Response(ArrayToXml::convert($data, $root), $status, $headers);
    }
}
