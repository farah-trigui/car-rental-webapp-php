<?php
session_start();
$isLoggedIn = isset($_SESSION['user_email']);

$cars = json_decode(file_get_contents('cars.json'), true);

$carId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$fromDate = $_GET['from'];
$toDate = $_GET['to'];

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

$days = (strtotime($toDate) - strtotime($fromDate)) / (60 * 60 * 24) + 1;
$totalPrice = $days * $car['daily_price_huf'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body class="booking-page">
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
    <h1>Successful booking!</h1>
    <img src="https://cdn1.vectorstock.com/i/1000x1000/83/15/quality-icon-from-competition-success-bicolor-vector-5608315.jpg" alt="Success">
    <p>The <?= $car['brand'] . ' ' . $car['model'] ?> has been sucessfully booked for the interval <?= $fromDate ?> - <?= $toDate ?>.<br>
    You can track the status of your reservation on your profile page</p>
    <p>Total price: HUF <?= number_format($totalPrice, 0) ?></p>
    <div class = back_button>
    <a href="profile.php" class="btn btn-light">My profile</a>
    </div>
</body>
</html>
