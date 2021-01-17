<?php

namespace app\core\form;

/**
 * Class Field
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\Model;
use app\core\View;

class InputField extends BaseField
{
    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_NUMBER = 'number';

    public $type;

    public function __construct(Model $model, $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
        $this->params['type'] = $this->type;
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function renderInput()
    {
        return sprintf('<input type="%s" name="%s"%s class="form-control%s">',
            $this->type,
            htmlentities($this->attribute, ENT_COMPAT, 'UTF-8'),
            $this->model->{$this->attribute} != '' ? ' value="' . htmlentities($this->model->{$this->attribute}, ENT_COMPAT, 'UTF-8') . '"' : '',
            $this->model->hasError($this->attribute) ? ' is-invalid' : ''
        );
    }
}