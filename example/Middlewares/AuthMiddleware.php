<?php
namespace Komodo\Example\Middlewares;

use Komodo\Routes\Http\Request;
use Komodo\Routes\Http\Response;

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

final class AuthMiddleware
{
    public function auth(Request $request, Response $response)
    {
        echo 'em andamento<br>';
    }
}
