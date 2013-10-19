<?php namespace Orchestra\Facile;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\RenderableInterface;

class Transformable
{
    /**
     * Run data transformation.
     *
     * @param  mixed    $data
     * @return array
     */
    public function run($data)
    {
        if (($data instanceof Eloquent) or ($data instanceof ArrayableInterface)) {
            return $data->toArray();
        } elseif ($data instanceof RenderableInterface) {
            return e($data->render());
        } elseif (is_array($data)) {
            return array_map(array($this, 'run'), $data);
        }

        return $data;
    }
}
