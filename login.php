<?php include 'components/header.php' ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['rememberMe']);


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    $user = array_values(array_filter($users, fn($u) => $u['email'] === $email && password_verify($password, $u['password'])))[0] ?? null;

    if (!$user) {
        $errors['password'] = 'Email or password is invalid';
    }

    if (empty($errors)) {
        $registrationTime = date('Y-m-d H:i:s');
        $logMessage = "User logged in: " . $user['email'] . " at " . $registrationTime . "\n";
        file_put_contents('log.txt', $logMessage, FILE_APPEND);
        $_SESSION['user'] = $user;
        if ($rememberMe) {
            $cookieValue = base64_encode($user['email']); 
            setcookie("user", $cookieValue, time() + (30 * 24 * 60 * 60), "/");
        }
        header("Location: profile.php");
    }
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
<body class="py-10 px-4">
    <div class="max-w-3xl mx-auto shadow-md rounded-lg bg-gray-100 p-4 sm:p-8">
        <div class="w-full bg-white shadow rounded p-8">
            <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>            
            <form method="POST" enctype="multipart/form-data">
                <div class="grid gap-6 mb-6">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input 
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="Enter your email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['email'] ?? '' ?></p>
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input 
                            type="password"
                            id="password"
                            value="<?= htmlspecialchars($_POST['password'] ?? '') ?>"
                            name="password"
                            placeholder="Enter your password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['password'] ?? '' ?></p>
                    </div>
                    <div class="flex gap-6 justofy-start">
                        <label>
                            <input type="checkbox" name="rememberMe" value="true" 
                            <?php if (isset($_POST['rememberMe'])) echo 'checked'; ?>> Remember Me
                        </label>
                    </div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <button type="submit" class="w-fit text-white items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm  px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                    <a href='index.php' class="text-blue-500 underline">Register for a new account</a>
                </div>
            </form> 
        </div>
    </div>
</body>
</html>
