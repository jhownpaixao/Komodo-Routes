<?php

use Komodo\Example\Middlewares\AuthMiddleware;
use Komodo\Example\Middlewares\TesteMiddleware;
use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Error\RouteException;
use Komodo\Routes\Http\Request;
use Komodo\Routes\Http\Response;
use Komodo\Routes\Router;

Router::prefix('/testes')->middleware([ AuthMiddleware::class, TesteMiddleware::class ])->group(function () {
    Router::get('/', function () {
        echo "Pasta testes RAIZ";
    });

    Router::get('/11', function () {
        echo "Pasta testes 11";
    });

    Router::get('/22', function () {
        echo "Pasta testes 22";
    });
});

Router::prefix('/aa')->group(function () {
    Router::get('/', function () {
        echo "Pasta aa RAIZ";
    });

    Router::get('/11', function () {
        echo "Pasta aa 11";
    });

    Router::get('/22', function () {
        echo "Pasta aa 22";
    });

    Router::prefix('/bb')->group(function () {
        Router::get('/', function () {
            echo "Pasta aa-bb raiz";
        });

        Router::get('/11', function () {
            echo "Pasta aa-bb 11";
        });

        Router::get('/22', function () {
            echo "Pasta aa-bb 22";
        });
    });
});

Router::prefix('/bb')->group(function () {
    Router::get('/', function () {
        echo "Pasta bb raiz";
    });

    Router::get('/11', function () {
        echo "Pasta bb 11";
    });

    Router::get('/22', function () {
        echo "Pasta bb 22";
    });
});

Router::group(function () {
    Router::get('/sem', function () {
        echo "sem ok";
    });

    Router::get('/sem1', function () {
        echo "SEM1 OK";
    });

    Router::get('/sem2', function () {
        echo "sem2 OK";
    });

    Router::get('/sem2/{param}', function (Request $request) {
        $p = $request->params[ 'param' ];
        echo "sem2:: $p OK";
    });
    Router::get('/sem2/{param1}/{param2}', function (Request $request) {
        $p1 = $request->params[ 'param1' ];
        $p2 = $request->params[ 'param2' ];
        echo "sem2:: $p1,$p2 OK";
    });

    Router::get('/sem22/{param1}/{param2}', function (Request $request) {
        $p1 = $request->params[ 'param1' ];
        $p2 = $request->params[ 'param2' ];
        echo "sem22:: $p1,$p2 OK";
    });
});

Router::middleware(AuthMiddleware::class)->get('/', function () {
    echo "teste RAIZ GET";
});

Router::post('/', function () {
    echo "teste RAIZ POST";
});

Router::get('/teste2', function () {
    echo "teste 2";
});

Router::math('/match', [ HTTPMethods::GET, HTTPMethods::DELETE, HTTPMethods::PATCH, HTTPMethods::POST ], function (Request $request) {
    echo "match method route: " . $request->method->getValue();
});

Router::error(function (RouteException $error, Request $req, Response $res) {

    switch ($error->getCode()) {
        case 404:
            $message = "You tried to access: '{$req->path}', but this address was not found";
            break;

        case 405:
            $message = "You tried to access: '{$req->path}', but the access method is not allowed";
            break;

        default:
            $message = "There was an error accessing this resource";
            break;
    }

    $res->write($message)->status($error->getCode())->send();
});
