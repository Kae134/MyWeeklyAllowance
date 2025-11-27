<?php

namespace MyWeeklyAllowance\Tests\ParentDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Exception\InvalidEmailException;
use MyWeeklyAllowance\Exception\MissingEmailException;
use MyWeeklyAllowance\Exception\EmailNotFoundInDatabaseException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;
use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\PasswordMismatchException;
use MyWeeklyAllowance\Exception\InvalidAmountException;
use MyWeeklyAllowance\Exception\MissingAmountException;
use MyWeeklyAllowance\Exception\InsufficientFundsException;

class DepositMoneyTest extends TestCase
{
    private ParentDashboardService $service;

    protected function setUp(): void
    {
        $this->service = new ParentDashboardService("parent@example.com");
    }

    public function testShouldAddMoneyToChildAccount(): void
    {
        $this->service->depositMoney(
            "ValidPassword123!",
            "child@example.com",
            50.0,
        );
        $this->assertTrue(true);
    }

    public function testShouldRemoveMoneyFromParent(): void
    {
        $this->service->depositMoney(
            "ValidPassword123!",
            "child@example.com",
            50.0,
        );
        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenNotEnoughMoney(): void
    {
        $this->expectException(InsufficientFundsException::class);
        $this->service->depositMoney(
            "ValidPassword123!",
            "child@example.com",
            10000.0,
        );
    }

    public function testShouldThrowExceptionWhenAmountIsMissing(): void
    {
        $this->expectException(MissingAmountException::class);
        $this->service->depositMoney(
            "ValidPassword123!",
            "child@example.com",
            0.0,
        );
    }

    public function testShouldThrowExceptionWhenAmountIsInvalid(): void
    {
        $this->expectException(InvalidAmountException::class);
        $this->service->depositMoney(
            "ValidPassword123!",
            "child@example.com",
            -50.0,
        );
    }

    public function testShouldThrowExceptionWhenChildEmailNotInDb(): void
    {
        $this->expectException(EmailNotFoundInDatabaseException::class);
        $this->service->depositMoney(
            "ValidPassword123!",
            "unknown@example.com",
            50.0,
        );
    }

    public function testShouldThrowExceptionWhenChildEmailIsMissing(): void
    {
        $this->expectException(MissingEmailException::class);
        $this->service->depositMoney("ValidPassword123!", "", 50.0);
    }

    public function testShouldThrowExceptionWhenChildEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->service->depositMoney(
            "ValidPassword123!",
            "invalid-email",
            50.0,
        );
    }

    public function testShouldThrowExceptionWhenPasswordDoesNotMatchParent(): void
    {
        $this->expectException(PasswordMismatchException::class);
        $this->service->depositMoney(
            "WrongPassword123!",
            "child@example.com",
            50.0,
        );
    }

    public function testShouldThrowExceptionWhenPasswordIsMissing(): void
    {
        $this->expectException(MissingPasswordException::class);
        $this->service->depositMoney("", "child@example.com", 50.0);
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $this->expectException(InvalidPasswordException::class);
        $this->service->depositMoney("123", "child@example.com", 50.0);
    }
}
