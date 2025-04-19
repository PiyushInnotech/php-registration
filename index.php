<?php include 'components/header.php' ?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $profileImage = $_FILES['profile_image'];

    if (empty($fullName)) $errors['fullName'] = 'Full Name is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $errors['email'] = "Email is already registered.";
            break;
        }
    }
    if (empty($dob)) $errors['dob'] = 'Date of Birth is required';
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } 
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%?&*_-]).{8,}$/", $password)) {
        $errors['password'] = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one special character.";
    }

    if ($profileImage['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['profile_image'] = 'Profile image is required';
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profileImage['type'], $allowedTypes)) {
            $errors['profile_image'] = 'Only JPEG, PNG, or GIF images are allowed';
        } elseif ($profileImage['size'] > 2 * 1024 * 1024) {
            $errors['profile_image'] = 'Image size must not exceed 2MB';
        }
    }

    if (empty($errors)) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($profileImage['name']);
        $uploadPath = $uploadDir . $fileName;
        move_uploaded_file($profileImage['tmp_name'], $uploadPath);
        $registrationTime = date('Y-m-d H:i:s');
        $userData = [
            'fullName' => $fullName,
            'email' => $email,
            'dob' => $dob,
            'password' => $hashedPassword,
            'profileImage' => $uploadPath,
            'registration_time' => $registrationTime
        ];
        $users[] = $userData;
        $logMessage = "New registration: " . $email . " at " . $registrationTime . "\n";
        file_put_contents('log.txt', $logMessage, FILE_APPEND);
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        header("Location: login.php");
    }
}
?>

<div class="py-10 px-4">
    <div class="max-w-3xl mx-auto shadow-md rounded-lg bg-gray-100 p-4 sm:p-8">
        <div class="w-full bg-white shadow rounded p-8">
            <h1 class="text-2xl font-bold mb-6 text-center">Register User</h1>            
            <form method="POST" enctype="multipart/form-data">
                <div class="grid gap-6 mb-6">
                    <div>
                        <label for="fullName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full name</label>
                        <input 
                            type="text"
                            id="fullName"
                            name="fullName"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter your fullname"
                            value="<?= htmlspecialchars($_POST['fullName'] ?? '') ?>"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['fullName'] ?? '' ?></p>
                    </div>
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
                        <label for="dob" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date of Birth</label>
                        <input 
                            type="date"
                            id="dob"
                            value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>"
                            name="dob"
                            max="<?php echo date('Y-m-d'); ?>" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onkeydown="return false"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['dob'] ?? '' ?></p>
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
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="profile_image">Upload Profile Image</label>
                        <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="profile_image" name="profile_image" type="file"  accept="image/*">
                        <p class="text-red-500 text-sm pt-1"><?= $errors['profile_image'] ?? '' ?></p>
                    </div> 
                </div>
                <div class="flex flex-col justify-center items-center">
                    <button type="submit" class="w-fit text-white items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm  px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                </div>
            </form> 
        </div>
    </div>
</div>
</html>
