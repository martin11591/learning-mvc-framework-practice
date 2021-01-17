<?php

namespace app\core\exceptions;

/**
 * Class NotFoundException
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class NotFoundException extends \Exception {
    protected $code = 404;
    protected $message = 'Page not found';
}