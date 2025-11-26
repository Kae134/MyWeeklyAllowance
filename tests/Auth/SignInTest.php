<?php

namespace MyWeeklyAllowance\Tests\Auth;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\AuthService;
use MyWeeklyAllowance\DTO\UserData;
use MyWeeklyAllowance\Exception\InvalidEmailException;
use MyWeeklyAllowance\Exception\MissingEmailException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;
use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\MissingNameException;
use MyWeeklyAllowance\Exception\InvalidNameException;
use MyWeeklyAllowance\Exception\MissingFirstnameException;
use MyWeeklyAllowance\Exception\InvalidFirstnameException;

class SignInTest extends TestCase
{
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->authService = new AuthService();
    }

    public function testShouldSigninAndReturnParentDataWithValidCredentials(): void
    {
        $result = $this->authService->signIn(
            "newparent@example.com",
            "ValidPassword123!",
            "Doe",
            "John",
            "parent",
        );

        $this->assertInstanceOf(UserData::class, $result);
        $this->assertEquals("newparent@example.com", $result->email);
        $this->assertEquals("Doe", $result->name);
        $this->assertEquals("John", $result->firstname);
        $this->assertEquals("parent", $result->userType);
        $this->assertEquals(0.0, $result->balance);
    }

    public function testShouldSigninAndReturnChildDataWithValidCredentials(): void
    {
        $result = $this->authService->signIn(
            "newchild@example.com",
            "ValidPassword123!",
            "Smith",
            "Emma",
            "child",
        );

        $this->assertInstanceOf(UserData::class, $result);
        $this->assertEquals("newchild@example.com", $result->email);
        $this->assertEquals("Smith", $result->name);
        $this->assertEquals("Emma", $result->firstname);
        $this->assertEquals("child", $result->userType);
        $this->assertEquals(0.0, $result->balance);
    }

    public function testShouldThrowExceptionWhenEmailIsMissing(): void
    {
        $this->expectException(MissingEmailException::class);
        $this->authService->signIn(
            "",
            "ValidPassword123!",
            "Doe",
            "John",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->authService->signIn(
            "invalid-email",
            "ValidPassword123!",
            "Doe",
            "John",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenPasswordMissing(): void
    {
        $this->expectException(MissingPasswordException::class);
        $this->authService->signIn(
            "parent@example.com",
            "",
            "Doe",
            "John",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $this->expectException(InvalidPasswordException::class);
        $this->authService->signIn(
            "parent@example.com",
            "123",
            "Doe",
            "John",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenNameIsMissing(): void
    {
        $this->expectException(MissingNameException::class);
        $this->authService->signIn(
            "parent@example.com",
            "ValidPassword123!",
            "",
            "John",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenNameIsInvalid(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->authService->signIn(
            "parent@example.com",
            "ValidPassword123!",
            "123",
            "John",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenFirstnameIsMissing(): void
    {
        $this->expectException(MissingFirstnameException::class);
        $this->authService->signIn(
            "parent@example.com",
            "ValidPassword123!",
            "Doe",
            "",
            "parent",
        );
    }

    public function testShouldThrowExceptionWhenFirstnameIsInvalid(): void
    {
        $this->expectException(InvalidFirstnameException::class);
        $this->authService->signIn(
            "parent@example.com",
            "ValidPassword123!",
            "Doe",
            "123",
            "parent",
        );
    }
}
