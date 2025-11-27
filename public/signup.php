<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use MyWeeklyAllowance\AuthService;
use MyWeeklyAllowance\Database\Database;

Database::getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $userType = $_POST['userType'] ?? 'parent';

    try {
        $authService = new AuthService();
        $userData = $authService->signIn($email, $password, $name, $firstname, $userType);

        $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
        header('Location: login.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - MyWeeklyAllowance</title>
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
            max-width: 500px;
            margin: 50px auto;
            border: 2px solid #000000;
            padding: 40px;
        }

        h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 40px;
            font-style: italic;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
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
            padding: 15px;
            background-color: #000000;
            color: #ffffff;
            border: none;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            background-color: #333333;
        }

        .error {
            background-color: #ffcccc;
            border: 1px solid #ff0000;
            padding: 15px;
            margin-bottom: 20px;
            color: #cc0000;
        }

        .link {
            text-align: center;
            margin-top: 20px;
        }

        .link a {
            color: #000000;
            text-decoration: underline;
        }

        .link a:hover {
            text-decoration: none;
        }

        .info-box {
            border: 1px solid #000000;
            padding: 15px;
            margin: 20px 0;
            background-color: #f5f5f5;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MyWeeklyAllowance</h1>
        <p class="subtitle">Inscription</p>

        <?php if ($error): ?>
            <div class="error">
                <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="userType">Type de compte</label>
                <select id="userType" name="userType" required>
                    <option value="parent" <?= (($_POST['userType'] ?? 'parent') === 'parent') ? 'selected' : '' ?>>Parent</option>
                    <option value="child" <?= (($_POST['userType'] ?? '') === 'child') ? 'selected' : '' ?>>Enfant</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input type="text" id="firstname" name="firstname" required value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
            </div>

            <div class="info-box">
                <strong>Format du mot de passe :</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li>Au moins 8 caractères</li>
                    <li>Une majuscule</li>
                    <li>Une minuscule</li>
                    <li>Un chiffre</li>
                    <li>Un caractère spécial (!@#$%^&*...)</li>
                </ul>
            </div>

            <button type="submit">S'inscrire</button>
        </form>

        <div class="link">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
            <p><a href="index.php">Retour à l'accueil</a></p>
        </div>
    </div>
</body>
</html>
