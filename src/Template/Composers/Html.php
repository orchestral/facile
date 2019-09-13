<?php

namespace Orchestra\Facile\Template\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Response;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

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
    public function composeHtml(array $data = [], int $status = 200, array $config = []): SymfonyResponse
    {
        if (\is_null($view = $config['view'])) {
            throw new InvalidArgumentException('Missing [$view].');
        }

        return Response::make($this->convertToViewable($view, $data, $config), $status);
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
    protected function convertToViewable($view, array $data, array $config)
    {
        if (! $view instanceof View) {
            $view = $this->view->make($view);
        }

        return $view->with($data);
    }
}
