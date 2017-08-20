<?php

namespace Orchestra\Facile\Template;

class Api extends Template
{
    use Composers\Json,
        Composers\Xml;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['json', 'xml'];
}
