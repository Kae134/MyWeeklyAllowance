<?php

namespace MyWeeklyAllowance\Interface;

interface ChildDashboardServiceInterface
{
    public function spendMoney(string $password, float $amount): void;
}
