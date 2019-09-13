<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

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
    public function composeJson(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        return Response::make($this->createJsonResponse($data, $config)(), $status, [
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
    public function streamJson(array $data = [], $status = 200, array $config = []): SymfonyResponse
    {
        return Response::stream($this->createJsonResponse($data, $config), $status, [
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
    protected function createJsonResponse(array $data, array $config)
    {
        if (! \is_null($uses = $config['uses'] ?? null)) {
            $data = $data[$uses] ?? [];
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = \array_map([$this, 'transformToArray'], $data);

        return function () use ($data, $config) {
            return \json_encode($data, $config['encoding'] ?? 0);
        };
    }
}
