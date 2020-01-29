<?php


namespace App\Interfaces;


interface ProvideRegisterInterface
{
    public static function set($key, $value);

    public static function get($key);

    public static function removeData($key);
}