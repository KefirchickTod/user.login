<?php


class Uri
{

    private $path;

    private $host;
    private $scheme;
    private $port;
    private $query;
    private $fragment;

    public $basePath;

    public function __construct($scheme, $host, $port = null, $path = '/', $query = '', $fragment = '')
    {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $host;
        $this->port = $port;
        $this->path = empty($path) ? '/' : $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);


    }

    protected function filterQuery($query)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }

    protected function filterScheme($scheme)
    {
        $scheme = str_replace('://', '', strtolower((string)$scheme));
        return $scheme;
    }

    protected function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    public static function creat(ServerData $serverData)
    {
        $isSecure = $serverData->get('HTTPS');
        $scheme = (empty($isSecure) || $isSecure === 'off') ? 'http' : 'https';

        if ($serverData->is('HTTP_HOST')) {
            $host = $serverData->get('HTTP_HOST');
        } else {
            $host = $serverData->get('SERVER_NAME');
        }
        $port = (int)$serverData->get('SERVER_PORT', 80);
        if (preg_match('/^(\[[a-fA-F0-9:.]+\])(:\d+)?\z/', $host, $matches)) {
            $host = $matches[1];

            if ($matches[2]) {
                $port = (int)substr($matches[2], 1);
            }
        } else {
            $pos = strpos($host, ':');
            if ($pos !== false) {
                $port = (int)substr($host, $pos + 1);
                $host = strstr($host, ':', true);
            }
        }

        $requestScriptName = parse_url($serverData->get('SCRIPT_NAME'), PHP_URL_PATH);
        $requestScriptDir = dirname($requestScriptName);
        $requestUri = parse_url($serverData->get('REQUEST_URI'), PHP_URL_PATH);
        $basePath = '';
        $virtualPath = $requestUri;
        if (stripos($requestUri, $requestScriptName) === 0) {
            $basePath = $requestScriptName;
        } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
            $basePath = $requestScriptDir;
        }

        if ($basePath) {
            $virtualPath = ltrim(substr($requestUri, strlen($basePath)), '/');
        }


        $queryString = $serverData->get('QUERY_STRING', '');

        $fragment = '';

        $uri = new static($scheme, $host, $port, $virtualPath, $queryString, $fragment);
        if ($basePath) {
            $uri = $uri->withBasePath($basePath);
        }

        return $uri;
    }

    public function withBasePath($basePath)
    {
        if (!is_string($basePath)) {
            throw new Error('Uri path must be a string');
        }
        if (!empty($basePath)) {
            $basePath = '/' . trim($basePath, '/');
        }
        $clone = clone $this;

        if ($basePath !== '/') {
            $clone->basePath = $this->filterPath($basePath);
        }

        return $clone;
    }

    public function getScheme(){
        return $this->scheme;
    }
    public function getPath(){
        return $this->path;
    }
    public function getQuery(){
        return $this->query;
    }
    public function getFragment(){
        return $this->fragment;
    }

}