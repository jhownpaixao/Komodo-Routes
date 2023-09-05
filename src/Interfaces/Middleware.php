<?php
namespace Komodo\Routes\Interfaces;

use Komodo\Routes\Http\Request;
use Komodo\Routes\Http\Response;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: Middleware.php
| Data da Criação Mon Sep 04 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

interface Middleware
{
    public function run(): void;
    public function __construct(Request $request, Response $response);
}
