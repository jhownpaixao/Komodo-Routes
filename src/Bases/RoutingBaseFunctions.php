<?php

namespace Komodo\Routes\Bases;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: RoutingBaseFunctions.php
| Data da Criação Wed Aug 02 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Router;

trait RoutingBaseFunctions
{
    // *Http Request Methods
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function get($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::get);
        return new self;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function post($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::post);
        return new self;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function put($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::put);
        return new self;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function patch($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::patch);
        return new self;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function delete($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::delete);
        return new self;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function options($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::options);
        return new self;
    }
    
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function head($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::head);
        return new self;
    }

    /**
     * @param string $route
     * @param HTTPMethods[]|string[] $methods
     * @param callable|string|Closure $callback
     *
     * @return [type]
     */
    public static function math($route, $methods, $callback)
    {
        self::register($route, $callback, $methods);
        return new self;
    }
}
