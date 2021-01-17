<?php

namespace app\core;

/**
 * Class Model
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

/**
 * Making it as abstract class allow us
 * to avoid making instances of this class
 */
abstract class Model
{
    const RULE_REQUIRED = 'required';
    const RULE_MATCH = 'match';
    const RULE_UNIQUE = 'unique';
    const RULE_EMAIL = 'email';
    const RULE_MIN_CHARACTERS = 'min';
    const RULE_MAX_CHARACTERS = 'max';
    const RULE_ONLY_DIGITS = 'digits'; // TODO
    const RULE_ONLY_LETTERS = 'letters'; // TODO
    const RULE_ONLY_SPECIAL_CHARACTERS = 'special_characters'; // TODO
    const RULE_AT_LEAST_ONE_SMALL_LETTER = 'one_small_letter'; // TODO
    const RULE_AT_LEAST_ONE_BIG_LETTER = 'one_big_letter'; // TODO
    const RULE_AT_LEAST_ONE_DIGIT = 'one_digit'; // TODO
    const RULE_AT_LEAST_ONE_SPECIAL_CHARACTER = 'one_special_character'; // TODO
    const RULE_ALLOWED_CHARACTERS = 'allowed_characters'; // TODO
    const RULE_DISALLOWED_CHARACTERS = 'disallowed_characters'; // TODO
    const RULE_AT_LEAST_X_CHARACTERS = 'at_least_x_characters'; // TODO
    const RULE_AT_LEAST_X_CHARACTERS_EACH = 'at_least_x_characters_each'; // TODO

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules();

    public function labels()
    {
        return [];
    }

    public function getLabel($attribute)
    {
        return isset($this->labels()[$attribute]) ? $this->labels()[$attribute] : $attribute;
    }

    public $errors = [];

    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute}; // Get value of attribute
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addRuleError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addRuleError($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN_CHARACTERS && mb_strlen($value) < $rule['min']) {
                    $this->addRuleError($attribute, self::RULE_MIN_CHARACTERS, $rule);
                }
                if ($ruleName === self::RULE_MAX_CHARACTERS && mb_strlen($value) > $rule['max']) {
                    $this->addRuleError($attribute, self::RULE_MAX_CHARACTERS, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addRuleError($attribute, self::RULE_MATCH, ['match' => $this->getLabel($rule['match'])]);                                        
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttribute = isset($rule['attribute']) ? $rule['attribute'] : $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->dbh->prepare("SELECT * FROM $tableName WHERE $uniqueAttribute = :attribute");
                    $statement->bindValue(":attribute", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addRuleError($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors); // If errors empty, then is properly validated
    }

    private function addRuleError($attribute, $rule, $params = [])
    {
        $message = isset($this->errorMessages()[$rule]) ? $this->errorMessages()[$rule] : '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function addError($attribute, $message)
    {
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages()
    {
        return [            
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULE_MIN_CHARACTERS => 'Minimum length of this field must be {min} characters',
            self::RULE_MAX_CHARACTERS => 'Maximum length of this field must be {max} characters',
            self::RULE_MATCH => 'This field must be the same as {match} field',
            self::RULE_UNIQUE => 'Record with this {field} already exists'
        ];
    }

    public function hasError($attribute)
    {
        return isset($this->errors[$attribute]) ? $this->errors[$attribute] : false;
    }

    public function getFirstError($attribute)
    {
        return isset($this->errors[$attribute][0]) ? $this->errors[$attribute][0] : '';
    }
}