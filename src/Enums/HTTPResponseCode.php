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
use MyCLabs\Enum\Enum;

class HTTPResponseCode extends Enum
{
     const CONTINUE  = 100;
     const ACCEPTED = 202;
     const CREATED = 201;
     const SUCCESS = 200;
     const PARTIALLYCOMPLETEDPROCESS = 206;

    /* Redirect */
     const REDIRECTINGFORRESPONSE = 303;

    /* Warning */
     const INCOMPLETEREQUEST = 400;
     const INFORMATIONALREADYEXISTS = 409;
     const PRECONDITIONREQUIRED = 428;
     const INFORMATIONNOTFOUND = 404;

    /* Error */
     const ITERNALERRO = 500;
     const METHODNOTALLOWED = 405;
     const INFORMATIONNOTTRUE = 406;
     const PREPROCESSNOTINITIALIZED = 424;
     const REQUESTTIMEOUT = 408;
     const INFORMATIONUNAUTHORIZED = 401;
     const INFORMATIONBLOCKED = 403;
}
