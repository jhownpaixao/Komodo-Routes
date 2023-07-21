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

enum HTTPMethods: string
{
    case get = 'GET';
    case post = 'POST';
    case path = 'PATH';
    case put = 'PUT';
    case delete = 'DELETE';
    case head = 'HEAD';
    case options = 'OPTIONS';
}
