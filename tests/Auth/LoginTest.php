<?php

namespace MyWeeklyAllowance\Tests\Auth;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\AuthService;
use MyWeeklyAllowance\DTO\UserData;
use MyWeeklyAllowance\Exception\InvalidEmailException;
use MyWeeklyAllowance\Exception\MissingEmailException;
use MyWeeklyAllowance\Exception\EmailNotFoundInDatabaseException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;
use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\PasswordMismatchException;

class LoginTest extends TestCase
{
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->authService = new AuthService();
    }

    public function testShouldLoginAsParentAndReturnCorrectData(): void
    {
        $result = $this->authService->login(
            "parent@example.com",
            "ValidPassword123!",
        );

        $this->assertInstanceOf(UserData::class, $result);
        $this->assertEquals("parent@example.com", $result->email);
        $this->assertEquals("parent", $result->userType);
        $this->assertIsFloat($result->balance);
    }

    public function testShouldLoginAsChildAndReturnCorrectData(): void
    {
        $result = $this->authService->login(
            "child@example.com",
            "ValidPassword123!",
        );

        $this->assertInstanceOf(UserData::class, $result);
        $this->assertEquals("child@example.com", $result->email);
        $this->assertEquals("child", $result->userType);
        $this->assertIsFloat($result->balance);
    }

    public function testShouldThrowExceptionWhenEmailIsMissing(): void
    {
        $this->expectException(MissingEmailException::class);
        $this->authService->login("", "ValidPassword123!");
    }

    public function testShouldThrowExceptionWhenEmailIsNotInDb(): void
    {
        $this->expectException(EmailNotFoundInDatabaseException::class);
        $this->authService->login("unknown@example.com", "ValidPassword123!");
    }

    public function testShouldThrowExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->authService->login("invalid-email", "ValidPassword123!");
    }

    public function testShouldThrowExceptionWhenPasswordIsMissing(): void
    {
        $this->expectException(MissingPasswordException::class);
        $this->authService->login("parent@example.com", "");
    }

    public function testShouldThrowExceptionWhenPasswordDoesNotMatchUserEmail(): void
    {
        $this->expectException(PasswordMismatchException::class);
        $this->authService->login("parent@example.com", "WrongPassword123!");
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $this->expectException(InvalidPasswordException::class);
        $this->authService->login("parent@example.com", "123");
    }
}
