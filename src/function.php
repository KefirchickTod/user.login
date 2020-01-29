<?php
if (!function_exists('debug')) {
    function debug($value)
    {
        var_dump($value);
        exit;
    }
}

if (!function_exists('addStyleOrScript')) {
    function addStyleOrScript($link, $tags)
    {
        if ($tags === 'script') {
            return "<script src = '$link' defer></script>";
        } elseif ($tags === 'link') {
            return "<link rel='stylesheet' href='$link'>";
        } else {
            return "<$tags>$link</$tags>";
        }
    }
}

if (!function_exists('addJs')) {
    function addJs()
    {
        $js = func_get_args();
        foreach ($js as $value) {
            $result[] = addStyleOrScript($value, 'script');
        }
        return join('', $result);
    }
}

if (!function_exists('addCss')) {
    function addCss()
    {
        $result = [];
        $css = func_get_args();
        foreach ($css as $value) {

            $result[] = addStyleOrScript($value, 'link');
        }
        return join('', $result);
    }
}

if (!function_exists('resource')) {
    function resource($name, $parram = [])
    {
        $resource = new \App\Recourse();
        $resource->add(is_array($name) ? $name : [$name]);
        $resource->addParram($parram);
        return $resource;

    }
}