<?php 

session_start();
$isLoggedIn = isset($_SESSION['user_email']);

$jsonData = file_get_contents('cars.json');
$cars = json_decode($jsonData, true);

$bookingsData = file_get_contents('bookings.json');
$bookings = json_decode($bookingsData, true);

$passengers = isset($_GET['passengers']) ? intval($_GET['passengers']) : 0;
$transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
$price_min = isset($_GET['price_min']) ? intval($_GET['price_min']) : 0;
$price_max = isset($_GET['price_max']) ? intval($_GET['price_max']) : PHP_INT_MAX;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$filteredCars = array_filter($cars, function ($car) use ($passengers, $transmission, $price_min, $price_max, $date_from, $date_to, $bookings) {
    $matchesBasicFilters = 
        $car['passengers'] >= $passengers &&
        ($transmission === '' || $car['transmission'] === $transmission) &&
        $car['daily_price_huf'] >= $price_min &&
        $car['daily_price_huf'] <= $price_max;

    if (empty($date_from) || empty($date_to)) {
        return $matchesBasicFilters;
    }

    foreach ($bookings as $booking) {
        if (
            $booking['car_id'] === $car['id'] &&
            (
                ($date_from <= $booking['end_date'] && $date_to >= $booking['start_date']) 
            )
        ) {
            return false; 
        }
    }

    return $matchesBasicFilters;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="main-header">
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
    <div class="hero">
        <h1>Rent cars easily!</h1>
        <?php if (!$isLoggedIn): ?>
            <a href="register.php" class="btn btn-large">Registration</a>
        <?php endif; ?>
    </div>
    <form method="GET" action="index.php" class="filter-form">
        <label for="passengers">Seats:</label>
        <input type="number" name="passengers" id="passengers" min="0" placeholder="0">

        <label for="transmission">Gear type:</label>
        <select name="transmission" id="transmission">
            <option value="">Any</option>
            <option value="Automatic">Automatic</option>
            <option value="Manual">Manual</option>
        </select>

        <label for="price_min">Price (Ft):</label>
        <input type="number" name="price_min" id="price_min" placeholder="Min">
        <input type="number" name="price_max" id="price_max" placeholder="Max">

        <label for="date_from">From:</label>
        <input type="date" name="date_from" id="date_from">

        <label for="date_to">Until:</label>
        <input type="date" name="date_to" id="date_to">

        <button type="submit" class="btn">Filter</button>
    </form>
</header>

<main>
    <div class="car-grid">
    <?php if (count($filteredCars) > 0): ?>
        <?php foreach ($filteredCars as $car): ?>
            <div class="car-card">
                <a href="car_details.php?id=<?= $car['id'] ?>">
                <div class="image-container">
                    <img src="<?=$car['image']?>" alt="<?=$car['brand'] . ' ' . $car['model'] ?>">
                    <div class="price"><?= number_format($car['daily_price_huf'], 0) ?> Ft</div>
                </div>
                    <h3><?= $car['brand'] . ' ' . $car['model'] ?></h3>
                    <p><?= $car['passengers'] ?> seats - <?=$car['transmission'] ?></p>
                    <button class="btn">Book</button>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No cars match your criteria.</p>
    <?php endif; ?>   
    </div>
</main>
</body>
</html>
