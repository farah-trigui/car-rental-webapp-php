<?php
session_start();
$errors = [
    'email' => '',
    'password' => '',
];
$input = [
    'email' => '',
    'password' => '',
];
$users = json_decode(file_get_contents('users.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['email'] = trim($_POST['email']);
    $input['password'] = $_POST['password'];

    if (empty($input['email'])) {
        $errors['email'] = 'Email Address is required.';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address.';
    }

    if (empty($input['password'])) {
        $errors['password'] = 'Password is required.';
    }
    if (empty(array_filter($errors))) {
        $userFound = false;

        if ($input['email'] === 'admin@ikarrental.hu' && $input['password'] === 'admin') {
            $_SESSION['user_email'] = $input['email'];
            $_SESSION['is_admin'] = true;
            header('Location: admin.php'); 
            exit();
        }

        foreach ($users as $user) {
            if ($user['email'] === $input['email'] && $user['password'] === $input['password']) {
                $userFound = true;
                $_SESSION['user_email'] = $user['email']; 
                $_SESSION['is_admin'] = $user['isAdmin']; 

                if ($user['isAdmin']) {
                    header('Location: admin.php'); 
                } else {
                    header('Location: index.php');
                }
                exit();
            }
        }
        if (!$userFound) {
            $errors['password'] = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page">
<div class="top-bar">
    <div class="logo">
        <a href="index.php">iKarRental</a>
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <a href="admin.php" class="btn btn-yellow">Admin Panel</a>
        <?php elseif (isset($_SESSION['user_email'])): ?>
            <a href="profile.php" class="btn btn-yellow">My Profile</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-light">Login</a>
            <a href="register.php" class="btn btn-yellow">Registration</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_email'])): ?>
            <form method="POST" action="logout.php" style="display: inline;">
                <button type="submit" class="btn btn-light">Logout</button>
            </form>
        <?php endif; ?>
    </div>
</div>
    <div class="login-container">
        <h1>Login</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= $input['email'] ?>">
                <small class="error-message"><?= $errors['email']?></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <small class="error-message"><?= $errors['password']?></small>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
