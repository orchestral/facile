<?php namespace Orchestra\Facile\Template\Composers;

use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Arrayable;

trait Json
{
    /**
     * Compose JSON.
     *
     * @param  mixed   $view
     * @param  array   $data
     * @param  int     $status
     * @param  array   $config
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function composeJson($view, array $data = [], $status = 200, array $config = [])
    {
        unset($view);

        $uses = Arr::get($config, 'uses');

        if (! is_null($uses)) {
            $data = Arr::get($data, $uses, []);
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = array_map([$this, 'transformToArray'], $data);

        return new JsonResponse($data, $status);
    }
}
