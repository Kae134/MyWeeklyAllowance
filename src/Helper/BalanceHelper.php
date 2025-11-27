<?php

namespace MyWeeklyAllowance\Helper;

use MyWeeklyAllowance\Repository\UserRepository;

class BalanceHelper
{
    // Input: userRepository (UserRepository), email (string), newBalance (float) | Output: void
    public static function updateUserAndChildBalance(
        UserRepository $userRepository,
        string $email,
        float $newBalance
    ): void {
        $userRepository->updateUserBalance($email, $newBalance);
        $userRepository->updateChildBalance($email, $newBalance);
    }
}
