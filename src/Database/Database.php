<?php

namespace MyWeeklyAllowance\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    private static string $dbPath =
        __DIR__ . "/../../data/myweeklyallowance.db";

    // Input: void | Output: PDO
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dataDir = dirname(self::$dbPath);
                if (!is_dir($dataDir)) {
                    mkdir($dataDir, 0777, true);
                }

                self::$connection = new PDO("sqlite:" . self::$dbPath);
                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION,
                );
                self::$connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC,
                );

                self::initializeSchema();
            } catch (PDOException $e) {
                throw new \RuntimeException(
                    "Database connection failed: " . $e->getMessage(),
                );
            }
        }

        return self::$connection;
    }

    // Input: void | Output: void
    private static function initializeSchema(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                name TEXT NOT NULL,
                firstname TEXT NOT NULL,
                user_type TEXT NOT NULL,
                balance REAL NOT NULL DEFAULT 0.0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS children (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                parent_email TEXT NOT NULL,
                name TEXT NOT NULL,
                firstname TEXT NOT NULL,
                balance REAL NOT NULL DEFAULT 0.0,
                weekly_allowance REAL NOT NULL DEFAULT 0.0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (email) REFERENCES users(email),
                FOREIGN KEY (parent_email) REFERENCES users(email)
            );

            CREATE TABLE IF NOT EXISTS expenses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                child_email TEXT NOT NULL,
                amount REAL NOT NULL,
                description TEXT,
                is_saved BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (child_email) REFERENCES users(email)
            );

            CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
            CREATE INDEX IF NOT EXISTS idx_children_email ON children(email);
        ";

        self::$connection->exec($sql);
    }

    // Input: void | Output: void
    public static function initializeDefaultData(): void
    {
        $db = self::getConnection();

        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();

        if ($result["count"] == 0) {
            $users = [
                [
                    "email" => "parent@example.com",
                    "password" => "ValidPassword123!",
                    "name" => "Parent",
                    "firstname" => "Test",
                    "user_type" => "parent",
                    "balance" => 1000.0,
                ],
                [
                    "email" => "child@example.com",
                    "password" => "ValidPassword123!",
                    "name" => "Child",
                    "firstname" => "Test",
                    "user_type" => "child",
                    "balance" => 100.0,
                ],
                [
                    "email" => "existing@example.com",
                    "password" => "ValidPassword123!",
                    "name" => "Existing",
                    "firstname" => "User",
                    "user_type" => "child",
                    "balance" => 0.0,
                ],
            ];

            $stmt = $db->prepare("
                INSERT INTO users (email, password, name, firstname, user_type, balance)
                VALUES (:email, :password, :name, :firstname, :user_type, :balance)
            ");

            foreach ($users as $user) {
                $stmt->execute($user);
            }

            $childStmt = $db->prepare("
                INSERT INTO children (email, parent_email, name, firstname, balance, weekly_allowance)
                VALUES (:email, :parent_email, :name, :firstname, :balance, :weekly_allowance)
            ");

            $childStmt->execute([
                "email" => "child@example.com",
                "parent_email" => "parent@example.com",
                "name" => "Child",
                "firstname" => "Test",
                "balance" => 100.0,
                "weekly_allowance" => 0.0,
            ]);

            $expenseStmt = $db->prepare("
                INSERT INTO expenses (id, child_email, amount, description, is_saved)
                VALUES (1, 'child@example.com', 10.0, 'Test expense', 0)
            ");
            $expenseStmt->execute();
        }
    }

    // Input: void | Output: void
    public static function resetForTesting(): void
    {
        self::$connection = null;
        if (file_exists(self::$dbPath)) {
            unlink(self::$dbPath);
        }
    }

    // Input: void | Output: void
    public static function setTestMode(): void
    {
        self::$dbPath = __DIR__ . "/../../data/test.db";
        self::$connection = null;
    }
}
