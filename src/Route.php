<?php

namespace Komodo\Routes;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: Route.php
| Data da Criação Thu Aug 03 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/


use Komodo\Routes\Enums\HTTPMethods;

class Route extends \ArrayObject
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var callable
     */
    public $callback;

    /**
     * @var callable|string|string[]
     */
    public $middlewares;

    /**
     * @var HTTPMethods|HTTPMethods[]|string[]|null
     */
    public $method;

    /**
     * @var Route[]|Route|null
     */
    public $children;

    /**
     * @param string $path
     * @param HTTPMethods|HTTPMethods[]|null $method
     */
    public function __construct($path, $method)
    {
        $this->path = $path;
        $this->method = $method;
    }

    /**
     * @param callable $callback
     *
     * @return void
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param callable|string|string[] $callback
     *
     * @return void
     */
    public function setMiddleware($middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @param Route $children
     *
     * @return void
     */
    public function appendChildren($children)
    {
        $this->children = $children;
    }
}
