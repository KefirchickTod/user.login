<?php

namespace App;

use App\Routing\Router;

class App
{


    /**
     * @var Router
     */
    private static $router;

    public static function map(array $methods, array $group, $callable = null)
    {
        self::$router = self::$router ?: new \App\Routing\Router();
        self::$router->map($methods, $group, $callable);
        return self::$router;
    }

    public static function post($pattern, $model, $callable = null)
    {
        return self::map(['POST'], [
            'pattern' => $pattern,
            'model'   => $model,
        ], $callable);
    }

    public static function get($pattern, $model, $callable = null)
    {
        return self::map(['GET'], [
            'pattern' => $pattern,
            'model'   => $model,
        ], $callable);
    }
    public static function run(){
        $viewd =  self::$router->run();
        echo $viewd;
    }
}