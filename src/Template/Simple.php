<?php namespace Orchestra\Facile\Template;

use Orchestra\Facile\Template\Composers\Csv;
use Orchestra\Facile\Template\Composers\Xml;
use Orchestra\Facile\Template\Composers\Html;
use Orchestra\Facile\Template\Composers\Json;

class Simple extends Template
{
    use Csv, Html, Json, Xml;
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
}
