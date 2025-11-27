<?php

namespace MyWeeklyAllowance\Tests\ParentDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Exception\MissingExpenseIdException;
use MyWeeklyAllowance\Exception\ExpenseIdNotFoundInDatabaseException;

class SaveExpenseTest extends TestCase
{
    private ParentDashboardService $service;

    protected function setUp(): void
    {
        $this->service = new ParentDashboardService();
    }

    public function testShouldSaveChildExpense(): void
    {
        $this->service->saveExpense(1);
        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenExpenseIdIsMissing(): void
    {
        $this->expectException(MissingExpenseIdException::class);
        $this->service->saveExpense(0);
    }

    public function testShouldThrowExceptionWhenExpenseIdNotInDb(): void
    {
        $this->expectException(ExpenseIdNotFoundInDatabaseException::class);
        $this->service->saveExpense(99999);
    }
}
