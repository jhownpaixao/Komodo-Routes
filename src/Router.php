<?php

namespace Komodo\Routes;

/*******************************************************************************************
EP Exodus Project
____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: Router.php
 * Data da Criação Sun May 14 2023
 * Copyright (c) 2023
 *********************************************************************************************/

use Komodo\Logger\Logger;
use Komodo\Routes\CORS\CORSOptions;
use Komodo\Routes\Enums\HTTPMethods;
use Komodo\Routes\Enums\HTTPResponseCode;
use Komodo\Routes\Enums\Paths;
use Komodo\Routes\Error\ResponseError;
use Komodo\Routes\Response;

class Router
{
    // !Register Props
    /**
     * @var array
     */
    private static $prefixes = [  ];

    /**
     * @var string
     *
     */
    private static $prefix;

    /**
     * @var Route[]
     */
    private static $data = [  ];

    /**
     * @var RouteGroup[]
     */
    private static $groupData = null;

    /**
     * @var \Closure|string|string[]
     */
    private static $middewares = null;

    // !Router Props
    /**
     * @var array<string,Route|Route[]>
     */
    private static $routes = [  ];

    /**
     * @var array<string,string>
     */
    public static $paths = [
        'views' => '',
        'templates' => '',
     ];

    /**
     * @var string
     */
    public static $current;

    /**
     * @var Logger
     */
    public static $logger;

