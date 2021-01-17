<?php

namespace app\core;

/**
 * Class UserModel
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\database\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName();
}