<?php

namespace Orchestra\Facile\Template\Composers;

use InvalidArgumentException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Response;

trait Html
{
    /**
     * Compose HTML.
     *
     * @param  array   $data
     * @param  int   $status
     * @param  array   $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function composeHtml(array $data = [], $status = 200, array $config = [])
    {
        if (is_null($view = $config['view'])) {
            throw new InvalidArgumentException('Missing [$view].');
        }

        return Response::make($this->convertToHtml($view, $data, $config), $status);
    }

    /**
     * Convert content to XML.
     *
     * @param  \Illuminate\Contracts\View\View|string  $view
     * @param  array  $data
     * @param  array  $config
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function convertToHtml($view, array $data, array $config)
    {
        if (! $view instanceof View) {
            $view = $this->view->make($view);
        }

        return $view->with($data);
    }
}
