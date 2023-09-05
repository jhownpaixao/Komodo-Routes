<?php

namespace Komodo\Routes\Http;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: RouteMethods.php
| Data da Criação Mon Sep 04 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Enums\HTTPMethods;

trait RegistrationMethods
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
        self::register($route, $callback, HTTPMethods::GET);
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
        self::register($route, $callback, HTTPMethods::POST);
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
        self::register($route, $callback, HTTPMethods::PUT);
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
        self::register($route, $callback, HTTPMethods::PATCH);
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
        self::register($route, $callback, HTTPMethods::DELETE);
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
        self::register($route, $callback, HTTPMethods::OPTIONS);
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
        self::register($route, $callback, HTTPMethods::HEAD);
        return new self;
    }
}
