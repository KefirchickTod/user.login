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

    /**
     * @param $names
     * @param array $param
     * @return \App\Recourse
     * @throws \ErrorException
     */
    function resource($names, $param = [])
    {
        static $resource;
        if (!$resource) {
            $resource = new \App\Recourse();
        }
        $resource->set(!is_array($names) ? [$names] : $names, $param);
        return $resource;
    }
}

if (!function_exists('redirect_post')) {

    /**
     * @param $url
     * @param array $data
     * @param array|null $headers
     * @throws Exception
     */
    function redirect_post($url, array $data, array $headers = null)
    {
        $params = [
            'http' => [
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        if (!is_null($headers)) {
            $params['http']['header'] = '';
            foreach ($headers as $k => $v) {
                $params['http']['header'] .= "$k: $v\n";
            }
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if ($fp) {
            echo @stream_get_contents($fp);
            die();
        } else {
            // Error
            $error = error_get_last();
            debug($error);
            throw new Exception("Error loading '$url', $error");
        }
    }
}

if(!function_exists('isLogin')){
    function isLogin(){

        return empty($_SESSION) || !isset($_SESSION['log']) ? false : (bool)$_SESSION['log'];
    }
}

if(!function_exists('redirect')){
    function redirect($path, $get = []){
        $get = $get ? http_build_query($get) : null;

        header("Location: http://user.login.local/$path".($get ? "?{$get}" : ''));
    }
}