<?php

namespace App\Routing;

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

        $path = str_replace($pattern, '', $uri->getPath());

        foreach ($methods as $method){
            $method = in_array($method, ['','index','/']) ? '/' : $method;

            if(preg_match("~$method~", $path)){

                return $method == '/' ? 'index' : $method;
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
            /** @var $model Model */

            $model = $data->getModel();
            $methods = get_class_methods($model);

            if($method = $this->parse($data->getPattern(), $uri, $methods)){

                if(class_exists($model)){
                    $model = new $model;

                    if(method_exists($model, $method)){
                        return $model->$method();
                    }
                }
            }
        }
        return '';
    }

}