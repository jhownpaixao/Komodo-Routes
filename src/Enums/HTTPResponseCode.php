<?php

namespace Komodo\Routes\Enums;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: HTTPResponseCode.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

enum HTTPResponseCode: int
{
        /* Success */
    case continue  = 100;
    case accepted = 202;
    case created = 201;
    case success = 200;
    case partiallyCompletedProcess = 206;

        /* Redirect */
    case redirectingForResponse = 303;

        /* Warning */
    case incompleteRequest = 400;
    case informationAlreadyExists = 409;
    case preconditionRequired = 428;
    case informationNotFound = 404;

        /* Error */
    case iternalErro = 500;
    case methodNotAllowed = 405;
    case informationNotTrue = 406;
    case preProcessNotInitialized = 424;
    case requestTimeOut = 408;
    case informationUnauthorized = 401;
    case informationBlocked = 403;
}
