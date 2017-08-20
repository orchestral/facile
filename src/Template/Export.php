<?php

namespace Orchestra\Facile\Template;

class Export extends Api
{
    use Composers\Csv;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['csv', 'json', 'xml'];
}
