<?php

namespace App\Interfaces;

interface RouterIntefaces
{
    /**
     * @param string[] $methods
     * @param string $pattern
     * @param \Closure|object $callable
     * @return RouterIntefaces
     */
    public function map($methods, string $pattern, $callable);

    /***
     * @param $name
     * @return RouterIntefaces
     */
    public function getNamedRoute($name);

    /***
     * @param string $name
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    public function pathFor($name, array $data = [], array $queryParams = []);
}