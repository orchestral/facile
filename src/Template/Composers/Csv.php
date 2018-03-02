<?php

namespace Orchestra\Facile\Template\Composers;

use Orchestra\Support\Collection;
use Illuminate\Support\Facades\Response;
use Orchestra\Support\Contracts\Csvable;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait Csv
{
    /**
     * Compose CSV.
     *
     * @param  mixed  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function composeCsv($view = null, array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        $filename = $config['filename'] ?? 'export';

        return Response::make($this->createCallbackToCsv($data, $config)(), $status, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            'Cache-Control' => 'private',
            'pragma' => 'cache',
        ]);
    }

    /**
     * Stream CSV.
     *
     * @param  mixed   $view
     * @param  array   $data
     * @param  int     $status
     * @param  array   $config
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function streamCsv($view = null, array $data = [], $status = 200, array $config = []): SymfonyResponse
    {
        $filename = $config['filename'] ?? 'export';

        return Response::stream($this->createCallbackToCsv($data, $config), $status, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ]);
    }

    /**
     * Convert content to CSV.
     *
     * @param  array  $data
     * @param  array  $config
     *
     * @return \Closure
     */
    protected function createCallbackToCsv(array $data, array $config)
    {
        $uses = $config['uses'] ?? 'data';
        $content = $data[$uses] ?? [];

        if (! $content instanceof Csvable) {
            if ($content instanceof Arrayable) {
                $content = $content->toArray();
            }

            $content = (new Collection(array_map([$this, 'transformToArray'], $content)));
        }

        return function () use ($content) {
            return $content->toCsv();
        };
    }
}
