<?php

namespace Komodo\Routes;

use Komodo\Logger\Logger;
use Komodo\Routes\CORS\CORSOptions;
use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Error\MethodNotAllowed;
use Komodo\Routes\Error\ResponseError;
use Komodo\Routes\Error\RouteException;
use Komodo\Routes\Error\RouteNotFound;
use Komodo\Routes\Http\Request;
use Komodo\Routes\Http\Response;
use Komodo\Routes\Interfaces\Middleware;
use Komodo\Routes\Support\Matcher;
use Komodo\Routes\Support\Route;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: Router.php
| Data da Criação Mon Sep 04 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

class Router
{
    use \Komodo\Routes\Http\RegistrationMethods;
    use \Komodo\Routes\Support\RouteBase;

    /** @var array<string,Route|Route[]> */
    private static $routes = [  ];

    /** @var Logger */
    public static $logger;

    /** @var string */
    private static $current;

    /**
     * Register a route or groups of routes
     *
     * @param string $path
     * @param callable|string $callback
     * @param HTTPMethods|HTTPMethods[]|string[]|string $method
     *
     * @return Router
     */
    private static function register($path, $callback, $method)
    {
        $group = self::getCurrentGroup();
        $path = '/' != $path ? $path : '';
        /* Create Route */
        $path = $group ? $group->getPrefix() . $path : self::parsedPrefix() . $path;
        $route = self::createRoute($path, $method, $callback);
        $route->setMiddleware(self::getMiddlewares());

        if ($group) {
            $group->addRoute($route);
        } else {
            self::$routes[ $path ] = isset(self::$routes[ $path ]) ? [ self::$routes[ $path ], $route ] : $route;
        }

        return new self;
    }

    /**
     * Listen for http requests and trigger the corresponding route
     *
     * @param bool $handleErros
     * @param CORSOptions|null $cors
     * @param Logger|null $logger
     *
     * @return void
     */
    public static function listen($handleErros = false, $cors = null, $logger = null)
    {
        if ($handleErros) {
            set_error_handler(function ($errno, $errstr) {
                throw new ResponseError($errstr, $errno);
            });
        }

        self::$logger = $logger ? clone $logger : new Logger;
        self::$logger->register(static::class);
        $matcher = new Matcher(self::$routes, self::$prefixes);
        $route = null;
        $matcher->match();

        $response = new Response($cors ?: new CORSOptions);
        $request = new Request($matcher->path, $matcher->params, $_GET ?: [  ], apache_request_headers(), $matcher->method);

        self::$logger->debug([
            "route" => $matcher->path,
            "founded" => $matcher->route,
            "method" => $matcher->method->getValue(),
         ], 'Inicializando rotas');

        #Verificar se a rota existe
        if (!$matcher->route) {
            return self::handleRouteError(new RouteNotFound('Route not found 404'), $request, $response);
        }

        #Se for um grupo de rotas
        if (is_array($matcher->route)) {
            foreach ($matcher->route as $var) {
                if (self::validadeMethod($matcher->method, $var)) {
                    $route = $var;
                }
            }
        } elseif (self::validadeMethod($matcher->method, $matcher->route)) {
            $route = $matcher->route;
        }

        #Se for o method OPTIONS
        if (HTTPMethods::OPTIONS == $matcher->method) {
            $options = self::filterOptions($matcher->route);
            $alloweds = self::generateAllowedMethods($options);
            $response->header('Allow', implode(', ', $alloweds))->send();
        }

        #Se o method é permitido
        if ($matcher->route && !$route) {
            self::$logger->debug('Método não implementado');
            $options = self::filterOptions($matcher->route);
            $alloweds = self::generateAllowedMethods($options);
            return self::handleRouteError(new MethodNotAllowed('Method not allowed', $alloweds), $request, $response);
        }

        #Definindo rota requisitada
        self::$current = $route->path;
        #Executa as middlewares
        if ($route->middlewares) {
            self::processMiddlewares($route->middlewares, $request, $response);
        }

        #Executar o controller
        self::processCallbacks($route->callback, $request, $response);
    }

