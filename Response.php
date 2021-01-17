<?php

namespace app\core;

/**
 * Class Response
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class Response
{
    public function setStatusCode($code)
    {
        http_response_code($code);
    }

    public function redirect($url)
    {
        header("Location: $url");
    }
}