<?php

namespace Orchestra\Facile\Template;

class Api extends Parser
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
