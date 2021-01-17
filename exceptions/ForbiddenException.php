<?php

namespace app\core\exceptions;

/**
 * Class ForbiddenException
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class ForbiddenException extends \Exception {
    protected $code = 403;
    protected $message = 'You don\'t have a permission to access this page';
}