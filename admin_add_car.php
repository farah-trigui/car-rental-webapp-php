<?php 
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
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
    'image' => '',
];

$input = [
    'brand' => '',
    'model' => '',
    'year' => '',
    'transmission' => '',
    'fuel_type' => '',
    'passengers' => '',
    'daily_price_huf' => '',
    'image' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['brand'] = trim($_POST['brand']);
    $input['model'] = trim($_POST['model']);
    $input['year'] = trim($_POST['year']);
    $input['transmission'] = trim($_POST['transmission']);
    $input['fuel_type'] = trim($_POST['fuel_type']);
    $input['passengers'] = trim($_POST['passengers']);
    $input['daily_price_huf'] = trim($_POST['daily_price_huf']);
    $input['image'] = trim($_POST['image']);

    if (empty($input['brand'])) {
        $errors['brand'] = 'Brand is required.';
    }
    if (empty($input['model'])) {
        $errors['model'] = 'Model is required.';
    }
    if (empty($input['year']) || !is_numeric($input['year']) || $input['year'] < 1886 || $input['year'] > date('Y')) {
        $errors['year'] = 'Year must be a valid number.';
    }
    if (empty($input['transmission'])) {
        $errors['transmission'] = 'Transmission is required.';
    }
    if (empty($input['fuel_type'])) {
        $errors['fuel_type'] = 'Fuel type is required.';
    }
    if (empty($input['passengers']) || !is_numeric($input['passengers'])) {
        $errors['passengers'] = 'Passengers must be a valid number.';
    }
    if (empty($input['daily_price_huf']) || !is_numeric($input['daily_price_huf'])) {
        $errors['daily_price_huf'] = 'Daily price must be a valid number.';
    }
    if (empty($input['image']) || !filter_var($input['image'], FILTER_VALIDATE_URL)) {
        $errors['image'] = 'Image URL must be valid.';
    }

    if (empty(array_filter($errors))) {
        $cars = json_decode(file_get_contents('cars.json'), true);

        $newCarId = count($cars) + 1;

        $newCar = [
            'id' => $newCarId,
            'brand' => $input['brand'],
            'model' => $input['model'],
            'year' => intval($input['year']),
            'transmission' => $input['transmission'],
            'fuel_type' => $input['fuel_type'],
            'passengers' => intval($input['passengers']),
            'daily_price_huf' => intval($input['daily_price_huf']),
            'image' => $input['image'],
        ];
        $cars[] = $newCar;

        file_put_contents('cars.json', json_encode($cars, JSON_PRETTY_PRINT));

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
    <title>Add New Car</title>
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
    <h1>Add New Car</h1>
    <div class="edit-form">
        <form method="POST" action="">
            <label for="brand">Brand</label>
            <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($input['brand']) ?>">
            <small class="error-message"><?= htmlspecialchars($errors['brand']) ?></small>

            <label for="model">Model</label>
            <input type="text" id="model" name="model" value="<?= htmlspecialchars($input['model']) ?>">
            <small class="error-message"><?= htmlspecialchars($errors['model']) ?></small>

            <label for="year">Year</label>
            <input type="number" id="year" name="year" value="<?= htmlspecialchars($input['year']) ?>">
            <small class="error-message"><?= htmlspecialchars($errors['year']) ?></small>

            <label for="transmission">Transmission</label>
            <select id="transmission" name="transmission">
                <option value="">Select Transmission</option>
                <option value="Automatic" <?= $input['transmission'] === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                <option value="Manual" <?= $input['transmission'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
            </select>
            <small class="error-message"><?= htmlspecialchars($errors['transmission']) ?></small>

            <label for="fuel_type">Fuel Type</label>
            <select id="fuel_type" name="fuel_type">
                <option value="">Select Fuel Type</option>
                <option value="Petrol" <?= $input['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                <option value="Diesel" <?= $input['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Electric" <?= $input['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Electric</option>
                <option value="Hybrid" <?= $input['fuel_type'] === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
            </select>
            <small class="error-message"><?= htmlspecialchars($errors['fuel_type']) ?></small>

            <label for="passengers">Passengers</label>
            <input type="number" id="passengers" name="passengers" value="<?= htmlspecialchars($input['passengers']) ?>">
            <small class="error-message"><?= htmlspecialchars($errors['passengers']) ?></small>

            <label for="daily_price_huf">Daily Price (HUF)</label>
            <input type="number" id="daily_price_huf" name="daily_price_huf" value="<?= htmlspecialchars($input['daily_price_huf']) ?>">
            <small class="error-message"><?= htmlspecialchars($errors['daily_price_huf']) ?></small>

            <label for="image">Image URL</label>
            <input type="text" id="image" name="image" value="<?= htmlspecialchars($input['image']) ?>">
            <small class="error-message"><?= htmlspecialchars($errors['image']) ?></small>

            <button type="submit">Add Car</button>
        </form>
    </div>
</body>
</html>
