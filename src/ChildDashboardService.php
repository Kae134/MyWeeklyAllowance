<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\ChildDashboardServiceInterface;

class ChildDashboardService implements ChildDashboardServiceInterface
{
    public function spendMoney(string $password, float $amount): void {}
}
