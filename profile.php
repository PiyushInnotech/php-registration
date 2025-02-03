<?php include 'components/header.php' ?>
<body class="py-10 px-4">
    <p>Hello to profile page <?= htmlspecialchars($loggedUser['email'] ?? '') ?></p>
</body>
</html>
