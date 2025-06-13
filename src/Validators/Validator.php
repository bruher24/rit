<?php

namespace App\Validators;

class Validator
{
    private string $storageFile = __DIR__ . '/../storage/actives.json';

    private array $rules = [
        'name' => [
            'required' => true,
            'unique' => true,
            'string' => true,
            'min' => 3,
            'max' => 50,
        ],
        'type' => [
            'required' => true,
            'enum' => ['money', 'other'],
        ],
        'totalCost' => [
            'float' => true,
            'min' => 0.1,
        ],
        'bankName' => [
            'or' => 'currency',
            'string' => true,
            'min' => 3,
            'max' => 50,
        ],
        'accountNumber' => [
            'or' => 'currency',
            'string' => true,
            'min' => 3,
            'max' => 50,
        ],
        'currency' => [
            'or' => 'bankName,accountNumber',
            'string' => true,
            'min' => 3,
            'max' => 50,
        ],
        'startBalanceCost' => [
            'float' => true,
            'min' => 0.1,
        ],
        'residualBalanceCost' => [
            'float' => true,
            'min' => 0.1,
        ],
        'finalBalanceCost' => [
            'float' => true,
            'min' => 0.1,
        ],
        'inventoryNumber' => [
            'string' => true,
            'min' => 3,
            'max' => 50,
        ],
        'measureUnits' => [
            'string' => true,
            'min' => 3,
            'max' => 50,
        ],
        'productionDate' => [
            'string' => true,
            'min' => 4,
            'max' => 50,
        ],
    ];

    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule => $ruleValue) {
                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    if ($rule === 'or' || $rule === 'unique') {
                        $check = $this->$method($field, $value, $ruleValue, $data);
                    }else {
                        $check = $this->$method($field, $value, $ruleValue);
                    }
                    if (!$check) {
                        break;
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateRequired(string $field, $value, bool $required): bool
    {
        if ($required && ($value === null || $value === '')) {
            $this->errors[$field][] = "Поле $field является обязательным!";
            return false;
        }
        return true;
    }

    private function validateUnique(string $field, $value, bool $required, array $data): bool
    {
        if (!isset($data['id']) && $required && ($value != null || $value != '')) {
            $actives = $this->loadActives();
            foreach ($actives as $active) {
                if ($active[$field] == $value) {
                    $this->errors[$field][] = "Поле $field должно быть уникальным!";
                    return false;
                }
            }
        }
        return true;
    }

    private function validateString(string $field, $value, bool $isString): bool
    {
        if ($value !== null && $isString && !is_string($value)) {
            $this->errors[$field][] = "Поле $field должно являться строкой!";
            return false;
        }
        return true;
    }

    private function validateMin(string $field, $value, $min): bool
    {
        if ($value === null) return true;

        if (is_string($value)) {
            if (strlen($value) < $min) {
                $this->errors[$field][] = "Поле $field должно быть не короче $min символов!";
                return false;
            }
        } else if (is_numeric($value)) {
            if ($value < $min) {
                $this->errors[$field][] = "Значение $field должно быть не меньше $min!";
                return false;
            }
        }

        return true;
    }

    private function validateMax(string $field, $value, $max): bool
    {
        if ($value === null) return true;

        if (is_string($value)) {
            if (strlen($value) > $max) {
                $this->errors[$field][] = "Поле $field должно быть не длиннее $max символов!";
                return false;
            }
        } else if (is_numeric($value)) {
            if ($value > $max) {
                $this->errors[$field][] = "Значение $field должно быть не больше $max!";
                return false;
            }
        }

        return true;
    }

    private function validateEnum(string $field, $value, array $allowedValues): bool
    {
        if ($value !== null && !in_array($value, $allowedValues, true)) {
            $this->errors[$field][] = "Поле $field может быть: " . implode(', ', $allowedValues) . "!";
            return false;
        }
        return true;
    }

    private function validateFloat(string $field, $value, bool $isFloat): bool
    {
        if ($value !== null && $isFloat && !is_numeric($value)) {
            $this->errors[$field][] = "Поле $field должно быть числом (целым или нет)!";
            return false;
        }
        return true;
    }

    private function validateOr(string $field, $value, string $required, array $data): bool
    {
        if ($value == null || $value == '') {
            $keys = explode(',', $required);
            $fieldsToFill = '';
            foreach ($keys as $key) {
                if ((isset($data[$key]) && ($data[$key] == null || $data[$key] == ''))) {
                    $fieldsToFill .= $key . ', ';
                }
            }
            if (!empty($fieldsToFill)) {
                $result = rtrim($fieldsToFill, ', ');
                $this->errors[$field][] = "Необходимо заполнить поле $field либо: $result!";
                return false;
            }
        }
        return true;
    }

    private function loadActives(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $json = file_get_contents($this->storageFile);
        return json_decode($json, true) ?? [];
    }
}