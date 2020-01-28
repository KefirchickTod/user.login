<?php


namespace App\Routing;


use App\Interfaces\RouterIntefaces;

class Route implements \App\Interfaces\RouteInterface
{

    protected $methods = [];
    protected $pattern = [];
    protected $callaable;

    public function __construct($methods, $pattern, $callable)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->callaable = $callable;
    }

    public function getArgument($name, $default = null)
    {
        // TODO: Implement getArgument() method.
    }

    public function getArguments()
    {
        // TODO: Implement getArguments() method.
    }

    public function getPattern()
    {
        // TODO: Implement getPattern() method.
    }

    public function run()
    {
        // TODO: Implement run() method.
    }
}