<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Arrayable;

trait Json
{
    /**
     * Compose JSON.
     *
     * @param  mixed  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function composeJson($view, array $data = [], int $status = 200, array $config = []): JsonResponse
    {
        unset($view);

        if (! is_null($uses = $config['uses'] ?? null)) {
            $data = $data[$uses] ?? [];
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = array_map([$this, 'transformToArray'], $data);

        return new JsonResponse($data, $status);
    }
}
