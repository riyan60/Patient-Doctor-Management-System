<?php
include __DIR__ . '/../config/db_connect.php';

// Fetch tests data from database
$sql = "SELECT t.id, t.name, l.name as lab_name, t.price, t.discounted_price, t.icon, t.description, t.duration, t.is_available FROM tests t LEFT JOIN labs l ON t.lab_id = l.id ORDER BY t.name";
$result = $conn->query($sql);

$tests = [];
$tests_count = 0;
if ($result->num_rows > 0) {
    $tests_count = $result->num_rows;
    while($row = $result->fetch_assoc()) {
        $test_key = $row['id']; // Use id as key
        $tests[$test_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "lab_name" => $row['lab_name'] ?: "N/A",
            "price" => (float)$row['price'],
            "discounted_price" => $row['discounted_price'] ? (float)$row['discounted_price'] : (float)$row['price'],
            "icon" => $row['icon'] ?: "&#129298;",
            "desc" => $row['description'] ?: "Medical test for health assessment.",
            "duration" => $row['duration'] ?: "N/A"
        ];
    }
} else {
    echo "<p>No medical tests are currently available. Please check back later.</p>";
}

?>
