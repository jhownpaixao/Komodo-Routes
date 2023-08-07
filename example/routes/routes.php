<?php

use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Request;
use Komodo\Routes\Router;

Router::prefix('/testes')->group(function () {
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



Router::get('/', function () {
    echo "teste";
});

Router::get('/teste2', function () {
    echo "teste 2";
});

Router::math('/match', [ HTTPMethods::get, HTTPMethods::delete, HTTPMethods::patch, HTTPMethods::post ], function (Request $request) {
    echo "match method route: " . $request->method->value;
});
