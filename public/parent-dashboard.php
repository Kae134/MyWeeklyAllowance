<?php
session_start();
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/auth-check.php";

use MyWeeklyAllowance\ParentDashboardService;
use MyWeeklyAllowance\Repository\UserRepository;
use MyWeeklyAllowance\Database\Database;

if ($_SESSION["user"]["userType"] !== "parent") {
    header("Location: child-dashboard.php");
    exit();
}

Database::getConnection();
$parentService = new ParentDashboardService($_SESSION["user"]["email"]);
$userRepo = new UserRepository();

$error = "";
$success = "";

// Traitement des actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    try {
        switch ($action) {
            case "addMoney":
                $amount = floatval($_POST["amount"] ?? 0);
                $parentService->addMoneyToAccount($amount);
                $success = "Montant de {$amount}€ ajouté à votre compte avec succès !";
                refreshUserBalance();
                break;

            case "createChild":
                $childData = $parentService->createChild(
                    $_POST["child_email"] ?? "",
                    $_POST["child_password"] ?? "",
                    $_POST["child_name"] ?? "",
                    $_POST["child_firstname"] ?? "",
                );
                $success = "Compte enfant créé avec succès pour {$childData->firstname} {$childData->name} !";
                break;

            case "depositMoney":
                $parentService->depositMoney(
                    $_POST["parent_password"] ?? "",
                    $_POST["child_email_transfer"] ?? "",
                    floatval($_POST["transfer_amount"] ?? 0),
                );
                $success = "Transfert effectué avec succès !";
                refreshUserBalance();
                break;

            case "fixAllowance":
                $parentService->fixAllowance(
                    $_POST["child_email_allowance"] ?? "",
                    floatval($_POST["allowance_amount"] ?? 0),
                );
                $success = "Allocation hebdomadaire configurée avec succès !";
                break;

            case "saveExpense":
                $parentService->saveExpense(intval($_POST["expense_id"] ?? 0));
                $success = "Dépense sauvegardée avec succès !";
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer la liste des enfants du parent connecté
$children = $parentService->getMyChildren();

// Récupérer les dépenses non sauvegardées des enfants du parent
$db = Database::getConnection();
$stmt = $db->prepare("SELECT e.id, e.child_email, e.amount, e.description, e.created_at, c.name, c.firstname
                    FROM expenses e
                    JOIN children c ON e.child_email = c.email
                    WHERE e.is_saved = 0 AND c.parent_email = :parent_email
                    ORDER BY e.created_at DESC");
$stmt->execute(["parent_email" => $_SESSION["user"]["email"]]);
$expenses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Parent - MyWeeklyAllowance</title>
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
            font-size: 1.5em;
            font-weight: bold;
            margin: 5px 0;
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

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            border: 2px solid #000000;
            padding: 20px;
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

        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #000000;
            background-color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        input:focus,
        select:focus {
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

        .children-list {
            border: 2px solid #000000;
            padding: 20px;
            margin-bottom: 20px;
        }

        .children-list h2 {
            font-size: 1.2em;
            margin-bottom: 15px;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
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

        .expense-actions {
            display: flex;
            gap: 5px;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            width: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard Parent</h1>
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

        <div class="grid">
            <div class="card">
                <h2>▸ Ajouter de l'argent à mon compte</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="addMoney">
                    <div class="form-group">
                        <label for="amount">Montant (€)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <button type="submit">Ajouter</button>
                </form>
            </div>

            <div class="card">
                <h2>▸ Créer un compte enfant</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="createChild">
                    <div class="form-group">
                        <label for="child_email">Email</label>
                        <input type="email" id="child_email" name="child_email" required>
                    </div>
                    <div class="form-group">
                        <label for="child_password">Mot de passe</label>
                        <input type="password" id="child_password" name="child_password" required>
                    </div>
                    <div class="form-group">
                        <label for="child_name">Nom</label>
                        <input type="text" id="child_name" name="child_name" required>
                    </div>
                    <div class="form-group">
                        <label for="child_firstname">Prénom</label>
                        <input type="text" id="child_firstname" name="child_firstname" required>
                    </div>
                    <button type="submit">Créer le compte</button>
                </form>
            </div>

            <div class="card">
                <h2>▸ Transférer de l'argent à un enfant</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="depositMoney">
                    <div class="form-group">
                        <label for="child_email_transfer">Sélectionner l'enfant</label>
                        <select id="child_email_transfer" name="child_email_transfer" required>
                            <option value="">-- Choisir un enfant --</option>
                            <?php foreach ($children as $child): ?>
                                <option value="<?= htmlspecialchars(
                                    $child["email"],
                                ) ?>">
                                    <?= htmlspecialchars(
                                        $child["firstname"],
                                    ) ?> <?= htmlspecialchars(
     $child["name"],
 ) ?> (<?= number_format($child["balance"], 2, ",", " ") ?> €)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transfer_amount">Montant (€)</label>
                        <input type="number" id="transfer_amount" name="transfer_amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="parent_password">Votre mot de passe</label>
                        <input type="password" id="parent_password" name="parent_password" required>
                    </div>
                    <button type="submit">Transférer</button>
                </form>
            </div>

            <div class="card">
                <h2>▸ Fixer une allocation hebdomadaire</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="fixAllowance">
                    <div class="form-group">
                        <label for="child_email_allowance">Sélectionner l'enfant</label>
                        <select id="child_email_allowance" name="child_email_allowance" required>
                            <option value="">-- Choisir un enfant --</option>
                            <?php foreach ($children as $child): ?>
                                <option value="<?= htmlspecialchars(
                                    $child["email"],
                                ) ?>">
                                    <?= htmlspecialchars(
                                        $child["firstname"],
                                    ) ?> <?= htmlspecialchars($child["name"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="allowance_amount">Montant hebdomadaire (€)</label>
                        <input type="number" id="allowance_amount" name="allowance_amount" step="0.01" min="0" required>
                    </div>
                    <button type="submit">Configurer</button>
                </form>
            </div>
        </div>

        <div class="children-list">
            <h2>▸ Liste des enfants</h2>
            <?php if (count($children) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Solde</th>
                            <th>Allocation hebdo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($children as $child): ?>
                            <tr>
                                <td><?= htmlspecialchars(
                                    $child["firstname"],
                                ) ?></td>
                                <td><?= htmlspecialchars($child["name"]) ?></td>
                                <td><?= htmlspecialchars(
                                    $child["email"],
                                ) ?></td>
                                <td><?= number_format(
                                    $child["balance"],
                                    2,
                                    ",",
                                    " ",
                                ) ?> €</td>
                                <td><?= number_format(
                                    $child["weekly_allowance"],
                                    2,
                                    ",",
                                    " ",
                                ) ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucun enfant enregistré pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="children-list">
            <h2>▸ Dépenses non validées</h2>
            <?php if (count($expenses) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Enfant</th>
                            <th>Montant</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?= date(
                                    "d/m/Y H:i",
                                    strtotime($expense["created_at"]),
                                ) ?></td>
                                <td><?= htmlspecialchars(
                                    $expense["firstname"],
                                ) ?> <?= htmlspecialchars(
     $expense["name"],
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
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="saveExpense">
                                        <input type="hidden" name="expense_id" value="<?= $expense[
                                            "id"
                                        ] ?>">
                                        <button type="submit" class="btn-small">Valider</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucune dépense à valider.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
