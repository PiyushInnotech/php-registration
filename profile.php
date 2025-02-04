<?php include 'components/header.php' ?>
<?php
if (!isset($_SESSION['isEditingProfile'])) {
    $_SESSION['isEditingProfile'] = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if (!$_SESSION['isEditingProfile']) {
        $_SESSION['isEditingProfile'] = true;
    } else {
        $errors = [];
        $fullName = htmlspecialchars(trim($_POST['fullName']));
        $dob = $_POST['dob'];
        $profileImage = $_FILES['profile_image'];
    
        if (empty($fullName)) $errors['fullName'] = 'Full Name is required';
        if (empty($dob)) $errors['dob'] = 'Date of Birth is required';

        if ($profileImage['error'] !== UPLOAD_ERR_NO_FILE) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($profileImage['type'], $allowedTypes)) {
                $errors['profile_image'] = 'Only JPEG, PNG, or GIF images are allowed';
            } elseif ($profileImage['size'] > 2 * 1024 * 1024) {
                $errors['profile_image'] = 'Image size must not exceed 2MB';
            }
        }

        $loggedUser['fullName'] = $fullName;
        $loggedUser['dob'] = $dob;

        if (empty($errors)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '_' . basename($profileImage['name']);
            $uploadPath = $uploadDir . $fileName;
            move_uploaded_file($profileImage['tmp_name'], $uploadPath);

            if (!empty($loggedUser['profileImage']) && file_exists($loggedUser['profileImage'])) {
                unlink($loggedUser['profileImage']);
            }

            $loggedUser['profileImage'] = $uploadPath;
            foreach ($users as &$user) {
                if ($user['email'] === $loggedUser['email']) {
                    $user = $loggedUser;
                    break;
                }
            }
            $registrationTime = date('Y-m-d H:i:s');
            $logMessage = "User update profile:  ( " . $loggedUser['email'] . " ) at " . $registrationTime . "\n";
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            $_SESSION['isEditingProfile'] = false;
        }

    }
}
?>


<body>
    <div class="max-w-3xl mx-auto shadow-md rounded-lg bg-gray-100 p-4 sm:p-8 mt-10 mx-2">
        <div class="w-full bg-white shadow rounded p-8">     
            <img class="w-24 h-24 mb-3 rounded-full shadow-lg mx-auto" src="<?= htmlspecialchars($loggedUser['profileImage'] ?? '') ?>" alt="<?= htmlspecialchars($loggedUser['fullName'] ?? '') ?>"/>
            <form method="POST" enctype="multipart/form-data">
                <div class="grid gap-6 mb-6">
                    <div>
                        <label for="fullName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name </label>
                        <input 
                            type="text"
                            id="fullName"
                            name="fullName"
                            value="<?= htmlspecialchars($loggedUser['fullName'] ?? '') ?>"
                            placeholder="Enter your fullName"
                            <?= $_SESSION['isEditingProfile'] ? '' : 'disabled' ?>
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['fullName'] ?? '' ?></p>
                    </div>
                    <div>
                        <label for="dob" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date of Birth</label>
                        <input 
                            type="date"
                            id="dob"
                            value="<?= htmlspecialchars($loggedUser['dob'] ?? '') ?>"
                            name="dob"
                            max="<?php echo date('Y-m-d'); ?>" 
                            <?= $_SESSION['isEditingProfile'] ? '' : 'disabled' ?>
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onkeydown="return false"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['dob'] ?? '' ?></p>
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input 
                            type="email"
                            id="email"
                            name="email"
                            disabled
                            value="<?= htmlspecialchars($loggedUser['email'] ?? '') ?>"
                            placeholder="Enter your email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <p class="text-red-500 text-sm pt-1"><?= $errors['email'] ?? '' ?></p>
                    </div>
                    <?php if ($_SESSION['isEditingProfile']): ?>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="profile_image">Upload Profile Image</label>
                            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="profile_image" name="profile_image" type="file"  accept="image/*">
                            <p class="text-red-500 text-sm pt-1"><?= $errors['profile_image'] ?? '' ?></p>
                        </div> 
                    <?php endif; ?>

                </div>
                <div class="flex flex-col justify-center items-center">
                    <button type="submit" name="update" class="w-fit text-white items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm  px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <?php if ($_SESSION['isEditingProfile']): ?>
                            Update Profile
                        <?php else: ?> 
                            Edit Profile
                        <?php endif; ?>
                    </button>
                </div>
            </form> 
        </div>
    </div>
</body>
</html>
