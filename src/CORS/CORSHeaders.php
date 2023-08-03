<?php

namespace Komodo\Routes\CORS;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: HeaderBase.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

trait CORSHeaders
{
    /**
     * @var CORSOptions
     */
    private $corsOptions;

    public function setControllAllowOringin($oringin)
    {
        $allowOringins = $this->corsOptions->origins;

        if (in_array("*", $allowOringins)) {
            return $this->header('Access-Control-Allow-Origin', "*");
        }

        if (in_array($oringin, $allowOringins)) {
            return $this->header('Access-Control-Allow-Origin', $oringin);
        }
    }

    public function setCORSHeaders()
    {
        $this->setControllAllowOringin($this->getRequestOrigin());
    }

    private function getRequestOrigin()
    {
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            return $_SERVER['HTTP_ORIGIN'];
        } elseif (array_key_exists('HTTP_REFERER', $_SERVER)) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        };
    }
}
