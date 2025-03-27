<?php

namespace App\Utils;


class Validator
{
    private $raw_data = null;


    private $validated_data = null;


    public $errors = null;


    public function __construct(array $data)
    {
        $this->raw_data = $data;
    }


    public function validate(array $rules): ?array
    {
        foreach ($rules as $rule_name => $rules_array) {
            if (array_key_exists($rule_name, $this->raw_data)) {
                foreach ($rules_array as $rule) {
                    switch ($rule) {
                        case 'required':
                            $this->required($rule_name, $this->raw_data[$rule_name]);
                            break;
                        case substr($rule, 0, 3) === 'min':
                            $this->min($rule_name, $this->raw_data[$rule_name], $rule);
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $this->getErrors();
    }


    private function required(string $name, string $value): void
    {
        $value = trim($value);

        if (empty($value)) {
            $this->errors[$name][] = "$name value is required.";
        }
    }


    private function min(string $name, string $value, string $rule): void
    {
        preg_match_all('/(\d+)/', $rule, $matches);
        $min = (int) $matches[0][0];

        if (strlen($value) < $min) {
            $this->errors[$name][] = "The $name input must contain at least $min characters.";
        }
    }


    private function getErrors(): ?array
    {
        return $this->errors;
    }
}