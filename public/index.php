<?php
require_once __DIR__ . "/../vendor/autoload.php";

use MyWeeklyAllowance\Database\Database;
Database::getConnection();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyWeeklyAllowance - Gestion d'argent de poche</title>
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
        }

        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px 20px;
        }

        .logo {
            font-size: 4em;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 3px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .tagline {
            font-size: 1.5em;
            margin-bottom: 60px;
            font-style: italic;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 80px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 20px 50px;
            background-color: #000000;
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2em;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            border: 3px solid #000000;
        }

        .btn:hover {
            background-color: #ffffff;
            color: #000000;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #000000;
        }

        .btn-secondary:hover {
            background-color: #000000;
            color: #ffffff;
        }

        .features {
            max-width: 1000px;
            margin: 0 auto;
        }

        .features-title {
            font-size: 2em;
            margin-bottom: 40px;
            text-align: center;
            text-transform: uppercase;
            border-bottom: 3px solid #000000;
            padding-bottom: 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .feature-card {
            border: 3px solid #000000;
            padding: 30px;
            text-align: center;
        }

        .feature-card h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .feature-card p {
            font-size: 1em;
            line-height: 1.8;
        }

        .icon {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .demo-accounts {
            background-color: #000000;
            color: #ffffff;
            padding: 40px;
            margin-top: 40px;
            text-align: center;
        }

        .demo-accounts h3 {
            font-size: 1.5em;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .demo-card {
            border: 3px solid #ffffff;
            padding: 20px;
        }

        .demo-card h4 {
            font-size: 1.2em;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .demo-card p {
            margin: 8px 0;
            font-size: 0.95em;
        }

        code {
            background-color: #333333;
            padding: 3px 8px;
            font-family: 'Courier New', monospace;
        }

        .footer {
            text-align: center;
            padding: 40px 20px;
            border-top: 3px solid #000000;
            margin-top: 60px;
            background-color: #f5f5f5;
        }

        .ascii-art {
            font-size: 0.6em;
            margin-bottom: 30px;
            white-space: pre;
            line-height: 1;
        }

        @media (max-width: 768px) {
            .logo {
                font-size: 2.5em;
            }

            .tagline {
                font-size: 1.2em;
            }

            .btn {
                padding: 15px 30px;
                font-size: 1em;
            }

            .cta-buttons {
                flex-direction: column;
                width: 100%;
            }

            .ascii-art {
                font-size: 0.4em;
            }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="ascii-art">
 __  __       __        __        _    _          _    _ _
|  \/  |     / /        \ \      | |  | |        | |  | | |
| \  / |_   | |          | |     | |  | | ___  __| | _| | |_   _    /\   | | | _____      __ __ _ _ __   ___ ___
| |\/| | | | | |          | |     | |/\| |/ _ \/ _` |/ / | | | | |  /  \  | | |/ _ \ \ /\ / / _` | '_ \ / __/ _ \
| |  | | |_| | |          | |     \  /\  /  __/ (_| <  <| | | |_| | / /\ \ | | | (_) \ V  V / (_| | | | | (_|  __/
|_|  |_|\__, | |          | |      \/  \/ \___|\__,_|\_\_|_|\__, |/_/    \_\|_|\___/ \_/\_/ \__,_|_| |_|\___\___|
         __/ | |          | |                                 __/ |
        |___/  \_\        /_/                                 |___/
        </div>

        <h1 class="logo">MyWeeklyAllowance</h1>
        <p class="tagline">Gérez l'argent de poche de vos enfants en toute simplicité</p>

        <div class="cta-buttons">
            <a href="login.php" class="btn">Se connecter</a>
            <a href="signup.php" class="btn btn-secondary">S'inscrire</a>
        </div>

        <div class="features">
            <h2 class="features-title">Fonctionnalités</h2>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon">👨‍👩‍👧‍👦</div>
                    <h3>Pour les Parents</h3>
                    <p>Créez des comptes pour vos enfants, fixez des allocations hebdomadaires et transférez de l'argent en toute sécurité.</p>
                </div>

                <div class="feature-card">
                    <div class="icon">👶</div>
                    <h3>Pour les Enfants</h3>
                    <p>Suivez votre solde, enregistrez vos dépenses et apprenez à gérer votre argent de façon responsable.</p>
                </div>

                <div class="feature-card">
                    <div class="icon">💰</div>
                    <h3>Transferts Sécurisés</h3>
                    <p>Transférez de l'argent aux enfants avec authentification par mot de passe.</p>
                </div>

                <div class="feature-card">
                    <div class="icon">📊</div>
                    <h3>Suivi des Dépenses</h3>
                    <p>Les enfants enregistrent leurs dépenses et les parents peuvent les valider.</p>
                </div>

                <div class="feature-card">
                    <div class="icon">📅</div>
                    <h3>Allocation Hebdomadaire</h3>
                    <p>Configurez une allocation automatique pour responsabiliser vos enfants.</p>
                </div>

                <div class="feature-card">
                    <div class="icon">🔒</div>
                    <h3>100% Sécurisé</h3>
                    <p>Mots de passe cryptés, sessions sécurisées et validation stricte des données.</p>
                </div>
            </div>
        </div>

        <div class="demo-accounts">
            <h3>Comptes de démonstration</h3>
            <div class="demo-grid">
                <div class="demo-card">
                    <h4>👨 Compte Parent</h4>
                    <p>Email : <code>parent@example.com</code></p>
                    <p>Mot de passe : <code>ValidPassword123!</code></p>
                    <p>Solde : 1000.00 €</p>
                </div>

                <div class="demo-card">
                    <h4>👶 Compte Enfant</h4>
                    <p>Email : <code>child@example.com</code></p>
                    <p>Mot de passe : <code>ValidPassword123!</code></p>
                    <p>Solde : 100.00 €</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
