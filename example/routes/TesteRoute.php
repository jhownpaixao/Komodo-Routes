<?php

use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Request;
use Komodo\Routes\Router;

Router::get('/', function (Request $request) {
    echo "teste ".  $request->method->getValue();
});

Router::get('/teste2', function () {
    echo "teste 2";
});

Router::math('/match', [ HTTPMethods::DELETE, HTTPMethods::PATCH, HTTPMethods::POST,HTTPMethods::GET ], function (Request $request) {
    echo "match method route: " . $request->method->getValue();
});
