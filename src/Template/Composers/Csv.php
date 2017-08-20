<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Http\Response;
use Orchestra\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

trait Csv
{
    /**
     * Compose CSV.
     *
     * @param  mixed   $view
     * @param  array   $data
     * @param  int     $status
     * @param  array   $config
     *
     * @return \Illuminate\Http\Response
     */
    public function composeCsv($view = null, array $data = [], $status = 200, array $config = [])
    {
        unset($view);

        $filename = $config['filename'] ?? 'export';
        $uses     = $config['uses'] ?? 'data';
        $content  = $data[$uses] ?? [];

        if (! $content instanceof CsvableInterface) {
            if ($content instanceof Arrayable) {
                $content = $content->toArray();
            }

            $content = (new Collection(array_map([$this, 'transformToArray'], $content)));
        }

        return new Response($content->toCsv(), $status, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            'Cache-Control'       => 'private',
            'pragma'              => 'cache',
        ]);
    }
}
