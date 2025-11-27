<?php

namespace MyWeeklyAllowance\Validator;

use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;

class PasswordValidator
{
    private const MIN_LENGTH = 8;

    // Input: password (string) | Output: void
    public static function validate(string $password): void
    {
        if (empty($password)) {
            throw new MissingPasswordException("Password is required");
        }

        if (!self::isValidPassword($password)) {
            throw new InvalidPasswordException("Invalid password format");
        }
    }

    // Input: password (string) | Output: bool
    private static function isValidPassword(string $password): bool
    {
        if (strlen($password) < self::MIN_LENGTH) {
            return false;
        }

        $hasUpperCase = preg_match("/[A-Z]/", $password);
        $hasLowerCase = preg_match("/[a-z]/", $password);
        $hasNumber = preg_match("/[0-9]/", $password);
        $hasSpecialChar = preg_match(
            '/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?]/',
            $password,
        );

        return $hasUpperCase && $hasLowerCase && $hasNumber && $hasSpecialChar;
    }
}
