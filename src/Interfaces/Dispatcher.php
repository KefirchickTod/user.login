<?php


namespace App\Interfaces;


interface Dispatcher
{

    /**
     * @param string $methodHTTP
     * @param string $uri
     * @return array
     */
    public function dispatch($methodHTTP, $uri);
}