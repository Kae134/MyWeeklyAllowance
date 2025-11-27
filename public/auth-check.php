<?php
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

function refreshUserBalance()
{
    require_once __DIR__ . "/../vendor/autoload.php";

    $userRepo = new MyWeeklyAllowance\Repository\UserRepository();
    $balance = $userRepo->getUserBalance($_SESSION["user"]["email"]);
    $_SESSION["user"]["balance"] = $balance;
}

refreshUserBalance();
