<?php
include __DIR__ . '/../config/db_connect.php';

// Fetch packages data from database
$sql = "SELECT id, name, price, discounted_price, description, duration, features, is_active FROM packages ORDER BY name";
$result = $conn->query($sql);

$packages = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $package_key = $row['id']; // Use id as key
        $packages[$package_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => (float)$row['price'],
            "discounted_price" => $row['discounted_price'] ? (float)$row['discounted_price'] : (float)$row['price'],
            "desc" => $row['description'] ?: "Health package for comprehensive care.",
            "features" => json_decode($row['features'], true) ?: ["Consultation", "Basic Tests"],
            "duration" => $row['duration'] ?: "1 Year"
        ];
    }
} else {
    echo "<p>No health packages are currently available. Please check back later.</p>";
}

// Close the database connection
// $conn->close(); // Commented out to keep connection open for Admin.php
?>
