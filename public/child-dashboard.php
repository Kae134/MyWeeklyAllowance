<?php
session_start();
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/auth-check.php";

use MyWeeklyAllowance\ChildDashboardService;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Database\Database;

if ($_SESSION["user"]["userType"] !== "child") {
    header("Location: parent-dashboard.php");
    exit();
}

Database::getConnection();
$childService = new ChildDashboardService($_SESSION["user"]["email"]);
$userRepo = new UserRepository();

$error = "";
$success = "";

// Traitement des actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    try {
        switch ($action) {
            case "spendMoney":
                $amount = floatval($_POST["amount"] ?? 0);
                $description = trim($_POST["description"] ?? "");
                $password = $_POST["password"] ?? "";

                $childService->spendMoneyWithDescription(
                    $_SESSION["user"]["email"],
                    $password,
                    $amount,
                    $description,
                );

                $success = "Dépense de {$amount}€ enregistrée avec succès !";
                refreshUserBalance();
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer les informations de l'enfant
$childData = $userRepo->findChildByEmail($_SESSION["user"]["email"]);
$weeklyAllowance = $childData ? $childData["weeklyAllowance"] : 0.0;

// Récupérer l'historique des dépenses
$db = Database::getConnection();
$stmt = $db->prepare("SELECT id, amount, description, is_saved, created_at
                      FROM expenses
                      WHERE child_email = :email
                      ORDER BY created_at DESC
                      LIMIT 50");
$stmt->execute(["email" => $_SESSION["user"]["email"]]);
$expenses = $stmt->fetchAll();

// Calculer les statistiques
$stmt = $db->prepare("SELECT
                        SUM(amount) as total_spent,
                        COUNT(*) as total_transactions
                      FROM expenses
                      WHERE child_email = :email");
$stmt->execute(["email" => $_SESSION["user"]["email"]]);
$stats = $stmt->fetch();
$totalSpent = $stats["total_spent"] ?? 0.0;
$totalTransactions = $stats["total_transactions"] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enfant - MyWeeklyAllowance</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            color: #000000;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            border: 2px solid #000000;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8em;
            text-transform: uppercase;
        }

        .header-info {
            text-align: right;
        }

        .balance {
            font-size: 1.8em;
            font-weight: bold;
            margin: 5px 0;
            color: #000000;
        }

        .allowance {
            font-size: 1em;
            margin: 5px 0;
            color: #666666;
        }

        .logout-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #000000;
            color: #ffffff;
            text-decoration: none;
            margin-top: 10px;
        }

        .logout-btn:hover {
            background-color: #333333;
        }

        .error {
            background-color: #ffcccc;
            border: 1px solid #ff0000;
            padding: 15px;
            margin-bottom: 20px;
            color: #cc0000;
        }

        .success {
            background-color: #ccffcc;
            border: 1px solid #00cc00;
            padding: 15px;
            margin-bottom: 20px;
            color: #006600;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 2px solid #000000;
            padding: 20px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 0.9em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
        }

        .card {
            border: 2px solid #000000;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 1.2em;
            margin-bottom: 15px;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="password"],
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #000000;
            background-color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border: 2px solid #000000;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #000000;
            color: #ffffff;
            border: none;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
        }

        button:hover {
            background-color: #333333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000000;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #000000;
            color: #ffffff;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 0.85em;
            border: 1px solid #000000;
        }

        .status-pending {
            background-color: #ffffcc;
        }

        .status-saved {
            background-color: #ccffcc;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .header-info {
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard Enfant</h1>
                <p>Bienvenue <?= htmlspecialchars(
                    $_SESSION["user"]["firstname"],
                ) ?> <?= htmlspecialchars($_SESSION["user"]["name"]) ?></p>
            </div>
            <div class="header-info">
                <div class="balance"><?= number_format(
                    $_SESSION["user"]["balance"],
                    2,
                    ",",
                    " ",
                ) ?> €</div>
                <?php if ($weeklyAllowance > 0): ?>
                    <div class="allowance">Allocation hebdo: <?= number_format(
                        $weeklyAllowance,
                        2,
                        ",",
                        " ",
                    ) ?> €</div>
                <?php endif; ?>
                <p>Email: <?= htmlspecialchars(
                    $_SESSION["user"]["email"],
                ) ?></p>
                <a href="logout.php" class="logout-btn">Déconnexion</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <strong>Succès :</strong> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3>Solde Actuel</h3>
                <div class="value"><?= number_format(
                    $_SESSION["user"]["balance"],
                    2,
                    ",",
                    " ",
                ) ?> €</div>
            </div>
            <div class="stat-card">
                <h3>Total Dépensé</h3>
                <div class="value"><?= number_format(
                    $totalSpent,
                    2,
                    ",",
                    " ",
                ) ?> €</div>
            </div>
            <div class="stat-card">
                <h3>Nombre de Transactions</h3>
                <div class="value"><?= $totalTransactions ?></div>
            </div>
        </div>

        <div class="card">
            <h2>▸ Enregistrer une dépense</h2>
            <form method="POST">
                <input type="hidden" name="action" value="spendMoney">
                <div class="form-group">
                    <label for="amount">Montant dépensé (€) *</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                    <label for="description">Description de la dépense *</label>
                    <textarea id="description" name="description" required placeholder="Ex: Achat de fournitures scolaires, Sortie au cinéma, etc."></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Votre mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Enregistrer la dépense</button>
            </form>
        </div>

        <div class="card">
            <h2>▸ Historique des dépenses</h2>
            <?php if (count($expenses) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Description</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?= date(
                                    "d/m/Y H:i",
                                    strtotime($expense["created_at"]),
                                ) ?></td>
                                <td><?= number_format(
                                    $expense["amount"],
                                    2,
                                    ",",
                                    " ",
                                ) ?> €</td>
                                <td><?= htmlspecialchars(
                                    $expense["description"] ?? "-",
                                ) ?></td>
                                <td>
                                    <?php if ($expense["is_saved"]): ?>
                                        <span class="status-badge status-saved">✓ Validée</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">⏳ En attente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucune dépense enregistrée pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
