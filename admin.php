<?php  
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

$bookingsFile = 'bookings.json';
$carsFile = 'cars.json';
$bookings = json_decode(file_get_contents($bookingsFile), true);
$cars = json_decode(file_get_contents($carsFile), true);

$carsById = [];
foreach ($cars as $car) {
    $carsById[$car['id']] = $car;
}
function getNextBookingId($bookings) {
    $maxId = 0;
    foreach ($bookings as $booking) {
        if (isset($booking['id']) && $booking['id'] > $maxId) {
            $maxId = $booking['id'];
        }
    }
    return $maxId + 1;
}
foreach ($bookings as $index => $booking) {
    if (!isset($booking['id'])) {
        $bookings[$index]['id'] = getNextBookingId($bookings);
    }
}

// Save back updated bookings to ensure consistency
file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));

// Handle booking deletion
if (isset($_GET['delete_booking'])) {
    $bookingId = intval($_GET['delete_booking']);
    $bookings = array_filter($bookings, fn($booking) => $booking['id'] !== $bookingId);

    // Save updated bookings back to the file
    file_put_contents($bookingsFile, json_encode(array_values($bookings), JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit();
}

// Handle car deletion
if (isset($_GET['delete_car'])) {
    $carId = intval($_GET['delete_car']);
    $cars = array_filter($cars, fn($car) => $car['id'] !== $carId);

    // Remove related bookings for the deleted car
    $bookings = array_filter($bookings, fn($booking) => $booking['car_id'] !== $carId);

    // Save updated data back to the files
    file_put_contents($carsFile, json_encode(array_values($cars), JSON_PRETTY_PRINT));
    file_put_contents($bookingsFile, json_encode(array_values($bookings), JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin-page">
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
        <h1>All Bookings</h1>
        <section>
            <?php if (!empty($bookings)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Car</th>
                            <th>User Email</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['id'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (isset($carsById[$booking['car_id']])): ?>
                                        <?= htmlspecialchars($carsById[$booking['car_id']]['brand'] . ' ' . $carsById[$booking['car_id']]['model']) ?>
                                    <?php else: ?>
                                        <span style="color: red;">Car Not Found</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($booking['user_email'] ?? 'Unknown') ?></td>
                                <td><?= htmlspecialchars($booking['start_date'] ?? 'Unknown') ?></td>
                                <td><?= htmlspecialchars($booking['end_date'] ?? 'Unknown') ?></td>
                                <td>
                                    <a href="admin.php?delete_booking=<?= $booking['id'] ?>" class="btn btn-delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings available.</p>
            <?php endif; ?>
        </section>

        <h1>Manage Cars</h1>
        <section>
            <div class="car-grid">
                <div class="car-card add-car-card">
                    <a href="admin_add_car.php">
                        <div class="image-container">
                            <img src="https://static-00.iconduck.com/assets.00/add-square-icon-256x255-m06rimih.png" alt="Add Car">
                        </div>
                        <h3>Add New Car</h3>
                    </a>
                </div>
                <?php if (!empty($cars)): ?>
                    <?php foreach ($cars as $car): ?>
                        <div class="car-card">
                            <div class="image-container">
                                <img src="<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                            </div>
                            <h3><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h3>
                            <p><?= htmlspecialchars($car['passengers']) ?> seats - <?= htmlspecialchars($car['transmission']) ?></p>
                            <p><?= number_format($car['daily_price_huf']) ?> Ft/day</p>
                            <div class="actions">
                                <a href="edit_car.php?car_id=<?= $car['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="admin.php?delete_car=<?= $car['id'] ?>" class="btn btn-delete">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No cars available.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
