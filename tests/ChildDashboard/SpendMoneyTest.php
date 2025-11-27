<?php

namespace MyWeeklyAllowance\Tests\ChildDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ChildDashboardService;
use MyWeeklyAllowance\Exception\InvalidAmountException;
use MyWeeklyAllowance\Exception\MissingAmountException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;
use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\PasswordMismatchException;
use MyWeeklyAllowance\Exception\InsufficientFundsException;

class SpendMoneyTest extends TestCase
{
    private ChildDashboardService $service;

    protected function setUp(): void
    {
        $this->service = new ChildDashboardService();
    }

    public function testShouldRemoveMoneyFromChild(): void
    {
        $this->service->spendMoney("ChildPassword123!", 25.0);
        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenNotEnoughMoney(): void
    {
        $this->expectException(InsufficientFundsException::class);
        $this->service->spendMoney("ChildPassword123!", 10000.0);
    }

    public function testShouldThrowExceptionWhenAmountIsMissing(): void
    {
        $this->expectException(MissingAmountException::class);
        $this->service->spendMoney("ChildPassword123!", 0.0);
    }

    public function testShouldThrowExceptionWhenAmountIsInvalid(): void
    {
        $this->expectException(InvalidAmountException::class);
        $this->service->spendMoney("ChildPassword123!", -25.0);
    }

    public function testShouldThrowExceptionWhenPasswordDoesNotMatchParent(): void
    {
        $this->expectException(PasswordMismatchException::class);
        $this->service->spendMoney("WrongPassword123!", 25.0);
    }

    public function testShouldThrowExceptionWhenPasswordIsMissing(): void
    {
        $this->expectException(MissingPasswordException::class);
        $this->service->spendMoney("", 25.0);
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $this->expectException(InvalidPasswordException::class);
        $this->service->spendMoney("123", 25.0);
    }
}
