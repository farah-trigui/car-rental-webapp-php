<?php
session_start();
$isLoggedIn = isset($_SESSION['user_email']);

$cars = json_decode(file_get_contents('cars.json'), true);

$carId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$car = null;
foreach ($cars as $item) {
    if ($item['id'] === $carId) {
        $car = $item;
        break;
    }
}

if (!$car) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Failed</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body class="booking-page">
<header>
<div class="top-bar">
        <div class="logo">
            <a href="index.php">iKarRental</a>
        </div>
        <div class="nav-links">
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="btn btn-light">My Profile</a>
                <a href="logout.php" class="btn btn-yellow">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-light">Login</a>
                <a href="register.php" class="btn btn-yellow">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>
    <h1>Booking failed!</h1>
    <img src="https://icons.veryicon.com/png/o/education-technology/mobile-campus/fail-53.png" alt="Failure">
    <p><?= $car['brand'] . ' ' . $car['model'] ?> is not available for the specified interval from <?= $_GET['from']; ?> to <?= $_GET['to']; ?>. <br>
        Try entering a different interval or search for another vehicle </p>
    <div class="back_button">
    <a href="index.php" class="btn btn-light">Back to vehicle side</a>
    </div>
</main>
</body>
</html>
