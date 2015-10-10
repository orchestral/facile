<?php namespace Orchestra\Facile\Template\Composers;

use Illuminate\Http\JsonResponse;

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
        unset($config);

        $data = array_map([$this, 'transformToArray'], $data);

        return new JsonResponse($data, $status);
    }
}
