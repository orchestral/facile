<?php namespace Orchestra\Facile\Template;

use Orchestra\Facile\Template\Composers\Xml;
use Orchestra\Facile\Template\Composers\Json;

class Api
{
    use Json, Xml;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['json', 'xml'];
}
