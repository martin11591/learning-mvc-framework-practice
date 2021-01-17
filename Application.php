<?php

/**
 * Register this class in app\core namespace for composer autoload
 * So composer can autoload it properly
 */

namespace app\core;

/**
 * Class Application
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\database\Database;

class Application
{
    public static $ROOT_DIR;
    public $layout = 'main';
    public $router;
    public $request;
    public $response;
    public $session;
    public $view;
    public $db;
    public $userClass;
    public $user = null;
    public static $app;
    public $controller;
    public function __construct($rootPath, $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->user = null;
        $this->userClass = $config['userClass'];

        // Getting data from Request
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->view = new View();

        // Create router for application and
        // Passing data from Request to the router (so can read request data)
        $this->router = new Router($this->request, $this->response);

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = call_user_func([$this->userClass, 'primaryKey']);
            $this->user = call_user_func([$this->userClass, 'findOne'], [$primaryKey => $primaryValue]);
        }
    }
    
    public function run()
    {
        try {
            echo $this->router->resolve(); // Resolve route
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
        }
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }
}