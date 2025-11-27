<?php

namespace MyWeeklyAllowance\Tests\ParentDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Exception\InvalidAmountException;
use MyWeeklyAllowance\Exception\MissingAmountException;

class FixAllowanceTest extends TestCase
{
    private ParentDashboardService $service;

    protected function setUp(): void
    {
        $this->service = new ParentDashboardService();
    }

    public function testShouldFixAllowanceToChild(): void
    {
        $this->service->fixAllowance("child@example.com", 20.0);
        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenAmountIsMissing(): void
    {
        $this->expectException(MissingAmountException::class);
        $this->service->fixAllowance("child@example.com", 0.0);
    }

    public function testShouldThrowExceptionWhenAmountIsInvalid(): void
    {
        $this->expectException(InvalidAmountException::class);
        $this->service->fixAllowance("child@example.com", -20.0);
    }
}