    /**
     * handleRouteError
     *
     * @param  RouteException $error
     * @param  Request $request
     * @return void
     */
    private static function handleRouteError($error, $request, $response)
    {
        if (isset(self::$routes[ 'route.error' ])) {
            return call_user_func_array((self::$routes[ 'route.error' ])->callback, [ $error, $request, $response ]);
        }
        throw $error;
    }

    /**
     * @param HTTPMethods $requested
     * @param Route $route
     *
     * @return mixed
     */
    private static function validadeMethod($requested, $route)
    {

        $methods = $route->method;
        if (is_array($methods)) {
            foreach ($methods as $method) {
                if ($requested->getValue() === $method) {
                    return true;
                }
            }
        } elseif ($requested->getValue() === $methods) {
            return true;
        }
        return false;
    }

    /**
     * @param Route|Route[] $routes
     *
     * @return array
     */
    private static function filterOptions($routes)
    {
        $methods = [  ];

        if (is_array($routes)) {
            foreach ($routes as $route) {
                $methods[  ] = $route->method;
            }
        } else {
            $methods[  ] = $routes->method;
        }
        return $methods;
    }

    private static function generateAllowedMethods($methods)
    {
        $allows = [  ];

        if (is_array($methods)) {
            foreach ($methods as $method) {
                if (is_array($method)) {
                    $allows = self::generateAllowedMethods($method);
                } else {
                    $allows[  ] = $method instanceof HTTPMethods ? $method->getValue() : $method;
                }
            }
        } else {
            $allows[  ] = $methods instanceof HTTPMethods ? $methods->getValue() : $methods;
        }
        return $allows;
    }

    /**
     * @param \Closure $cbs
     * @param Request $req
     * @param Response $res
     *
     * @return void
     */
    private static function processCallbacks($cbs, $req, $res)
    {
        $next = function () {
            global $res;
            var_dump($res->body);
        };

        if (is_callable($cbs)) {
            call_user_func_array($cbs, [ (object) $req, $res, $next ]);
            return;
        }

        self::execute($cbs, $req, $res, $next);
    }

    /**
     * Processes a middleware
     *
     * @param  class-string|class-string[] $middlewares
     * @return void
     */
    private static function processMiddlewares($middlewares, $req, $res)
    {
        $execute = function ($md, $rq, $rs) {
            /** @var Middleware */
            $mdlw = new $md($rq, $rs);
            $mdlw->run();
        };
        $check = function ($md) {
            return is_subclass_of($md, Middleware::class);
        };

        if (is_array($middlewares)) {
            foreach ($middlewares as $middleware) {
                if (!$check($middleware)) {
                    self::$logger->debug('This class does not belong to or does not implement MiddlewareInterface::' . $middleware);
                    continue;
                };
                $execute($middleware, $req, $res);
            }
        } elseif ($check($middlewares)) {
            $execute($middlewares, $req, $res);
        } else {
            self::$logger->debug('This class does not belong to or does not implement MiddlewareInterface::' . $middlewares);
        }
    }

    private static function execute($cbs, $req, $res, $next)
    {
        $type = gettype($cbs);

        switch ($type) {
            case 'array':
                foreach ($cbs as $cb) {
                    [ $class, $method ] = self::parseCallbacks($cb);
                    self::classExecute($class, $method, [ (object) $req, $res ]);
                };
                break;

            case 'string':
                [ $class, $method ] = self::parseCallbacks($cbs);
                self::classExecute($class, $method, [ (object) $req, $res ]);
                break;
        };
    }

    private static function classExecute($class, $method, $params = [  ])
    {
        $m = new $class;
        call_user_func_array([ $m, $method ], $params);
    }

    private static function parseCallbacks($str)
    {
        $separator = '::';
        $defaultMethod = 'execute';
        $cb = explode($separator, $str);
        $class = $cb[ 0 ];
        $method = count($cb) > 1 ? $cb[ 1 ] : $defaultMethod;
        return [ $class, $method ];
    }

    /**
     * Includes files
     * @param mixed $data
     *
     * @return void
     */
    public function use($data)
    {
        if (gettype($data) === 'string') {
            require_once $data;
        }
    }
}
