<?php namespace Orchestra\Facile\Template\Composers;

use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Orchestra\Support\Collection;
use Orchestra\Support\Contracts\Csvable;
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

        $filename = Arr::get($config, 'filename', 'export');
        $uses     = Arr::get($config, 'uses', 'data');
        $content  = Arr::get($data, $uses, []);

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
