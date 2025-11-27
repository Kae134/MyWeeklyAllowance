<?php

namespace MyWeeklyAllowance;

use MyWeeklyAllowance\Interface\ParentDashboardServiceInterface;
use MyWeeklyAllowance\DTO\ChildData;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Validator\EmailValidator;
use MyWeeklyAllowance\Validator\PasswordValidator;
use MyWeeklyAllowance\Validator\NameValidator;
use MyWeeklyAllowance\Validator\FirstnameValidator;
use MyWeeklyAllowance\Validator\AmountValidator;
use MyWeeklyAllowance\Exception\EmailAlreadyInUseException;
use MyWeeklyAllowance\Exception\EmailNotFoundInDatabaseException;
use MyWeeklyAllowance\Exception\PasswordMismatchException;
use MyWeeklyAllowance\Exception\InsufficientFundsException;
use MyWeeklyAllowance\Exception\MissingExpenseIdException;
use MyWeeklyAllowance\Exception\ExpenseIdNotFoundInDatabaseException;

class ParentDashboardService implements ParentDashboardServiceInterface
{
    private UserRepository $userRepository;
    private string $parentEmail;

    public function __construct(string $parentEmail)
    {
        $this->userRepository = new UserRepository();
        $this->parentEmail = $parentEmail;
    }

    // Input: email (string), password (string), name (string), firstname (string) | Output: ChildData
    public function createChild(
        string $email,
        string $password,
        string $name,
        string $firstname,
    ): ChildData {
        EmailValidator::validate($email);
        PasswordValidator::validate($password);
        NameValidator::validate($name);
        FirstnameValidator::validate($firstname);

        $existingUser = $this->userRepository->findByEmail($email);
        if (
            $existingUser !== null &&
            $this->UserRepository->emailExists($email)
        ) {
            throw new EmailAlreadyInUseException("Email is already in use");
        }

        $userData = [
            "email" => $email,
            "password" => $password,
            "name" => $name,
            "firstname" => $firstname,
            "userType" => "child",
            "balance" => 0.0,
        ];

        $this->userRepository->createUser($userData);

        $childData = [
            "email" => $email,
            "parentEmail" => $this->parentEmail,
            "name" => $name,
            "firstname" => $firstname,
            "balance" => 0.0,
            "weeklyAllowance" => 0.0,
        ];

        $this->userRepository->createChild($childData);

        return new ChildData($email, $name, $firstname, 0.0, 0.0);
    }

    // Input: void | Output: array
    public function getMyChildren(): array
    {
        return $this->userRepository->getChildrenByParent($this->parentEmail);
    }

    // Input: parentPassword (string), childEmail (string), amount (float) | Output: void
    public function depositMoney(
        string $parentPassword,
        string $childEmail,
        float $amount,
    ): void {
        PasswordValidator::validate($parentPassword);
        EmailValidator::validate($childEmail);
        AmountValidator::validate($amount);

        $parent = $this->userRepository->findByEmail($this->parentEmail);
        if ($parent === null || $parent["password"] !== $parentPassword) {
            throw new PasswordMismatchException(
                "Password does not match parent",
            );
        }

        $child = $this->userRepository->findByEmail($childEmail);
        if ($child === null) {
            throw new EmailNotFoundInDatabaseException("Child email not found");
        }

        $parentBalance = $this->userRepository->getUserBalance(
            $this->parentEmail,
        );
        if ($parentBalance < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }

        $this->userRepository->updateUserBalance(
            $this->parentEmail,
            $parentBalance - $amount,
        );

        $childBalance = $this->userRepository->getUserBalance($childEmail);
        $this->userRepository->updateUserBalance(
            $childEmail,
            $childBalance + $amount,
        );

        $this->userRepository->updateChildBalance(
            $childEmail,
            $childBalance + $amount,
        );
    }

    // Input: expenseId (int) | Output: void
    public function saveExpense(int $expenseId): void
    {
        if ($expenseId === 0) {
            throw new MissingExpenseIdException("Expense ID is required");
        }

        if (!$this->userRepository->expenseExists($expenseId)) {
            throw new ExpenseIdNotFoundInDatabaseException(
                "Expense ID not found",
            );
        }

        $this->userRepository->saveExpense($expenseId);
    }

    // Input: childEmail (string), amount (float) | Output: void
    public function fixAllowance(string $childEmail, float $amount): void
    {
        AmountValidator::validate($amount);

        $this->userRepository->updateChildAllowance($childEmail, $amount);
    }

    // Input: amount (float) | Output: void
    public function addMoneyToAccount(float $amount): void
    {
        AmountValidator::validate($amount);

        $currentBalance = $this->userRepository->getUserBalance(
            $this->parentEmail,
        );
        $this->userRepository->updateUserBalance(
            $this->parentEmail,
            $currentBalance + $amount,
        );
    }
}
