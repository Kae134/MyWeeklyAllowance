<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use MyWeeklyAllowance\AuthService;
use MyWeeklyAllowance\Database\Database;

Database::getConnection();

$error = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $authService = new AuthService();
        $userData = $authService->login($email, $password);

        $_SESSION['user'] = [
            'email' => $userData->email,
            'name' => $userData->name,
            'firstname' => $userData->firstname,
            'userType' => $userData->userType,
            'balance' => $userData->balance,
        ];

        if ($userData->userType === 'parent') {
            header('Location: parent-dashboard.php');
        } else {
            header('Location: child-dashboard.php');
        }
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
    <title>Connexion - MyWeeklyAllowance</title>
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
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #000000;
            background-color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
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

        .success {
            background-color: #ccffcc;
            border: 1px solid #00cc00;
            padding: 15px;
            margin-bottom: 20px;
            color: #006600;
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
        }

        .info-box h3 {
            margin-bottom: 10px;
            font-size: 1em;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 0.9em;
        }

        code {
            background-color: #ffffff;
            padding: 2px 6px;
            border: 1px solid #000000;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MyWeeklyAllowance</h1>
        <p class="subtitle">Connexion</p>

        <?php if ($error): ?>
            <div class="error">
                <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Se connecter</button>
        </form>

        <div class="link">
            <p>Pas encore de compte ? <a href="signup.php">S'inscrire</a></p>
            <p><a href="index.php">Retour à l'accueil</a></p>
        </div>

        <div class="info-box">
            <h3>Comptes de test</h3>
            <p>Parent : <code>parent@example.com</code> / <code>ValidPassword123!</code></p>
            <p>Enfant : <code>child@example.com</code> / <code>ValidPassword123!</code></p>
        </div>
    </div>
</body>
</html>
