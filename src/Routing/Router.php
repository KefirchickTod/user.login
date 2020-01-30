<?php

namespace App\Routing;


use App\Controllers\Controller;
use App\HTTP\Uri;
use App\Interfaces\RouterIntefaces;
use App\Models\Model;
use App\Recourse;


class Router implements RouterIntefaces
{
    private $route;
    /**
     * @var string[]
     */
    private $methods;
    /**
     * @var string
     */
    protected $basePath;

    private $container = [];

    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function map($methods, $group, $callable)
    {
        $methods = array_map('strtolower', $methods);
        $callable = !$callable ? $this : $callable;
        $route = new Route($methods, $group, $callable);
        $this->route[] = $route;
    }

    public function getMethod(){

    }

    /**
     * @inheritDoc
     */
    public function getNamedRoute($name)
    {
        // TODO: Implement getNamedRoute() method.
    }

    /**
     * @inheritDoc
     */
    public function pathFor($name, array $data = [], array $queryParams = [])
    {
        // TODO: Implement pathFor() method.
    }
    public function name($name){
        $this->container[$name] = $this;
    }


    public function parse(string $pattern, Uri $uri, $methods) {

        $path = trim(str_replace($pattern, '', $uri->getPath()),'/');

        if($methods){
            foreach ($methods as $value){
                $value = in_array($value, ['','index','/']) ? '/' : $value;

                if(preg_match("~$value~", $path) || $path === ''){

                    return $value == '/' ? 'index' : $value;
                }
            }
        }
       return 'index';
    }

    /**
     * Parses a route string that does not contain optional segments.
     */



    public function run(){
        $uri = \App\Http\Uri::creat(new \App\Http\ServerData());
        foreach ($this->route as $data){

            /** @var $data Route */
            /** @var $controller Controller */

            $controller = $data->getModel();

            $methods = get_class_methods($controller);

            if($method = $this->parse($data->getPattern(), $uri, $methods)){
                if(class_exists($controller)){
                    $controller = new $controller;

                    if(method_exists($controller, $method)){
                        return $controller->$method();
                    }
                }
            }
        }
        return '';
    }

}