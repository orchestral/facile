<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Support\Arrayable;

trait Json
{
    /**
     * Compose JSON.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function composeJson(array $data = [], $status = 200, array $config = [])
    {
        return Response::make($this->createCallbackToJson($data, $config)(), $status, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Stream JSON.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function streamJson(array $data = [], $status = 200, array $config = [])
    {
        return Response::stream($this->createCallbackToJson($data, $config), $status, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Convert content to JSON.
     *
     * @param  array  $data
     * @param  array  $config
     *
     * @return \Closure
     */
    protected function createCallbackToJson(array $data, array $config)
    {
        if (! is_null($uses = $config['uses'] ?? null)) {
            $data = $data[$uses] ?? [];
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = array_map([$this, 'transformToArray'], $data);

        return function () use ($data, $config) {
            return json_encode($data, $config['encoding'] ?? 0);
        };
    }
}
