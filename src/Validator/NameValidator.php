<?php

namespace MyWeeklyAllowance\Validator;

use MyWeeklyAllowance\Exception\MissingNameException;
use MyWeeklyAllowance\Exception\InvalidNameException;

class NameValidator
{
    // Input: name (string) | Output: void
    public static function validate(string $name): void
    {
        if (empty($name)) {
            throw new MissingNameException("Name is required");
        }

        if (!self::isValidName($name)) {
            throw new InvalidNameException("Invalid name format");
        }
    }

    // Input: name (string) | Output: bool
    private static function isValidName(string $name): bool
    {
        return preg_match('/^[a-zA-Z\s\-\']+$/', $name) === 1;
    }
}
