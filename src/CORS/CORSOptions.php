<?php

namespace Komodo\Routes\CORS;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: CORSOptions.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

class CORSOptions
{
    /**
     * @var array
     */
    public $origins;

    /**
     * @var array
     */
    public $originsPatterns;

    /**
     * @var array
     */
    public $methods;

    /**
     * @var array
     */
    public $headers;

    /**
     * @param array $origins Access-Control-Allow-Oringins
     * @param array $originsPatterns Access-Control-Allow-Oringins in Patterns
     * @param array $methods Access-Control-Allow-Methods
     * @param array $headers Access-Control-Allow-Headers
     *
     * @return void
     */
    public function __construct($origins = [ "*" ], $originsPatterns = [ '*' ], $methods = [ '*' ], $headers = [ '*' ])
    {
        $this->origins = $origins;
        $this->origins = $originsPatterns;
        $this->origins = $methods;
        $this->origins = $headers;
    }
}
