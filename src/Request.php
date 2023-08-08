<?php

namespace Komodo\Routes;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: Request.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Enums\HTTPMethods;

class Request
{

    /**
     * @var array|null
     */
    public $params;

    /**
     * @var array|null
     */
    public $query;

    /**
     * @var array
     */
    public $body = [  ];

    /**
     * @var array
     */
    public $headers;

    /**
     * @var HTTPMethods
     */
    public $method;

    /**
     * @param array|null $params
     * @param array $body
     * @param array|null $query
     * @param array $headers
     * @param HTTPMethods $method
     */
    public function __construct($params, $body, $query, $headers, $method)
    {
        $this->params = $params;
        $this->body = $body;
        $this->query = $query;
        $this->headers = $headers;
        $this->method = $method;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     */
    public function setPropety($key, $value)
    {
        if (!$key) {
            return false;
        }

        $this->body[ $key ] = $value;
    }
}
