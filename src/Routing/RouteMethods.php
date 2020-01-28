<?php

namespace App\Routing;

class RouteMethods{


    public static function map($methods, $models, \Closure $closure){
        return '';
    }

    public static function get($models, \Closure $closure){
        return self::map(['GET'], $models, $closure);
    }

    public static function post($models, \Closure $closure){
        return self::map(['POST'], $models, $closure);
    }



}