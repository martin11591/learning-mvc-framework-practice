<?php

namespace app\core\form;

/**
 * Class BaseField
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\Model;
use app\core\View;

class TextAreaField extends BaseField
{
    public function renderInput()
    {
        return sprintf('<textarea name="%s" class="form-control%s">%s</textarea>',
            htmlentities($this->attribute, ENT_COMPAT, 'UTF-8'),
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->model->{$this->attribute} != '' ? htmlentities($this->model->{$this->attribute}, ENT_COMPAT, 'UTF-8') : ''
        );
    }
}