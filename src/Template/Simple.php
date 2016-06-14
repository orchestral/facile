<?php

namespace Orchestra\Facile\Template;

use Orchestra\Facile\Template\Composers\Html;

class Simple extends Export
{
    use Html;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['csv', 'html', 'json', 'xml'];
}
