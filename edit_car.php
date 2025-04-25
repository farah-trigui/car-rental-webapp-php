<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

$carsFile = 'cars.json';
$cars = json_decode(file_get_contents($carsFile), true);

if (!isset($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
    header('Location: admin.php');
    exit();
}

$carId = intval($_GET['car_id']);

$car = null;
foreach ($cars as $item) {
    if ($item['id'] === $carId) {
        $car = $item;
        break;
    }
}

if (!$car) {
    header('Location: admin.php');
    exit();
}

$errors = [
    'brand' => '',
    'model' => '',
    'year' => '',
    'transmission' => '',
    'fuel_type' => '',
    'passengers' => '',
    'daily_price_huf' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $year = intval($_POST['year']);
    $transmission = trim($_POST['transmission']);
    $fuelType = trim($_POST['fuel_type']);
    $passengers = intval($_POST['passengers']);
    $dailyPrice = intval($_POST['daily_price_huf']);

    if (empty($brand)) $errors['brand'] = 'Brand is required.';
    if (empty($model)) $errors['model'] = 'Model is required.';
    if (empty($year) || $year < 1900 || $year > date('Y')) $errors['year'] = 'Invalid year.';
    if (empty($transmission)) $errors['transmission'] = 'Transmission is required.';
    if (!in_array($transmission, ['Automatic', 'Manual'])) $errors['transmission'] = 'Invalid transmission type.';
    if (empty($fuelType)) $errors['fuel_type'] = 'Fuel type is required.';
    if (!in_array($fuelType, ['Petrol', 'Diesel', 'Electric'])) $errors['fuel_type'] = 'Invalid fuel type.';
    if (empty($passengers) || $passengers < 1) $errors['passengers'] = 'Number of seats must be at least 1.';
    if (empty($dailyPrice) || $dailyPrice < 0) $errors['daily_price_huf'] = 'Daily price must be a positive number.';

    if (empty(array_filter($errors))) {
        foreach ($cars as &$item) {
            if ($item['id'] === $carId) {
                $item['brand'] = $brand;
                $item['model'] = $model;
                $item['year'] = $year;
                $item['transmission'] = $transmission;
                $item['fuel_type'] = $fuelType;
                $item['passengers'] = $passengers;
                $item['daily_price_huf'] = $dailyPrice;
                break;
            }
        }

        file_put_contents($carsFile, json_encode($cars, JSON_PRETTY_PRINT));
        header('Location: admin.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="edit-car-page">
    <header class="top-bar">
        <div class="logo">
            <a href="index.php">iKarRental</a>
        </div>
        <div class="nav-links">
            <a href="admin.php" class="btn btn-yellow">Admin Panel</a>
            <form method="POST" action="logout.php" style="display: inline;">
                <button type="submit" class="btn btn-light">Logout</button>
            </form>
        </div>
    </header>

    <main>
        <h1>Edit Car</h1>
        <form method="POST" class="edit-form">
            <label>Brand:</label>
            <input type="text" name="brand" value="<?= $car['brand'] ?>">
            <small class="error-message"><?=$errors['brand'] ?></small>

            <label>Model:</label>
            <input type="text" name="model" value="<?= $car['model'] ?>">
            <small class="error-message"><?=$errors['model'] ?></small>

            <label>Year:</label>
            <input type="number" name="year" value="<?= $car['year'] ?>">
            <small class="error-message"><?=$errors['year'] ?></small>

            <label>Transmission:</label>
            <select name="transmission">
                <option value="Automatic" <?= $car['transmission'] === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                <option value="Manual" <?= $car['transmission'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
            </select>
            <small class="error-message"><?= $errors['transmission'] ?></small>

            <label>Fuel Type:</label>
            <select name="fuel_type">
                <option value="Petrol" <?= $car['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                <option value="Diesel" <?= $car['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Electric" <?= $car['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Electric</option>
            </select>
            <small class="error-message"><?= $errors['fuel_type'] ?></small>

            <label>Number of Seats:</label>
            <input type="number" name="passengers" value="<?= $car['passengers'] ?>">
            <small class="error-message"><?= $errors['passengers'] ?></small>

            <label>Daily Price (Ft):</label>
            <input type="number" name="daily_price_huf" value="<?= $car['daily_price_huf'] ?>">
            <small class="error-message"><?= $errors['daily_price_huf'] ?></small>

            <button type="submit" class="btn btn-yellow">Save Changes</button>
        </form>
    </main>
</body>
</html>
