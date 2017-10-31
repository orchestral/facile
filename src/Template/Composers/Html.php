<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Http\Response;
use InvalidArgumentException;
use Illuminate\Contracts\View\View;

trait Html
{
    /**
     * Compose HTML.
     *
     * @param  mixed|null   $view
     * @param  array   $data
     * @param  int   $status
     * @param  array   $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Http\Response
     */
    public function composeHtml($view = null, array $data = [], $status = 200, array $config = [])
    {
        if (! isset($view)) {
            throw new InvalidArgumentException('Missing [$view].');
        }

        if (! $view instanceof View) {
            $view = $this->view->make($view);
        }

        return new Response($view->with($data), $status);
    }
}
