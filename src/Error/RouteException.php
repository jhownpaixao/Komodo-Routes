<?php

namespace Komodo\Routes\Error;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: RouteException.php
| Data da Criação Mon Sep 04 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Exception;
use Komodo\Routes\Enums\HTTPResponseCode;
use Komodo\Routes\Router;

class RouteException extends Exception
{

    /**
     * @param string|array $message
     * @param int|HTTPResponseCode $code
     * @param \Throwable|null $previous
     *
     * @return void
     */
    public function __construct($message, $code = HTTPResponseCode::ITERNALERRO, \Throwable $previous = null)
    {
        $code = $code instanceof HTTPResponseCode ? $code->getValue() : $code;
        parent::__construct($message, $code, $previous);
        Router::$logger->error($message);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
