<?php

namespace MyWeeklyAllowance\Repository;

use MyWeeklyAllowance\Database\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        Database::initializeDefaultData();
    }

    // Input: email (string) | Output: array|null
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(["email" => $email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    // Input: email (string) | Output: bool
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    // Input: userData (array) | Output: void
    public function createUser(array $userData): void
    {
        $stmt = $this->db->prepare("
            INSERT OR REPLACE INTO users (email, password, name, firstname, user_type, balance)
            VALUES (:email, :password, :name, :firstname, :user_type, :balance)
        ");

        $stmt->execute([
            "email" => $userData["email"],
            "password" => $userData["password"],
            "name" => $userData["name"],
            "firstname" => $userData["firstname"],
            "user_type" => $userData["userType"],
            "balance" => $userData["balance"],
        ]);
    }

    // Input: email (string), balance (float) | Output: void
    public function updateUserBalance(string $email, float $balance): void
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET balance = :balance WHERE email = :email",
        );
        $stmt->execute(["balance" => $balance, "email" => $email]);
    }

    // Input: email (string) | Output: float
    public function getUserBalance(string $email): float
    {
        $stmt = $this->db->prepare(
            "SELECT balance FROM users WHERE email = :email",
        );
        $stmt->execute(["email" => $email]);
        $result = $stmt->fetch();

        return $result ? (float) $result["balance"] : 0.0;
    }

    // Input: email (string) | Output: array|null
    public function findChildByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM children WHERE email = :email",
        );
        $stmt->execute(["email" => $email]);
        $result = $stmt->fetch();

        if ($result) {
            return [
                "email" => $result["email"],
                "name" => $result["name"],
                "firstname" => $result["firstname"],
                "balance" => (float) $result["balance"],
                "weeklyAllowance" => (float) $result["weekly_allowance"],
            ];
        }

        return null;
    }

    // Input: childData (array) | Output: void
    public function createChild(array $childData): void
    {
        $stmt = $this->db->prepare("
            INSERT OR REPLACE INTO children (email, parent_email, name, firstname, balance, weekly_allowance)
            VALUES (:email, :parent_email, :name, :firstname, :balance, :weekly_allowance)
        ");

        $stmt->execute([
            "email" => $childData["email"],
            "parent_email" => $childData["parentEmail"],
            "name" => $childData["name"],
            "firstname" => $childData["firstname"],
            "balance" => $childData["balance"],
            "weekly_allowance" => $childData["weeklyAllowance"],
        ]);
    }

    // Input: parentEmail (string) | Output: array
    public function getChildrenByParent(string $parentEmail): array
    {
        $stmt = $this->db->prepare("
            SELECT email, name, firstname, balance, weekly_allowance
            FROM children
            WHERE parent_email = :parent_email
            ORDER BY name, firstname
        ");
        $stmt->execute(["parent_email" => $parentEmail]);
        return $stmt->fetchAll();
    }

    // Input: email (string), balance (float) | Output: void
    public function updateChildBalance(string $email, float $balance): void
    {
        $stmt = $this->db->prepare(
            "UPDATE children SET balance = :balance WHERE email = :email",
        );
        $stmt->execute(["balance" => $balance, "email" => $email]);
    }

    // Input: email (string), allowance (float) | Output: void
    public function updateChildAllowance(string $email, float $allowance): void
    {
        $stmt = $this->db->prepare(
            "UPDATE children SET weekly_allowance = :allowance WHERE email = :email",
        );
        $stmt->execute(["allowance" => $allowance, "email" => $email]);
    }

    // Input: expenseId (int) | Output: bool
    public function expenseExists(int $expenseId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM expenses WHERE id = :id",
        );
        $stmt->execute(["id" => $expenseId]);
        $result = $stmt->fetch();

        return $result["count"] > 0;
    }

    // Input: expenseId (int) | Output: void
    public function saveExpense(int $expenseId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE expenses SET is_saved = 1 WHERE id = :id",
        );
        $stmt->execute(["id" => $expenseId]);
    }

    // Input: childEmail (string), amount (float), description (string) | Output: int
    public function createExpense(
        string $childEmail,
        float $amount,
        string $description = "",
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO expenses (child_email, amount, description, is_saved)
            VALUES (:child_email, :amount, :description, 0)
        ");
        $stmt->execute([
            "child_email" => $childEmail,
            "amount" => $amount,
            "description" => $description,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Input: void | Output: void
    public static function resetForTesting(): void
    {
        Database::resetForTesting();
    }
}
