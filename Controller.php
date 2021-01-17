<?php

namespace app\core;

/**
 * Class Controller
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\middlewares\BaseMiddleware;

class Controller
{
    public $action = '';
    public $layout = 'main';
    /**
     * @var \app\core\middlwares\BaseMiddleware[]
     */
    protected $middlewares = [];

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }
}