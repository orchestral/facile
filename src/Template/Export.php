<?php namespace Orchestra\Facile\Template;

use Orchestra\Facile\Template\Composers\Csv;

class Export extends Api
{
    use Csv;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['csv', 'json', 'xml'];
}
