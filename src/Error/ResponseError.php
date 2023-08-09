<?php

namespace Komodo\Routes\Error;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: ResponseError.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Exception;
use Komodo\Routes\Enums\HTTPResponseCode;
use Komodo\Routes\Interfaces\ResponseBase;

class ResponseError extends Exception
{
    use ResponseBase;

    /**
     * $data
     *
     * @var array<string,string|int>
     */
    private $data;

    /**
     * @param string|array $message
     * @param int|HTTPResponseCode $code
     * @param \Throwable|null $previous
     *
     * @return void
     */

    public function __construct($message, $code = HTTPResponseCode::iternalErro, \Throwable $previous = null)
    {
        $code = $code instanceof HTTPResponseCode ? $code->value : $code;

        if (is_string($message)) {
            $m = $message;
            $this->data = [
                'status' => false,
                'message' => $m,
             ];
        } else {
            $this->data = $message;
        }

        parent::__construct($m, $code, $previous);
        $this->sendResponseToClient();
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function sendResponseToClient()
    {
        $this->sendJson($this->data, $this->code ?? 500);
    }
}
