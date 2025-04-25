<?php
session_start();

$isLoggedIn = isset($_SESSION['user_email']);

$cars = json_decode(file_get_contents('cars.json'), true);
$bookingsFile = 'bookings.json';

if (!file_exists($bookingsFile)) {
    file_put_contents($bookingsFile, json_encode([]));
}

$bookings = json_decode(file_get_contents($bookingsFile), true);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isLoggedIn) {
        header('Location: login.php');
        exit();
    }

    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    $userEmail = $_SESSION['user_email'];

    $isBooked = false;
    foreach ($bookings as $booking) {
        if (
            $booking['car_id'] === $carId &&
            (
                ($fromDate >= $booking['start_date'] && $fromDate <= $booking['end_date']) ||
                ($toDate >= $booking['start_date'] && $toDate <= $booking['end_date'])
            )
        ) {
            $isBooked = true;
            break;
        }
    }

    if ($isBooked) {
        header("Location: booking_failure.php?id=$carId&from=$fromDate&to=$toDate");
        exit();
    } else {
        $newBooking = [
            'start_date' => $fromDate,
            'end_date' => $toDate,
            'user_email' => $userEmail,
            'car_id' => $carId
        ];
        $bookings[] = $newBooking;
        file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));
        header("Location: booking_success.php?id=$carId&from=$fromDate&to=$toDate");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="car-details-page">
    <header class="top-bar">
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
    </header>
    <main>
        <div class="car-details">
            <img src="<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
            <div class="car-info">
                <p><strong>Fuel:</strong> <?= htmlspecialchars($car['fuel_type']) ?></p>
                <p><strong>Shifter:</strong> <?= htmlspecialchars($car['transmission']) ?></p>
                <p><strong>Year of manufacture:</strong> <?= htmlspecialchars($car['year']) ?></p>
                <p><strong>Number of seats:</strong> <?= htmlspecialchars($car['passengers']) ?></p>
                <p><strong>Price per day:</strong> HUF <?= number_format($car['daily_price_huf'], 0) ?></p>

                <?php if ($isLoggedIn): ?>
                    <form method="POST">
                        <label for="from_date">From:</label>
                        <input type="date" id="from_date" name="from_date" required>
                        <label for="to_date">To:</label>
                        <input type="date" id="to_date" name="to_date" required>
                        <button type="submit" class="btn">Book it</button>
                    </form>
                <?php else: ?>
                    <p>Please <a href="login.php" class="btn">Login</a> to book this car.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
