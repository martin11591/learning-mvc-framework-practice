<?php

namespace app\core\middlewares;

/**
 * Class BaseMiddleware
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

abstract class BaseMiddleware {
    abstract public function execute();
}