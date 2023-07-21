<?php
namespace Komodo\Routes\Interfaces;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: ResponseBase.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Enums\HTTPResponseCode;

trait ResponseBase
{
    /**
     * @param array $data
     * @param int|HTTPResponseCode $code
     *
     * @return mixed
     */
    protected static function sendJson($data, $code = HTTPResponseCode::success)
    {
        $code = $code instanceof HTTPResponseCode ? $code->value : $code;
        self::prepareResponse($code, [
            "Content-Type" => "application/json",
         ]);
        echo (json_encode($data));
        die();
    }
    protected static function send($data, $code = 200)
    {
        $code = $code instanceof HTTPResponseCode ? $code->value : $code;
        self::prepareResponse($code);
        echo json_encode($data);
    }

    /**
     * @param int $code
     * @param array $headers
     *
     * @return void
     */
    private static function prepareResponse($code, $headers = [  ])
    {
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
        http_response_code($code);
    }
}
