<?php

use Komodo\Routes\Router;

Router::get('/', function () {
    echo "teste";
});

Router::get('/teste2', function () {
    echo "teste 2";
});
