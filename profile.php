<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$cars = json_decode(file_get_contents('cars.json'), true);
$bookings = json_decode(file_get_contents('bookings.json'), true);

$userEmail = $_SESSION['user_email'];

$userReservations = array_filter($bookings, function ($booking) use ($userEmail) {
    return $booking['user_email'] === $userEmail;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="profile-page">
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

    <main>
        <section class="profile">
            <h1>Welcome, <?= $_SESSION['user_email'] ?>!</h1>
            <h2>Your Reservations</h2>

            <?php if (count($userReservations) > 0): ?>
                <ul class="reservations-list">
                    <?php foreach ($userReservations as $reservation): ?>
                        <?php
                        $car = array_filter($cars, function ($c) use ($reservation) {
                            return $c['id'] === $reservation['car_id'];
                        });
                        $car = reset($car);
                        ?>
                        <li class="reservation-item">
                            <div class="car-image">
                                <img src="<?= $car['image'] ?>" alt="<?= $car['brand'] . ' ' . $car['model'] ?>">
                            </div>
                            <div class="reservation-details">
                                <h3><?= $car['brand'] . ' ' . $car['model'] ?></h3>
                                <p><strong>From:</strong> <?= $reservation['start_date'] ?></p>
                                <p><strong>To:</strong> <?= $reservation['end_date'] ?></p>
                                <p><strong>Price per day:</strong> HUF <?= number_format($car['daily_price_huf'], 0) ?></p>
                                <p><strong>Seats:</strong> <?= $car['passengers'] ?></p>
                                <p><strong>Transmission:</strong> <?= $car['transmission'] ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no reservations yet.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
