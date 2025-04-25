<?php
$errors = [
    'fullName' => '',
    'email' => '',
    'password' => '',
];
$input = [
    'fullName' => '',
    'email' => '',
    'password' => '',
];

$usersFile = 'users.json'; 
$users = json_decode(file_get_contents($usersFile), true); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['fullName'] = trim($_POST['fullName']);
    $input['email'] = trim($_POST['email']);
    $input['password'] = $_POST['password'];

    if (empty($input['fullName'])) {
        $errors['fullName'] = 'Full Name is required.';
    }

    if (empty($input['email'])) {
        $errors['email'] = 'Email Address is required.';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address.';
    } else {
        foreach ($users as $user) {
            if ($user['email'] === $input['email']) {
                $errors['email'] = 'Email already exists.';
                break;
            }
        }
    }

    if (empty($input['password'])) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($input['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[0-9]/', $input['password'])) {
        $errors['password'] = 'Password must include at least one number.';
    } elseif (!preg_match('/[!@#$%^&*]/', $input['password'])) {
        $errors['password'] = 'Password must include at least one special character.';
    }

    if (empty(array_filter($errors))) {
        $newUser = [
            'fullName' => $input['fullName'],
            'email' => $input['email'],
            'password' => $input['password'], 
            'isAdmin' => false, 
        ];

        $users[] = $newUser;

        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

        echo '<p style="color: green;">Registration successful!</p>';
        $input = ['fullName' => '', 'email' => '', 'password' => ''];
    }
}
?>

<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="registration-page">
<header>
        <div class="top-bar">
            <div class="logo">
                <a href="index.php">iKarRental</a>
            </div>
            <div class="nav-links">
                <a href="login.php" class="btn btn-light">Login</a>
                <a href="register.php" class="btn btn-yellow">Registration</a>
            </div>
        </div>
    </header>
    <div class="registration-container">
        <h1>Registration</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?=$input['fullName'] ?>">
                <small class="error-message"><?= $errors['fullName'] ?></small>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= $input['email'] ?>">
                <small class="error-message"><?= $errors['email'] ?></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <small class="error-message"><?= $errors['password'] ?></small>
            </div>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
