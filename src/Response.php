<?php

namespace Komodo\Routes;

/*******************************************************************************************
EP Exodus Project
____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: Response.php
 * Data da Criação Tue Jul 11 2023
 * Copyright (c) 2023
 *
 *********************************************************************************************/

use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Enums\HTTPResponseCode;
use Komodo\Routes\Interfaces\ViewBaseFunctions;

class Response
{
    use ViewBaseFunctions;
    /**
     * @var  mixed
     */
    public $body;

    /**
     * @var  array
     */
    public $headers;

    /**
     * @var  number|HTTPResponseCode
     */
    public $code = 200;

    /**
     * @var  \Closure
     * @param mixed $body
     * @return string
     */
    public $processBody;

    /**
     * @param array|null $params
     * @param array $body
     * @param array $headers
     * @param \Closure $headers
     * @param HTTPMethods $method
     */
    public function __construct($body = null, $headers = null, $processBody = null)
    {
        $this->body = $body;
        $this->headers = $headers ?: [
            "Content-Type" => "text/html; charset=utf-8",
         ];
        $this->processBody = $processBody ? $processBody : function ($body) {
            return $body;
        };
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function write($value)
    {
        $this->body = $value;
        return $this;
    }

    /**
     * @param string $name name of header
     * @param string|number $value value of header
     * @return $this
     */
    public function header($name, $value)
    {
        $this->headers[ $name ] = $value;
        return $this;
    }

    /**
     * @param string $name name of header
     * @param string|number $value value of header
     *
     * @return void
     */
    public function send()
    {

        $this->prepareResponse();

        $body = call_user_func_array($this->processBody, [ $this->body ]);

        if ($body) {
            echo $body;
        }

        die();
    }

    /**
     * @param string $name name of header
     * @param string|number $value value of header
     *
     * @return void
     */
    public function sendJson()
    {
        $this->header("Content-Type", "application/json; charset=utf-8");
        $this->prepareResponse();

        $body = call_user_func_array($this->processBody, [ $this->body ]);

        if ($body) {
            echo json_encode($body);
        }

        die();
    }

    /**
     * @param int|HTTPResponseCode $code
     *
     * @return $this
     */
    public function status($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param int[]|HTTPMethods[] $methods
     *
     * @return mixed
     */
    public function sendAllowedMethods($methods)
    {
        $alloweds = array_map(function ($value) {
            return $value instanceof HTTPMethods ? $value->value : $value;
        }, $methods);
        $alloweds = implode(',', $alloweds);
        $this->header("Allow", $alloweds);
        $this->send();
    }

    // #Private Methods
    /**
     * @param int $code
     * @param array $headers
     *
     * @return void
     */
    private function prepareResponse()
    {
        $code = $this->code instanceof HTTPResponseCode ? $this->code->value : $this->code;
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        http_response_code($code);
    }
}
