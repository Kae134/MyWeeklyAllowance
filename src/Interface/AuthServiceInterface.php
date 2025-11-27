<?php

namespace MyWeeklyAllowance\Interface;

use MyWeeklyAllowance\DTO\UserData;

interface AuthServiceInterface
{
    public function login(string $email, string $password): UserData;

    public function signIn(
        string $email,
        string $password,
        string $name,
        string $firstname,
        string $userType,
    ): UserData;
}
