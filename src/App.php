<?php

namespace App;

class App
{

    protected $router;

    public function __construct()
    {
        $this->router = new \App\Routing\Router();
    }

    public static function map(array $methods, array $group, $callable)
    {
        $router = new \App\Routing\Router();
        $router->map($methods, $group['pattern'], $callable);
        return $router;
    }

    public static function post($pattern, $model, $callable)
    {
        return self::map(['POST'], [
            'pattern' => $pattern,
            'model'   => $model,
        ], $callable);
    }

    public static function get($pattern, $model, $callable)
    {
        return self::map(['GET'], [
            'pattern' => $pattern,
            'model'   => $model,
        ], $callable);
    }
}