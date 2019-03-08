<?php

namespace Orchestra\Facile\Template;

class Export extends Api
{
    use Composers\Csv,
        Composers\Excel;

    /**
     * List of supported format.
     *
     * @var array
     */
    protected $formats = ['csv', 'json', 'xls', 'xlsx', 'xml'];
}
