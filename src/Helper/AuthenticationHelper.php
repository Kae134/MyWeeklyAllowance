<?php

namespace MyWeeklyAllowance\Helper;

use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Exception\PasswordMismatchException;

class AuthenticationHelper
{
    // Input: userRepository (UserRepository), email (string), password (string) | Output: void
    public static function verifyUserPassword(
        UserRepository $userRepository,
        string $email,
        string $password
    ): void {
        $user = $userRepository->findByEmail($email);

        if ($user === null || $user["password"] !== $password) {
            throw new PasswordMismatchException("Password does not match");
        }
    }
}
