<?php

namespace Orchestra\Facile\Template;

class Simple extends Export
{
    use Composers\Html;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['csv', 'html', 'json', 'xml'];
}
