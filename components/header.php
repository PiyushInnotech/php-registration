<?php
session_start();
$userDataDir = 'userData/';
if (!is_dir($userDataDir)) {
    mkdir($userDataDir, 0777, true);
}        
$usersFile = $userDataDir . 'users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
$currentRoute = basename($_SERVER['PHP_SELF']);
$loggedUser = null;

if (isset($_SESSION['user'])) {
    $user = array_values(array_filter($users, fn($u) => $u['email'] === $_SESSION['user'] ))[0] ?? null;
    $loggedUser = $user;
    if ($currentRoute != 'profile.php') {
        header('Location: profile.php');
    }
} else if (isset($_COOKIE['user'])) {
    $cookieValue = base64_decode($_COOKIE['user']);
    $_SESSION['userEmail'] = $cookieValue;
    if ($currentRoute != 'profile.php') {
        header('Location: profile.php');
    }
} else if ($currentRoute == 'profile.php'){
    header('Location: login.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $_SESSION = [];
    session_destroy();
    setcookie("user", "", time() - 3600, "/");
    header("Location: login.php");
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
<body>
    <nav class="bg-gray-100 dark:bg-gray-900 shadow-lg">
        <div class="flex flex-wrap items-center justify-between max-w-screen-xl mx-auto p-4">
            <p class="flex items-center space-x-3 rtl:space-x-reverse">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">PHP Form</span>
            </p>
            <?php if ($loggedUser): ?>
                <form method="POST" class="flex items-center md:order-2 space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <button type="submit" name="logout" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Log out</a>
                </form>
            <?php else: ?>
                <div  class="flex items-center gap-2">
                <a href="index.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Sign Up</a>
                <a href="login.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Log In</a>
            </form>
            <?php endif; ?>
        </div>
    </nav>