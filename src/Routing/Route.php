<?php


namespace App\Routing;


use App\Models\Model;

class Route
{

    /**
     * @var string[]
     */
    private $methods;

    /**
     * @var array
     */
    private $group;
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $model;

    private $callable;

    private $indefier;

    public function __construct($methods, $group, $callable, $indefier = 0) {

        $this->methods = array_map('strtoupper', $methods);
        $this->group = $group;
        $this->pattern = $group['pattern'] ?: '/';
        $this->model = $group['model'] ?: Model::class;
        $this->callable = $callable;
        $this->indefier = "route.{$indefier}";
    }

    public function getMethods(){
        return $this->methods;
    }
    public function getPattern(){
        return $this->pattern;
    }

    public function getCallable(){
        return $this->callable;
    }

    public function getModel(){
        return $this->model;
    }

    public function __invoke(Model $model, $method)
    {

    }
}