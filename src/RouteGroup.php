<?php

namespace Komodo\Routes;

/*
|-----------------------------------------------------------------------------
| LinxSys PHP-CRM
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: RouteGroup.php
| Data da Criação Thu Jul 20 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

class RouteGroup
{
    /**
     * @var Route[]
     */
    public $routes;

    /**
     * @var string
     */
    public $prefix;

    /**
     * @var callable|string|string[]
     */
    public $middlewares;

    public function __construct($prefix = '', $middlewares = [])
    {
        $this->prefix = $prefix;
        $this->middlewares = $middlewares;
        $this->routes = [];
    }


    /**
     * Get the value of prefix
     *
     * @return  string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the value of prefix
     *
     * @param  string  $prefix
     *
     * @return  self
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the value of middlewares
     *
     * @return  callable|string|string[]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * Set the value of middlewares
     *
     * @param  callable|string|string[]  $middlewares
     *
     * @return  self
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * Get the value of routes
     *
     * @return  Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Set the value of routes
     *
     * @param  Route  $route
     *
     * @return  self
     */
    public function addRoute($route)
    {
        $this->routes[] = $route;
        return $this;
    }
}
