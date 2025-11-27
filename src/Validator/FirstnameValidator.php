<?php

namespace MyWeeklyAllowance\Validator;

use MyWeeklyAllowance\Exception\MissingFirstnameException;
use MyWeeklyAllowance\Exception\InvalidFirstnameException;

class FirstnameValidator
{
    // Input: firstname (string) | Output: void
    public static function validate(string $firstname): void
    {
        if (empty($firstname)) {
            throw new MissingFirstnameException("Firstname is required");
        }

        if (!self::isValidFirstname($firstname)) {
            throw new InvalidFirstnameException("Invalid firstname format");
        }
    }

    // Input: firstname (string) | Output: bool
    private static function isValidFirstname(string $firstname): bool
    {
        return preg_match('/^[a-zA-Z\s\-\']+$/', $firstname) === 1;
    }
}
