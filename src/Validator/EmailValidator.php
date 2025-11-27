<?php

namespace MyWeeklyAllowance\Validator;

use MyWeeklyAllowance\Exception\MissingEmailException;
use MyWeeklyAllowance\Exception\InvalidEmailException;

class EmailValidator
{
    // Input: email (string) | Output: void
    public static function validate(string $email): void
    {
        if (empty($email)) {
            throw new MissingEmailException("Email is required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException("Invalid email format");
        }
    }
}
