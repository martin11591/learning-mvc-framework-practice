<?php

namespace app\core\form;

/**
 * Class Form
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\Model;
use app\core\form\Field;

class Form
{
    public static function begin($method = 'post', $action = '')
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attribute)
    {
        return new InputField($model, $attribute);
    }
}