<?php namespace Orchestra\Facile\Template;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Orchestra\Support\Collection;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Arrayable;
use Orchestra\Support\Contracts\CsvableInterface;
use Illuminate\Http\Response as IlluminateResponse;

class Simple extends Template
{
    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['csv', 'html', 'json', 'xml'];

    /**
     * Default format.
     *
     * @var string
     */
    protected $defaultFormat = 'html';

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

        return new IlluminateResponse($content->toCsv(), $status, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            'Cache-Control'       => 'private',
            'pragma'              => 'cache',
        ]);
    }

    /**
     * Compose HTML.
     *
     * @param  mixed|null   $view
     * @param  array   $data
     * @param  int   $status
     * @param  array   $config
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \InvalidArgumentException
     */
    public function composeHtml($view = null, array $data = [], $status = 200, array $config = [])
    {
        if (! isset($view)) {
            throw new InvalidArgumentException('Missing [$view].');
        }

        if (! $view instanceof View) {
            $view = $this->view->make($view);
        }

        return new IlluminateResponse($view->with($data), $status);
    }

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

        return new IlluminateResponse(ArrayToXml::convert($data, $root), $status, $headers);
    }
}
