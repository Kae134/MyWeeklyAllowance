<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\ChildDashboardServiceInterface;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Validator\PasswordValidator;
use MyWeeklyAllowance\Validator\AmountValidator;
use MyWeeklyAllowance\Helper\AuthenticationHelper;
use MyWeeklyAllowance\Helper\BalanceHelper;
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

        AuthenticationHelper::verifyUserPassword(
            $this->userRepository,
            $this->childEmail,
            $password,
        );

        $currentBalance = $this->userRepository->getUserBalance(
            $this->childEmail,
        );

        if ($currentBalance < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }

        $newBalance = $currentBalance - $amount;
        BalanceHelper::updateUserAndChildBalance(
            $this->userRepository,
            $this->childEmail,
            $newBalance,
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

        AuthenticationHelper::verifyUserPassword(
            $this->userRepository,
            $childEmail,
            $password,
        );

        $currentBalance = $this->userRepository->getUserBalance($childEmail);

        if ($currentBalance < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }

        $newBalance = $currentBalance - $amount;
        BalanceHelper::updateUserAndChildBalance(
            $this->userRepository,
            $childEmail,
            $newBalance,
        );

        return $this->userRepository->createExpense(
            $childEmail,
            $amount,
            $description,
        );
    }
}
