<?php

namespace app\core;

use app\core\Application;
use app\core\exceptions\NotFoundException;

/**
 * Class Router
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class Router
{
    public $request;
    public $response;
    protected $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * Resolving routes - here starts everything
     */
    public function resolve()
    {
        $path = $this->request->path();
        $method = $this->request->method();
        $callback = isset($this->routes[$method][$path]) ? $this->routes[$method][$path] : false;
        if ($callback === false) {
            throw new NotFoundException();
        }
        if (is_string($callback)) {
            return Application::$app->view->renderView($callback);
        }
        if (is_array($callback)) {
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }

        return call_user_func($callback, $this->request, $this->response);
    }
}