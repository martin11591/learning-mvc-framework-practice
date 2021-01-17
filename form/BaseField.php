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

abstract class BaseField
{
    public $model;
    public $attribute;
    public $params = [];
    
    abstract public function renderInput();

    public function __construct(Model $model, $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->params = [
            'name' => $this->attribute,
            'label' => $this->model->getLabel($this->attribute),
            'value' => $this->model->{$this->attribute},
            'hasError' => $this->model->hasError($this->attribute),
            'firstError' => $this->model->getFirstError($this->attribute),
            'fieldHTML' => $this->renderInput()
        ];
    }

    public function __toString()
    {
        return View::renderPartial('forms/field', $this->params);
    }
}