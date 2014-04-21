<?php namespace Orchestra\Facile\Template;

use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\View\View;

class Base extends Driver
{
    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = array('html', 'json', 'csv');

    /**
     * Default format.
     *
     * @var string
     */
    protected $defaultFormat = 'html';

    /**
     * Compose HTML.
     *
     * @param  mixed    $view
     * @param  array    $data
     * @param  integer  $status
     * @param  array    $config
     * @return \Illuminate\Http\Response
     */
    public function composeHtml($view = null, array $data = array(), $status = 200, array $config = array())
    {
        if (! isset($view)) {
            throw new InvalidArgumentException("Missing [\$view].");
        }

        if (! $view instanceof View) {
            $view = $this->view->make($view);
        }

        return new IlluminateResponse($view->with($data), $status);
    }

    /**
     * Compose JSON.
     *
     * @param  mixed    $view
     * @param  array    $data
     * @param  integer  $status
     * @param  array    $config
     * @return \Illuminate\Http\JsonResponse
     */
    public function composeJson($view, array $data = array(), $status = 200, array $config = array())
    {
        unset($view);

        $data = array_map(array($this, 'transform'), $data);

        return new JsonResponse($data, $status);
    }

    /**
     * Compose CSV.
     *
     * @param  mixed    $view
     * @param  array    $data
     * @param  integer  $status
     * @param  array    $config
     * @return \Illuminate\Http\Response
     */
    public function composeCsv($view = null, array $data = array(), $status = 200, array $config = array())
    {
        $data     = array_map(array($this, 'transform'), $data);
        $filename = array_get($config, 'filename', 'export');
        $uses     = array_get($config, 'uses', 'data');
        $content  = array_get($data, $uses, array());

        $row = max($content);
        $header = array_keys(array_dot($row));

        ob_start();
        $instance = fopen('php://output', 'r+');
        fputcsv($instance, $header, ',', '"');
        foreach ($content as $key => $line) {
            fputcsv($instance, array_dot($line), ',', '"');
        }

        $response = ob_get_clean();

        return new IlluminateResponse($response, $status, array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            'Cache-Control'       => 'private',
            'pragma'              => 'cache',
        ));
    }
}
