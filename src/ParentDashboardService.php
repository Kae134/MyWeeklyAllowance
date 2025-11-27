<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\ParentDashboardServiceInterface;
use MyWeeklyAllowance\DTO\ChildData;

class ParentDashboardService implements ParentDashboardServiceInterface
{
    public function createChild(
        string $email,
        string $password,
        string $name,
        string $firstname,
    ): ChildData {}

    public function depositMoney(
        string $parentPassword,
        string $childEmail,
        float $amount,
    ): void {}

    public function saveExpense(int $expenseId): void {}

    public function fixAllowance(string $childEmail, float $amount): void {}

    public function addMoneyToAccount(float $amount): void {}
}
