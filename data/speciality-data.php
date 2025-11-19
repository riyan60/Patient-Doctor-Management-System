<?php
include __DIR__ . '/../config/db_connect.php';

// Fetch specialties data from database
$sql = "SELECT id, name, image, description FROM specialties ORDER BY name";
$result = $conn->query($sql);

$specialties = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $specialty_key = $row['name']; // Use name as key for compatibility with specialities.php
        $specialties[$specialty_key] = [
            "image" => str_replace('pics/', 'assets/images/', $row['image']) ?: "assets/images/default.png", // Default image if none
            "description" => $row['description'] ?: "Specialty description not available.",
            "extra" => [
                "Common Conditions" => "Various conditions related to this specialty.",
                "Tests & Procedures" => "Standard diagnostic tests and procedures.",
                "When to See" => "When experiencing symptoms related to this specialty."
            ],
            "doctors" => [] // Will be populated separately if needed
        ];
    }
} else {
    echo "<p>No specialties are currently available. Please check back later.</p>";
}

// Close the database connection
$conn->close();
?>
