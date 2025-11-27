<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\ChildDashboardServiceInterface;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Validator\PasswordValidator;
use MyWeeklyAllowance\Validator\AmountValidator;
use MyWeeklyAllowance\Exception\PasswordMismatchException;
use MyWeeklyAllowance\Exception\InsufficientFundsException;

class ChildDashboardService implements ChildDashboardServiceInterface
{
    private UserRepository $userRepository;
    private string $childEmail;

    public function __construct(string $childEmail)
    {
        $this->childEmail = $childEmail;
        $this->userRepository = new UserRepository();
    }

    // Input: password (string), amount (float) | Output: void
    public function spendMoney(string $password, float $amount): void
    {
        PasswordValidator::validate($password);
        AmountValidator::validate($amount);

        $child = $this->userRepository->findByEmail($this->childEmail);

        if ($password !== $child["password"]) {
            throw new PasswordMismatchException("Password does not match");
        }

        $currentBalance = $this->userRepository->getUserBalance(
            $this->childEmail,
        );

        if ($currentBalance < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }

        $this->userRepository->updateUserBalance(
            $this->childEmail,
            $currentBalance - $amount,
        );

        $this->userRepository->updateChildBalance(
            $this->childEmail,
            $currentBalance - $amount,
        );
    }

    // Input: childEmail (string), password (string), amount (float), description (string) | Output: int
    public function spendMoneyWithDescription(
        string $childEmail,
        string $password,
        float $amount,
        string $description,
    ): int {
        PasswordValidator::validate($password);
        AmountValidator::validate($amount);

        $currentBalance = $this->userRepository->getUserBalance($childEmail);

        if ($currentBalance < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }

        $this->userRepository->updateUserBalance(
            $childEmail,
            $currentBalance - $amount,
        );

        $this->userRepository->updateChildBalance(
            $childEmail,
            $currentBalance - $amount,
        );

        return $this->userRepository->createExpense(
            $childEmail,
            $amount,
            $description,
        );
    }
}
