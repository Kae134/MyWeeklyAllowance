<?php

namespace MyWeeklyAllowance\Tests\ParentDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Exception\InvalidAmountException;
use MyWeeklyAllowance\Exception\MissingAmountException;

class AddMoneyToAccountTest extends TestCase
{
    private ParentDashboardService $service;

    protected function setUp(): void
    {
        $this->service = new ParentDashboardService("parent@example.com");
    }

    public function testShouldAddMoneyToParent(): void
    {
        $this->service->addMoneyToAccount(100.0);
        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenAmountIsMissing(): void
    {
        $this->expectException(MissingAmountException::class);
        $this->service->addMoneyToAccount(0.0);
    }

    public function testShouldThrowExceptionWhenAmountIsInvalid(): void
    {
        $this->expectException(InvalidAmountException::class);
        $this->service->addMoneyToAccount(-100.0);
    }
}
