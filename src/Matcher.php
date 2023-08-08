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
    readonly public string $path;

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
    readonly public string $prefix;

    /**
     * @var array
     */
    readonly public array $variables;

    /**
     * @var array
     */
    readonly public array $middlewares;

    /**
     * @var HTTPMethods
     */
    readonly public HTTPMethods $method;

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

        $path = array_key_exists('QUERY_STRING', $_SERVER)
        ?str_replace('route=', '/', $_SERVER[ 'QUERY_STRING' ])
        : $_SERVER[ 'REQUEST_URI' ];

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

        // check absolute
        if (array_key_exists($routePath, $this->routes)) {
            $this->route = $this->routes[ $routePath ];
        } else {
            $this->selectRoute($paths);
        }
    }

    public function selectRoute($paths)
    {
        $selectedRoute = null;
        $selectedParams = [  ];
        foreach ($this->routes as $path => $route) {
            //? Rota já selecionada
            if ($selectedRoute) {
                break;
            }

            //? Desmontar rota
            $routeArr = array_filter(explode('/', $path)) ?: [ '1' => '/' ];

            # Numero de paths incompativeis com a rota atual
            if (count($routeArr) != count($paths)) {
                continue;
            };

            //? Seleciona a rota atual
            $selectedRoute = $route;

            //? Analizar a rota atual
            for ($i = 1; $i < count($routeArr) + 1; $i++) {
                $isParam = preg_match("/(?<={).+?(?=})/", $routeArr[ $i ], $params);
                // var_dump($routeArr[ $i ] . " " . $paths[ $i ]. " isparam: $isParam");
                if ($routeArr[ $i ] == $paths[ $i ] || $isParam) {
                    if ($isParam) {
                        $selectedParams[ $params[ 0 ] ] = $paths[ $i ];
                    }
                    /*  if (!$isParam) {
                    $selectedParams = $paths[ $i ];

                    } */
                    continue;
                };

                $selectedRoute = null; //clean non matched
                $selectedParams = [  ]; //clean non matched
                break;
            }
        }

        $this->route = $selectedRoute;
        $this->params = $selectedParams;
    }
}
