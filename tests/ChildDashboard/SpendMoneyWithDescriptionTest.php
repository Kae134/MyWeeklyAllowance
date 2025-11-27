<?php

namespace MyWeeklyAllowance\Tests\ChildDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ChildDashboardService;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Database\Database;
use MyWeeklyAllowance\Exception\InsufficientFundsException;
use MyWeeklyAllowance\Exception\InvalidPasswordException;
use MyWeeklyAllowance\Exception\MissingPasswordException;
use MyWeeklyAllowance\Exception\InvalidAmountException;
use MyWeeklyAllowance\Exception\MissingAmountException;

class SpendMoneyWithDescriptionTest extends TestCase
{
    private ChildDashboardService $childService;
    private ParentDashboardService $parentService;
    private UserRepository $userRepository;
    private const CHILD_EMAIL = "child@test.com";
    private const CHILD_PASSWORD = "ChildPassword123!";
    private const PARENT_EMAIL = "parent@test.com";

    protected function setUp(): void
    {
        Database::resetForTesting();
        Database::setTestMode();

        $this->childService = new ChildDashboardService();
        $this->parentService = new ParentDashboardService(self::PARENT_EMAIL);
        $this->userRepository = new UserRepository();

        // Créer un enfant avec de l'argent
        $this->parentService->createChild(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            "Doe",
            "Alice"
        );

        // Ajouter de l'argent à l'enfant
        $this->userRepository->updateUserBalance(self::CHILD_EMAIL, 100.0);
        $this->userRepository->updateChildBalance(self::CHILD_EMAIL, 100.0);
    }

    protected function tearDown(): void
    {
        Database::resetForTesting();
    }

    public function testShouldRemoveMoneyFromChildAndCreateExpense(): void
    {
        $initialBalance = $this->userRepository->getUserBalance(self::CHILD_EMAIL);

        $expenseId = $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            50.0,
            "Achat de fournitures"
        );

        $newBalance = $this->userRepository->getUserBalance(self::CHILD_EMAIL);

        $this->assertEquals(50.0, $initialBalance - $newBalance);
        $this->assertIsInt($expenseId);
        $this->assertGreaterThan(0, $expenseId);
    }

    public function testShouldReturnExpenseId(): void
    {
        $expenseId = $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            25.0,
            "Sortie au cinéma"
        );

        $this->assertIsInt($expenseId);
        $this->assertGreaterThan(0, $expenseId);

        // Vérifier que l'expense existe
        $this->assertTrue($this->userRepository->expenseExists($expenseId));
    }

    public function testShouldThrowExceptionWhenNotEnoughMoney(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            200.0,
            "Dépense trop élevée"
        );
    }

    public function testShouldThrowExceptionWhenAmountIsMissing(): void
    {
        $this->expectException(MissingAmountException::class);

        $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            0.0,
            "Description"
        );
    }

    public function testShouldThrowExceptionWhenAmountIsInvalid(): void
    {
        $this->expectException(InvalidAmountException::class);

        $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            -10.0,
            "Montant négatif"
        );
    }

    public function testShouldThrowExceptionWhenPasswordIsMissing(): void
    {
        $this->expectException(MissingPasswordException::class);

        $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            "",
            10.0,
            "Description"
        );
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $this->expectException(InvalidPasswordException::class);

        $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            "123",
            10.0,
            "Description"
        );
    }

    public function testShouldAcceptEmptyDescription(): void
    {
        $expenseId = $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            10.0,
            ""
        );

        $this->assertIsInt($expenseId);
        $this->assertGreaterThan(0, $expenseId);
    }

    public function testShouldSaveDescriptionCorrectly(): void
    {
        $description = "Achat de livre pour l'école";

        $expenseId = $this->childService->spendMoneyWithDescription(
            self::CHILD_EMAIL,
            self::CHILD_PASSWORD,
            15.50,
            $description
        );

        // Vérifier que la description est dans la base
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT description FROM expenses WHERE id = :id");
        $stmt->execute(["id" => $expenseId]);
        $result = $stmt->fetch();

        $this->assertEquals($description, $result["description"]);
    }
}