    // #Private Methods
    public static function getPrefix()
    {
        return self::$prefix ?: '';
    }
    /**
     * @param string $path
     * @param callable|string $callback
     * @param HTTPMethods|HTTPMethods[]|string[] $method
     *
     * @return Router
     */
    private static function register($path, $callback, $method)
    {

        $path = '/' != $path ? $path : '';
        /* Create Route */
        $path = self::$groupData?end(self::$groupData)->getPrefix() . $path:self::getPrefix() . $path;

        $route = self::createRoute($path, $method, $callback);
        if (self::$groupData) {
            $route->setMiddleware(end(self::$groupData)->getMiddlewares());
            end(self::$groupData)->addRoute($route);
        } else {
            $route->setMiddleware(self::$middewares);
            self::$routes[ $path ] = $route;
        }

        // self::$routes[ $path ] = $route;
        return new self;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    private static function filterPaths($paths)
    {
        $paths = array_filter(explode('/', $paths));
        return $paths;
    }

    /**
     * @param string $path
     * @param HTTPMethods|HTTPMethods[]|string[]  $method
     * @param callable|string|null $callback
     *
     * @return Route
     */
    private static function createRoute($path, $method = null, $callback = null)
    {
        $route = new Route($path, $method);
        if ($callback) {
            $route->setCallback($callback);
        }

        return $route;
    }

    private static function save()
    {
        $group = array_pop(self::$groupData);

        foreach ($group->getRoutes() as $route) {
            if (array_key_exists($route->path, self::$routes)) { //Se a rota ja existir
                if (gettype(self::$routes[ $route->path ]) != 'array') { //Transforma em array caso ainda não seja
                    self::$routes[ $route->path ] = [ self::$routes[ $route->path ] ];
                }
                array_push(self::$routes[ $route->path ], $route);
            } else {
                self::$routes[ $route->path ] = $route;
            }
        }

        self::$data = [  ];
        self::$middewares = null;
        $prfx = explode('/', self::$prefix);
        array_pop($prfx);
        self::$prefix = implode('/', $prfx);
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

    private static function execute($cbs, $req, $res, $next)
    {
        $type = gettype($cbs);

        switch ($type) {
            case 'array':
                foreach ($cbs as $cb) {
                    [ $class, $method ] = self::dismount($cb);
                    self::classExecute($class, $method, [ (object) $req, $res ]);
                }
                ;
                break;

            case 'string':
                [ $class, $method ] = self::dismount($cbs);
                self::classExecute($class, $method, [ (object) $req, $res ]);
                break;
        }
        ;
    }

    private static function classExecute($class, $method, $params = [  ])
    {
        $m = new $class;
        call_user_func_array([ $m, $method ], $params);
    }

    private static function dismount($str)
    {
        $separator = '::';
        $defaultMethod = 'execute';
        $cb = explode($separator, $str);
        $class = $cb[ 0 ];
        $method = count($cb) > 1 ? $cb[ 1 ] : $defaultMethod;
        return [ $class, $method ];
    }

    private static function getPaths()
    {
        $srvdir = str_replace("/", "", $_SERVER[ "REQUEST_URI" ]);
        $srvdir = explode(".php", $srvdir)[ 0 ];
        $dirss = "";
        for ($i = 1; $i <= substr_count($srvdir, "/"); $i++) {
            $dirss = $dirss . "../";
        }
        ;

        /*  Logger::BreackAndLog([$srvdir, $dirss, APP_FOLDER]); */
        return [ $srvdir, $dirss ];
    }
    /**
     * @param HTTPMethods $matcherMethod
     * @param HTTPMethods|HTTPMethods[]|string[]|string $routeMethod
     *
     * @return boolean
     */
    private static function validateMethods($matcherMethod, $routeMethod)
    {
        $matcherMethod = $matcherMethod instanceof HTTPMethods ? $matcherMethod->value : $matcherMethod;

        if (is_array($routeMethod)) {
            $routeMethod = array_map(function ($var) {
                return $var instanceof HTTPMethods ? $var->value : $var;
            }, $routeMethod);

            return in_array($matcherMethod, $routeMethod);
        }
        $routeMethod = $routeMethod instanceof HTTPMethods ? $routeMethod->value : $routeMethod;

        return $matcherMethod === $routeMethod;
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
                    $allows[  ] = $method instanceof HTTPMethods ? $method->value : $method;
                }
            }
        } else {
            $allows[  ] = $methods instanceof HTTPMethods ? $methods->value : $methods;
        }
        return $allows;
    }

    // #Public Methods
    public static function response(HTTPResponseCode $status, $status_message, $data = [  ])
    {
        header("HTTP/1.1 " . $status->value);
        /* header("Content-Type: application/json; charset=utf-8"); */
        $response[ 'status' ] = $status;
        $response[ 'status_message' ] = $status_message;
        $response[ 'data' ] = $data;

        $json_response = json_encode($response);
        echo $json_response;
    }

    /**
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

    /**
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
        self::$logger->register('Komodo\\Router');
        $matcher = new Matcher(self::$routes, self::$prefixes);
        $response = new Response($cors ?: new CORSOptions);
        $route = null;
        $matcher->match();

        self::$logger->debug([
            "route" => $matcher->path,
            "founded" => $matcher->route,
            "method" => $matcher->method->value,
         ], 'Inicializando rotas');

        #Se for um grupo de rotas
        if (is_array($matcher->route)) {
            foreach ($matcher->route as $var) {
                if ($var->method === $matcher->method) {
                    $route = $var;
                }
            }
        } else {
            $route = $matcher->route;
        }

        #Verificar se a rota existe
        if (!$matcher->route) {
            self::$logger->debug('Rota não encontrada');
            $response->write([
                "message" => "Rota não encontrada",
                "status" => false,
             ])->status(HTTPResponseCode::informationNotFound)->sendJson();
        }

        #Se for o method OPTIONS
        if (HTTPMethods::options == $matcher->method) {
            $options = self::filterOptions($matcher->route);
            $alloweds = self::generateAllowedMethods($options);
            $response->header('Allow', implode(', ', $alloweds))->send();
        }

        #Se o method é permitido
        if ($matcher->route && !$route) {
            self::$logger->debug('Método não implementado');
            $options = self::filterOptions($matcher->route);
            $alloweds = self::generateAllowedMethods($options);
            $response
                ->write([
                    "message" => "Método não implementado",
                    "status" => false,
                 ])
                ->status(HTTPResponseCode::methodNotAllowed)
                ->header('Access-Control-Allow-Methods', implode(', ', $alloweds))
                ->sendJson();
        }

        #Definindo rota requisitada
        self::$current = $route->path;

        #Definir corpo da requisição
        if ($body = @file_get_contents('php://input')) {
            $body = json_decode($body, true);
        } else {
            $body = $_POST;
        }
        self::$logger->debug($body, 'Criando requeste');
        $request = new Request($matcher->params, $body, apache_request_headers(), $matcher->method);
        #Executa as middlewares
        if ($route->middlewares) {
            self::processCallbacks($route->middlewares, $request, $response);
        }

        #Executar o controller
        self::processCallbacks($route->callback, $request, $response);
    }
    public static function setPath(Paths $type, $path)
    {
        switch ($type) {
            case Paths::template:
                self::$paths[ 'templates' ] = $path;
                break;
            case Paths::views:
                self::$paths[ 'views' ] = $path;
                break;
        }
    }

    public static function routeToHref($route)
    {

        return ltrim(self::getPaths()[ 1 ] . ltrim($route, '/'), '/');
    }

    public static function group($callback)
    {
        self::$groupData[  ] = new RouteGroup(self::$prefix);
        $callback->__invoke();

        self::save();

        // var_dump(self::$groupData);
        return new self;
    }

    /**
     * @param callable|string|string[] $callback
     *
     * @return [type]
     */
    public function middleware($callback)
    {
        self::$middewares = $callback;
        return new self;
    }

    public static function prefix($prefix)
    {
        $prefix = $prefix ?: '';
        self::$prefix .= $prefix;

        if (self::$groupData) {
            end(self::$groupData)->setPrefix(self::$prefix);
        }

        array_push(self::$prefixes, $prefix);

        return new self;
    }

    // *Http Request Methods
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function get($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::get);
        return new self;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function post($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::post);
        return new self;
    }
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function put($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::put);
        return new self;
    }
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function patch($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::path);
        return new self;
    }
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function delete($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::delete);
        return new self;
    }
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function options($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::options);
        return new self;
    }
    /**
     * @param string $route
     * @param callable|string $callback
     *
     * @return Router
     */
    public static function head($route, $callback)
    {
        self::register($route, $callback, HTTPMethods::head);
        return new self;
    }

    /**
     * @param string $route
     * @param HTTPMethods[]|string[] $methods
     * @param callable|string|Closure $callback
     *
     * @return [type]
     */
    public static function math($route, $methods, $callback)
    {
        self::register($route, $callback, $methods);
        return new self;
    }
}
