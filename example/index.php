<?php

include_once __DIR__ . '/../vendor/autoload.php';

$router = new Komodo\Routes\Router();
$router->use('./routes/routes.php');
$router->listen();
