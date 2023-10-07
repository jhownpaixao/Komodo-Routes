<?php

namespace Komodo\Routes\Support;

use Komodo\Routes\Enums\HTTPMethods;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: RouteBase.php
| Data da Criação Mon Sep 04 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Support\RouteGroup;

/**
 * @property array<string,Route|Route[]> $routes
 */
trait RouteBase
{
    //#Currente route process data

    /** @var RouteGroup[] */
    private static $groups = [];

    /** @var class-string|class-string[] */
    private static $middlewares = [];

    /** @var string[] */

    private static $prefixes = [];

    /**
     * Get last prefix
     *
     * @return string
     */
    private static function getCurrentPrefix()
    {
        return array_pop(self::$prefixes);
    }

    /**
     * Get parsed prefix
     *
     * @return string
     */
    private static function parsedPrefix()
    {
        return implode(self::$prefixes);
    }

    /**
     * Get last middleware of queue
     *
     * @return class-string|class-string[]
     */
    private static function getCurrentMiddleware()
    {
        return array_pop(self::$middlewares);
    }

    /**
     * Get all middleware for route register
     *
     * @return class-string[]
     */
    private static function getMiddlewares()
    {
        $routeMiddlewares = [];
        $groupMiddlewares = [];

        if ($group = self::getCurrentGroup()) {
            $gpM = $group->getMiddlewares();
            if (is_array($gpM)) {
                $groupMiddlewares = array_merge($groupMiddlewares, $gpM);
            } else {
                $groupMiddlewares[] = $gpM;
            }
        }

        $rtM = self::getCurrentMiddleware();
        if (is_array($rtM)) {
            array_merge($routeMiddlewares, $rtM);
        } else {
            array_push($routeMiddlewares, $rtM);
        }
        return array_merge($routeMiddlewares, $groupMiddlewares);
    }

    /**
     * Get last group
     *
     * @return RouteGroup
     */
    private static function getCurrentGroup()
    {
        return end(self::$groups);
    }

    /**
     * Get last group
     *
     * @return RouteGroup
     */
    private static function getLasrGroup()
    {
        return array_pop(self::$groups);
    }

    /**
     * Create a router group
     *
     * @param  \Closure $callback
     * @return static
     */
    public static function group($callback)
    {
        $md = self::getMiddlewares();
        self::$groups[] = new RouteGroup(self::parsedPrefix(), $md);
        $callback();

        self::saveGroup();

        return new self;
    }

    /**
     * @param class-string|class-string[] $callback
     *
     * @return static
     */
    public static function middleware($callback)
    {
        array_push(self::$middlewares, $callback);
        return new self;
    }

    /**
     * Set prefix for route or group
     *
     * @param  string $prefix
     * @return static
     */
    public static function prefix(string $prefix)
    {
        array_push(self::$prefixes, $prefix);
        return new self;
    }

    /**
     * @param string $route
     * @param array<HTTPMethods|string> $methods
     * @param callable|string|Closure $callback
     *
     * @return static
     */
    public static function math($route, $methods, $callback)
    {
        self::register($route, $callback, $methods);
        return new self;
    }

    /**
     * error
     *
     * @param  mixed $methods
     * @param  \Closure<\Exception,Request,Response> $callback
     * @return static
     */
    public static function error($callback)
    {
        self::register('route.error', $callback, []);
        return new self;
    }

    /**
     * @param string $path
     * @param HTTPMethods|HTTPMethods[]|string[]  $method
     * @param callable|string|null $callback
     *
     * @return Route
     */
    private static function createRoute($path, $method = null, $callback = null)
    {
        $route = new Route($path, $method);
        if ($callback) {
            $route->setCallback($callback);
        }

        return $route;
    }

    /**
     * Save current route group
     *
     * @return void
     */
    private static function saveGroup()
    {
        $group = self::getLasrGroup();

        foreach ($group->getRoutes() as $route) {
            if (array_key_exists($route->path, self::$routes)) { //Se a rota ja existir
                if (gettype(self::$routes[$route->path]) != 'array') { //Transforma em array caso ainda não seja
                    self::$routes[$route->path] = [self::$routes[$route->path]];
                }
                array_push(self::$routes[$route->path], $route);
            } else {
                self::$routes[$route->path] = $route;
            }
        }

        $prefix = self::getCurrentPrefix();
    }
}
