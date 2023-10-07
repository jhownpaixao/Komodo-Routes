<?php
namespace Komodo\Example\Middlewares;

use Komodo\Routes\Http\Request;
use Komodo\Routes\Http\Response;
use Komodo\Routes\Interfaces\Middleware;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: AtuhMiddleware.php
| Data da Criação Mon Sep 04 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

final class AuthMiddleware implements Middleware
{
    /** @var Request */
    private $request;

    /** @var Response */
    private $response;
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    public function run(): void
    {
        echo 'middleware em andamento<br>';
    }
}
