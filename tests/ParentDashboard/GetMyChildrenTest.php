<?php

namespace MyWeeklyAllowance\Tests\ParentDashboard;

use PHPUnit\Framework\TestCase;
use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Database\Database;

class GetMyChildrenTest extends TestCase
{
    protected function setUp(): void
    {
        Database::resetForTesting();
        Database::setTestMode();
    }

    protected function tearDown(): void
    {
        Database::resetForTesting();
    }

    public function testShouldReturnOnlyParentOwnChildren(): void
    {
        $userRepo = new UserRepository();

        // Créer les parents dans la base de données
        $userRepo->createUser([
            "email" => "parent1@test.com",
            "password" => "ValidPassword123!",
            "name" => "Parent1",
            "firstname" => "Test1",
            "userType" => "parent",
            "balance" => 0.0,
        ]);

        $userRepo->createUser([
            "email" => "parent2@test.com",
            "password" => "ValidPassword123!",
            "name" => "Parent2",
            "firstname" => "Test2",
            "userType" => "parent",
            "balance" => 0.0,
        ]);

        // Créer les services
        $parentService1 = new ParentDashboardService("parent1@test.com");
        $parentService2 = new ParentDashboardService("parent2@test.com");

        // Parent 1 crée 2 enfants
        $parentService1->createChild(
            "child1@test.com",
            "ValidPassword123!",
            "Doe",
            "Alice",
        );

        $parentService1->createChild(
            "child2@test.com",
            "ValidPassword123!",
            "Doe",
            "Bob",
        );

        // Parent 2 crée 1 enfant
        $parentService2->createChild(
            "child3@test.com",
            "ValidPassword123!",
            "Smith",
            "Charlie",
        );

        // Vérifier que parent1 voit uniquement ses 2 enfants
        $children1 = $parentService1->getMyChildren();

        $this->assertCount(2, $children1);
        $this->assertEquals("child1@test.com", $children1[0]["email"]);
        $this->assertEquals("child2@test.com", $children1[1]["email"]);

        // Vérifier que parent2 voit uniquement son enfant
        $children2 = $parentService2->getMyChildren();

        $this->assertCount(1, $children2);
        $this->assertEquals("child3@test.com", $children2[0]["email"]);
    }

    public function testShouldReturnEmptyArrayWhenParentHasNoChildren(): void
    {
        $userRepo = new UserRepository();

        // Créer un parent sans enfants
        $userRepo->createUser([
            "email" => "parentalone@test.com",
            "password" => "ValidPassword123!",
            "name" => "Alone",
            "firstname" => "Parent",
            "userType" => "parent",
            "balance" => 0.0,
        ]);

        $parentService = new ParentDashboardService("parentalone@test.com");
        $children = $parentService->getMyChildren();

        $this->assertIsArray($children);
        $this->assertCount(0, $children);
    }

    public function testShouldReturnChildrenOrderedByName(): void
    {
        $userRepo = new UserRepository();

        // Créer un parent
        $userRepo->createUser([
            "email" => "parentorder@test.com",
            "password" => "ValidPassword123!",
            "name" => "Order",
            "firstname" => "Parent",
            "userType" => "parent",
            "balance" => 0.0,
        ]);

        $parentService = new ParentDashboardService("parentorder@test.com");

        // Créer des enfants dans un ordre différent
        $parentService->createChild(
            "child1@test.com",
            "ValidPassword123!",
            "Zorro",
            "Alice",
        );

        $parentService->createChild(
            "child2@test.com",
            "ValidPassword123!",
            "Albert",
            "Bob",
        );

        $parentService->createChild(
            "child3@test.com",
            "ValidPassword123!",
            "Martin",
            "Charlie",
        );

        $children = $parentService->getMyChildren();

        // Vérifier l'ordre alphabétique par nom
        $this->assertEquals("Albert", $children[0]["name"]);
        $this->assertEquals("Martin", $children[1]["name"]);
        $this->assertEquals("Zorro", $children[2]["name"]);
    }
}
