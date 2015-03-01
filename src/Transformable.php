<?php namespace Orchestra\Facile;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model as Eloquent;

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
        if (($data instanceof Eloquent) || ($data instanceof Arrayable)) {
            return $data->toArray();
        } elseif ($data instanceof Renderable) {
            return e($data->render());
        } elseif (is_array($data)) {
            return array_map([$this, 'run'], $data);
        }

        return $data;
    }
}
