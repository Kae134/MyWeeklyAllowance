<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\AuthServiceInterface;
use MyWeeklyAllowance\DTO\UserData;

class AuthService implements AuthServiceInterface
{
    public function login(string $email, string $password): UserData {}

    public function signIn(
        string $email,
        string $password,
        string $name,
        string $firstname,
        string $userType,
    ): UserData {}
}
