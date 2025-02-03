<?php
session_start();
$userDataDir = 'userData/';
if (!is_dir($userDataDir)) {
    mkdir($userDataDir, 0777, true);
}        
$usersFile = $userDataDir . 'users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
$currentRoute = basename($_SERVER['PHP_SELF']); // Get the current script name
$loggedUser = null;

if (isset($_SESSION['user'])) {
    print_r('11111111111111111');
    $loggedUser = $_SESSION['user'];
    if ($currentRoute != 'profile.php') {
        header('Location: profile.php');
    }
} else if (isset($_COOKIE['user'])) {
    print_r('22222222222222222222');
    $cookieValue = base64_decode($_COOKIE['user']);
    print_r($cookieValue);
    $user = array_values(array_filter($users, fn($u) => $u['email'] === $cookieValue ))[0] ?? null;
    $_SESSION['user'] = $user;
    print_r($user);
    $loggedUser = $user;
    if ($currentRoute != 'profile.php') {
        header('Location: profile.php');
    }
} else if ($currentRoute == 'profile.php'){
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.0.0/dist/flowbite.min.js"></script>
    <title>Registration</title>
</head>