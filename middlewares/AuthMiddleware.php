<?php

namespace app\core\middlewares;

use app\core\Application;
use app\core\exceptions\ForbiddenException;

/**
 * Class AuthMiddleware
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class AuthMiddleware extends BaseMiddleware {
    public $actions = [];

    public function __construct($actions)
    {
        $this->actions = $actions;
    }
    public function execute()
    {
        /**
         * If actions is empty means every action from this controller should be processed
         * If there is an action on the list of actions, this means that the middleware
         * should do the processing on this found action
         */
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}