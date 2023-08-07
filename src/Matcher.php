<?php

namespace Komodo\Routes;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: Matcher.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Route;

class Matcher
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var Route[]|Route|null
     */
    public $route;

    /**
     * @var array<string,string>|null
     */
    public $params;
    /**
     * @var string
     */
    public $prefix;
    /**
     * @var array
     */
    public $variables;
    /**
     * @var array
     */
    public $middlewares;
    /**
     * @var HTTPMethods
     */
    public $method;

    /**
     * @var array
     */
    private $prefixes;

    /**
     * @var array<string,Route>
     */
    private $routes;

    /**
     * @param array $routes
     * @param array|null $prefixes
     */
    public function __construct($routes, $prefixes)
    {

        $path = array_key_exists('QUERY_STRING', $_SERVER)?str_replace('route=', '/', $_SERVER[ 'QUERY_STRING' ]): $_SERVER[ 'REQUEST_URI' ];
        $this->path = $path;
        $this->method = HTTPMethods::from($_SERVER[ 'REQUEST_METHOD' ]);
        $this->prefixes = $prefixes ?? [  ];
        $this->routes = $routes;
        $this->getData();
    }

    private function getData()
    {
        $this->prefix = $this->getPrefix();
    }

    private function getPrefix()
    {
        foreach ($this->prefixes as $prefix) {
            #if (str_contains($url, $prefix)) { <- para PHP 8+

            if (strpos($this->path, $prefix) !== false) {
                return $prefix;
            }
        }
        return '';
    }

    public function match()
    {
        $routePath = $this->path;
        $paths = array_filter(explode('/', $routePath)) ?: [ '1' => '/' ];
        $mathedPaths = null;

        $mathedRoute = null;
        $mathedParams = null;

        // check absolute
        if (array_key_exists($routePath, $this->routes)) {
            $mathedRoute = $this->routes[ $routePath ];
        } else {
            foreach ($this->routes as $path => $route) {
                $routeArr = array_filter(explode('/', $path)) ?: [ '1' => '/' ];
                //if (count($routeArr) != count($paths)) continue;
                if ($mathedRoute) {
                    break;
                }

                if (count($routeArr) < count($paths)) {
                    continue;
                };

                // ?Analize the actual routeArr
                for ($i = 1; $i < count($routeArr) + 1; $i++) {
                    $sRoute = array_key_exists($i, $routeArr) ? $routeArr[ $i ] : false;
                    $sPath = array_key_exists($i, $paths) ? $paths[ $i ] : false;
                    $mathedRoute = $route;
                    $isParam = preg_match("/(?<={).+?(?=})/", $routeArr[ $i ], $param);
                    if ($sRoute && $sPath && ($routeArr[ $i ] == $paths[ $i ] || $isParam)) {
                        if ($isParam) {
                            $mathedParams[ $param[ 0 ] ] = $paths[ $i ];
                        }

                        if (!$isParam) {
                            $mathedPaths = $paths[ $i ];
                        }

                        continue;
                    }
                    ;

                    $mathedRoute = null; //clean non matched
                    $mathedParams = null; //clean non matched

                    if ($mathedPaths) {
                        break 2;
                    } else {
                        break 1;
                    }
                }
            }
        }
        $this->route = $mathedRoute;
        $this->params = $mathedParams;
    }
}
