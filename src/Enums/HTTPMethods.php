<?php

namespace Komodo\Routes\Enums;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: HTTPMethods.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/
use MyCLabs\Enum\Enum;

class HTTPMethods extends Enum
{
    const GET = 'GET';
    const POST = 'POST';
    const PATCH = 'PATCH';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const HEAD = 'HEAD';
    const OPTIONS = 'OPTIONS';
}
