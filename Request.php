<?php

namespace app\core;

/**
 * Class Request
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class Request
{
    public function path()
    {
        $path = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : '/';
        $position = mb_strpos($path, '?');
        if ($position === false) return $path;
        return mb_substr($path, 0, $position);
    }

    public function method()
    {
        return mb_strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get';
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function body()
    {
        $body = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}