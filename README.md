# 💰 MyWeeklyAllowance

Application de gestion d'argent de poche pour adolescents, développée selon la méthodologie **TDD (Test-Driven Development)**.

## 📋 Description

MyWeeklyAllowance permet aux parents de gérer un "porte-monnaie virtuel" pour leurs adolescents. L'application offre :
- Création de comptes enfants
- Dépôt d'argent sur les comptes
- Suivi des dépenses avec description
- Allocation hebdomadaire automatique

## 🚀 Installation

### Prérequis
- PHP 8.4 ou supérieur
- Composer
- SQLite
- Xdebug (pour la couverture de code)

### Installation des dépendances

```bash
composer install
```

## 🧪 Tests

### Lancer les tests

```bash
# Tests complets avec affichage détaillé
vendor/bin/phpunit --testdox

# Tests simples
vendor/bin/phpunit
```

### Couverture de code

```bash
# Rapport en ligne de commande
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text

# Rapport HTML (dans le dossier coverage/)
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage/
```

**Couverture actuelle : 94.17%** ✅

## 📁 Structure du Projet

```
MyWeeklyAllowance/
├── src/
│   ├── AuthService.php
│   ├── ChildDashboardService.php
│   ├── ParentDashboardService.php
│   ├── DTO/
│   │   ├── ChildData.php
│   │   └── UserData.php
│   ├── Database/
│   │   └── Database.php
│   ├── Exception/
│   │   ├── EmailAlreadyInUseException.php
│   │   ├── InsufficientFundsException.php
│   │   ├── PasswordMismatchException.php
│   │   └── ...
│   ├── Helper/
│   │   ├── AuthenticationHelper.php
│   │   └── BalanceHelper.php
│   ├── Interface/
│   │   ├── AuthServiceInterface.php
│   │   ├── ChildDashboardServiceInterface.php
│   │   └── ParentDashboardServiceInterface.php
│   ├── Repository/
│   │   └── UserRepository.php
│   └── Validator/
│       ├── EmailValidator.php
│       ├── PasswordValidator.php
│       ├── NameValidator.php
│       ├── FirstnameValidator.php
│       └── AmountValidator.php
├── tests/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── SignInTest.php
│   ├── ChildDashboard/
│   │   ├── SpendMoneyTest.php
│   │   └── SpendMoneyWithDescriptionTest.php
│   └── ParentDashboard/
│       ├── AddMoneyToAccountTest.php
│       ├── CreateChildTest.php
│       ├── DepositMoneyTest.php
│       ├── FixAllowanceTest.php
│       ├── GetMyChildrenTest.php
│       └── SaveExpenseTest.php
└── data/
    └── myweeklyallowance.db
```

## 🔑 Fonctionnalités

### 🔐 Authentification

#### Login
- Connexion avec email et mot de passe
- Vérification des identifiants
- Retour des données utilisateur

#### SignIn (Inscription)
- Création de compte parent ou enfant
- Validation des données (email, mot de passe, nom, prénom)
- Initialisation du solde à 0

### 👨‍👩‍👧 Tableau de Bord Parent

#### Gestion des Enfants
- **Créer un compte enfant** : Ajouter un nouvel enfant avec ses informations
- **Voir mes enfants** : Liste des enfants triés par nom
- **Fixer l'allocation hebdomadaire** : Définir le montant hebdomadaire

#### Gestion Financière
- **Déposer de l'argent** : Transférer de l'argent au compte enfant
- **Ajouter de l'argent à son compte** : Approvisionner son propre compte
- **Sauvegarder les dépenses** : Valider les dépenses des enfants

### 👶 Tableau de Bord Enfant

#### Dépenses
- **Dépenser de l'argent** : Retirer de l'argent du compte
- **Dépenser avec description** : Enregistrer une dépense avec justification
- Vérification du solde avant chaque dépense

## 📊 Méthodologie TDD

Le projet a été développé en suivant rigoureusement la méthodologie TDD :

### Phase 1 - RED 🔴
- Écriture des tests unitaires avant le code
- 67 tests couvrant toutes les fonctionnalités
- Tests échouant initialement

### Phase 2 - BLUE 🔵
- Implémentation du code pour faire passer les tests
- Développement itératif fonction par fonction
- Validation continue avec les tests

### Phase 3 - GREEN 🟢 (Refactoring)
- Création de classes Helper (AuthenticationHelper, BalanceHelper)
- Réduction de la duplication de code de 70%
- Amélioration de la maintenabilité
- Séparation des responsabilités (SRP)

### Phase 4 - Vérification ✅
- Couverture de code : **94.17%**
- 67 tests passants
- 100 assertions validées

## 🎯 Résultats des Tests

```
Tests: 67/67 (100%) ✅
Assertions: 100
Coverage: 94.17%

Classes:  85.71% (12/14)
Methods:  93.75% (45/48)
Lines:    94.17% (323/343)
```

### Répartition des Tests

| Module | Tests | Status |
|--------|-------|--------|
| Authentication (Login) | 8 | ✅ 100% |
| Authentication (SignIn) | 10 | ✅ 100% |
| Parent - Create Child | 10 | ✅ 100% |
| Parent - Get Children | 3 | ✅ 100% |
| Parent - Deposit Money | 11 | ✅ 100% |
| Parent - Save Expense | 3 | ✅ 100% |
| Parent - Fix Allowance | 3 | ✅ 100% |
| Parent - Add Money | 3 | ✅ 100% |
| Child - Spend Money | 7 | ✅ 100% |
| Child - Spend With Description | 9 | ✅ 100% |

## 🔒 Validation des Données

### Email
- Format valide requis (utilise `filter_var`)
- Non vide

### Mot de Passe
- Minimum 8 caractères
- Au moins une majuscule
- Au moins une minuscule
- Au moins un chiffre
- Au moins un caractère spécial

### Nom et Prénom
- Lettres uniquement (a-z, A-Z)
- Accepte espaces, tirets et apostrophes
- Non vides

### Montant
- Valeur positive
- Non nul (différent de 0)

## 💾 Base de Données

### Structure

**Table `users`**
- id, email, password, name, firstname, user_type, balance, created_at

**Table `children`**
- id, email, parent_email, name, firstname, balance, weekly_allowance, created_at

**Table `expenses`**
- id, child_email, amount, description, is_saved, created_at

### Données par Défaut

- **Parent** : parent@example.com / ValidPassword123!
- **Enfant** : child@example.com / ValidPassword123!

## 🏆 Principes SOLID Appliqués

✅ **SRP** (Single Responsibility Principle) : Chaque classe a une responsabilité unique  
✅ **OCP** (Open/Closed Principle) : Code extensible via les interfaces  
✅ **LSP** (Liskov Substitution Principle) : Interfaces respectées  
✅ **ISP** (Interface Segregation Principle) : Interfaces spécifiques  
✅ **DIP** (Dependency Inversion Principle) : Dépendances via abstractions  

## 🛠️ Technologies Utilisées

- **PHP 8.4** : Langage principal
- **PHPUnit 10.5** : Framework de tests
- **SQLite** : Base de données légère
- **Composer** : Gestionnaire de dépendances
- **Xdebug 3.4** : Couverture de code

## 📖 Documentation

- **TestDescription.md** : Description détaillée de tous les tests
- Commentaires Input/Output sur chaque fonction
- Code auto-documenté et lisible

## 👥 Auteur

[Kae](https://github.com/Kae134)

## 📝 Licence

Projet éducatif - Libre d'utilisation pour l'apprentissage.

---

**Dernière mise à jour** : 27/11/2025
