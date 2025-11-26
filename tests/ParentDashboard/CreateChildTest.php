<?php

namespace MyWeeklyAllowance\Tests\ParentDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\DTO\ChildData;
use MyWeeklyAllowance\Exception\InvalidEmailException;
use MyWeeklyAllowance\Exception\MissingEmailException;
use MyWeeklyAllowance\Exception\EmailAlreadyInUseException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;
use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\MissingNameException;
use MyWeeklyAllowance\Exception\InvalidNameException;
use MyWeeklyAllowance\Exception\MissingFirstnameException;
use MyWeeklyAllowance\Exception\InvalidFirstnameException;

class CreateChildTest extends TestCase
{
    private ParentDashboardService $service;

    protected function setUp(): void
    {
        $this->service = new ParentDashboardService();
    }

    public function testShouldCreateChildWithValidData(): void
    {
        $result = $this->service->createChild(
            "child@example.com",
            "ValidPassword123!",
            "Doe",
            "Jane",
        );

        $this->assertInstanceOf(ChildData::class, $result);
        $this->assertEquals("child@example.com", $result->email);
        $this->assertEquals("Doe", $result->name);
        $this->assertEquals("Jane", $result->firstname);
        $this->assertEquals(0.0, $result->balance);
        $this->assertEquals(0.0, $result->weeklyAllowance);
    }

    public function testShouldThrowExceptionWhenEmailIsAlreadyInUse(): void
    {
        $this->expectException(EmailAlreadyInUseException::class);
        $this->service->createChild(
            "existing@example.com",
            "ValidPassword123!",
            "Doe",
            "Jane",
        );
    }

    public function testShouldThrowExceptionWhenEmailIsMissing(): void
    {
        $this->expectException(MissingEmailException::class);
        $this->service->createChild("", "ValidPassword123!", "Doe", "Jane");
    }

    public function testShouldThrowExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->service->createChild(
            "invalid-email",
            "ValidPassword123!",
            "Doe",
            "Jane",
        );
    }

    public function testShouldThrowExceptionWhenPasswordIsMissing(): void
    {
        $this->expectException(MissingPasswordException::class);
        $this->service->createChild("child@example.com", "", "Doe", "Jane");
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $this->expectException(InvalidPasswordException::class);
        $this->service->createChild("child@example.com", "123", "Doe", "Jane");
    }

    public function testShouldThrowExceptionWhenNameIsMissing(): void
    {
        $this->expectException(MissingNameException::class);
        $this->service->createChild(
            "child@example.com",
            "ValidPassword123!",
            "",
            "Jane",
        );
    }

    public function testShouldThrowExceptionWhenNameIsInvalid(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->service->createChild(
            "child@example.com",
            "ValidPassword123!",
            "123",
            "Jane",
        );
    }

    public function testShouldThrowExceptionWhenFirstnameIsMissing(): void
    {
        $this->expectException(MissingFirstnameException::class);
        $this->service->createChild(
            "child@example.com",
            "ValidPassword123!",
            "Doe",
            "",
        );
    }

    public function testShouldThrowExceptionWhenFirstnameIsInvalid(): void
    {
        $this->expectException(InvalidFirstnameException::class);
        $this->service->createChild(
            "child@example.com",
            "ValidPassword123!",
            "Doe",
            "123",
        );
    }
}
