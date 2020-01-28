<?php


namespace App\Interfaces;


interface RouteInterface
{
    public function getArgument($name, $default = null);

    public function getArguments();

    public function getPattern();

    public function run();
}