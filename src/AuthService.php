<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\AuthServiceInterface;
use MyWeeklyAllowance\DTO\UserData;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Validator\EmailValidator;
use MyWeeklyAllowance\Validator\PasswordValidator;
use MyWeeklyAllowance\Validator\NameValidator;
use MyWeeklyAllowance\Validator\FirstnameValidator;
use MyWeeklyAllowance\Exception\EmailNotFoundInDatabaseException;
use MyWeeklyAllowance\Exception\PasswordMismatchException;

class AuthService implements AuthServiceInterface
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Input: email (string), password (string) | Output: UserData
    public function login(string $email, string $password): UserData
    {
        EmailValidator::validate($email);
        PasswordValidator::validate($password);

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new EmailNotFoundInDatabaseException(
                "Email not found in database",
            );
        }

        if ($user["password"] !== $password) {
            throw new PasswordMismatchException("Password does not match");
        }

        return new UserData(
            $user["email"],
            $user["name"],
            $user["firstname"],
            $user["user_type"],
            $user["balance"],
        );
    }

    // Input: email (string), password (string), name (string), firstname (string), userType (string) | Output: UserData
    public function signIn(
        string $email,
        string $password,
        string $name,
        string $firstname,
        string $userType,
    ): UserData {
        EmailValidator::validate($email);
        PasswordValidator::validate($password);
        NameValidator::validate($name);
        FirstnameValidator::validate($firstname);

        $userData = [
            "email" => $email,
            "password" => $password,
            "name" => $name,
            "firstname" => $firstname,
            "userType" => $userType,
            "balance" => 0.0,
        ];

        $this->userRepository->createUser($userData);

        return new UserData($email, $name, $firstname, $userType, 0.0);
    }
}
