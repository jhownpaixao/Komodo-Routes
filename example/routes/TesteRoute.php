<?php

use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Request;
use Komodo\Routes\Router;

Router::get('/', function () {
    echo "teste";
});

Router::get('/teste2', function () {
    echo "teste 2";
});

Router::math('/match', [  HTTPMethods::delete, HTTPMethods::path, HTTPMethods::post ], function (Request $request) {
    echo "match method route: " . $request->method->value;
});
