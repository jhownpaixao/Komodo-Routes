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

use Komodo\Routes\Enums\HTTPResponseCode;
use Komodo\Routes\Router;

class MethodNotAllowed extends RouteException
{

    /**
     * @param string|array $message
     * @param  array<string,string> $alloweds
     * @param int|HTTPResponseCode $code
     * @param \Throwable|null $previous
     *
     * @return void
     */
    public function __construct($message, $alloweds = [  ], \Throwable $previous = null)
    {
        parent::__construct($message, HTTPResponseCode::METHODNOTALLOWED, $previous);
        Router::$logger->error($message);
        header("Access-Control-Allow-Methods: " . implode(', ', $alloweds));
        http_response_code(HTTPResponseCode::METHODNOTALLOWED);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
