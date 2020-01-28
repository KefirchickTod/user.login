<?php

namespace App\Routing;

use App\Interfaces\RouterIntefaces;

class Router implements RouterIntefaces
{

    const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z][a-zA-Z0-9_]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;
    const DEFAULT_DISPATCH_REGEX = '[^/]+';
    /**
     * @var string
     */
    protected $basePath;

    private $container = [];
    /**
     * @inheritDoc
     */
    public function map($methods, string $pattern, $callable)
    {
        $methods = array_map('strtolower', $methods);
        $route = new Route($methods);
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

    /**
     * @param string $pattern
     * @return mixed[][]
     */
    public function parse(string $pattern) {
        $routeWithoutClosingOptionals = rtrim($pattern, ']');
        $numOptionals = strlen($pattern) - strlen($routeWithoutClosingOptionals);

        // Split on [ while skipping placeholders
        $segments = preg_split('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);
        if ($numOptionals !== count($segments) - 1) {
            throw new \Error("Number of opening '[' and closing ']' does not match");
        }

        $currentRoute = '';
        $routeDatas = [];
        foreach ($segments as $segment) {
            if ($segment === '') {
                throw new \Error("Empty optional part");
            }

            $currentRoute .= $segment;
            $routeDatas[] = $this->parsePlaceholders($currentRoute);
        }
        return $routeDatas;
    }

    /**
     * Parses a route string that does not contain optional segments.
     */
    private function parsePlaceholders($route) {
        if (!preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $route, $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            return [$route];
        }

        $offset = 0;
        $routeData = [];
        foreach ($matches as $set) {
            if ($set[0][1] > $offset) {
                $routeData[] = substr($route, $offset, $set[0][1] - $offset);
            }
            $routeData[] = [
                $set[1][0],
                isset($set[2]) ? trim($set[2][0]) : self::DEFAULT_DISPATCH_REGEX
            ];
            $offset = $set[0][1] + strlen($set[0][0]);
        }

        if ($offset != strlen($route)) {
            $routeData[] = substr($route, $offset);
        }

        return $routeData;
    }

}