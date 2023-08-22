<?php

namespace Komodo\Routes;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: Request.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Enums\HTTPMethods;

class Request
{

    /**
     * @var array|null
     */
    public $params;

    /**
     * @var array|null
     */
    public $query;

    /**
     * @var array
     */
    public $body = [  ];

    /**
     * @var array
     */
    public $headers;

    /**
     * @var HTTPMethods
     */
    public $method;

    /**
     * @param array|null $params
     * @param array|null $query
     * @param array $headers
     * @param HTTPMethods $method
     */
    public function __construct($params, $query, $headers, $method)
    {
        $this->params = $params;
        $this->query = $query;
        $this->headers = $headers;
        $this->method = $method;
        $this->bodyParse();
    }

    private function bodyParse()
    {
        $b = [  ];

        $body = @file_get_contents('php://input');
        $type = isset($this->headers[ 'Content-Type' ])?explode(';', $this->headers[ 'Content-Type' ])[ 0 ]:'';
        switch ($type) {
            case 'application/json':
                $b = $this->parseJson($body);
                break;

            case 'multipart/form-data':
                $b = $this->parseMultipart($body);
                break;
            default:
                $b = [ $body ];
                break;
        }

        $this->body = $b;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     */
    public function setPropety($key, $value)
    {
        if (!$key) {
            return false;
        }

        $this->body[ $key ] = $value;
    }

    public function parseMultipart($raw_data)
    {
        if ($_FILES || $_POST) {
            return array_merge($_POST, $_FILES);
        }
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ("--\r\n" == $part) {
                break;
            }

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[ strtolower($name) ] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers[ 'content-disposition' ])) {
                $filename = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers[ 'content-disposition' ],
                    $matches
                );
                list(, $type, $name) = $matches;
                isset($matches[ 4 ]) and $filename = $matches[ 4 ];

                // handle your fields here
                switch ($name) {
                    // this is a file upload
                    case 'userfile':
                        file_put_contents($filename, $body);
                        break;

                    // default for all other files is to populate $data
                    default:
                        $data[ $name ] = substr($body, 0, strlen($body) - 2);
                        break;
                }
            }
        }

        return $data;
    }

    public function parseJson($raw_data)
    {
        return json_decode($raw_data, true) ?: [  ];
    }
}
