<?php
namespace App\Models;

use App\ModelTrait;

class Model
{

    use ModelTrait;

    public static function check($data, $keys) : bool {

        $data = array_keys($data);

        return (bool)empty(array_diff($data, $keys));
    }

}