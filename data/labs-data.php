<?php
include __DIR__ . '/../config/db_connect.php';

// Static services data
$services = [
    ["title" => "Blood Tests", "desc" => "Accurate blood analysis for diagnosis and monitoring health conditions."],
    ["title" => "X-Ray & Imaging", "desc" => "State-of-the-art radiology and diagnostic imaging services."],
    ["title" => "COVID-19 Testing", "desc" => "Safe and reliable PCR & rapid testing facilities available."],
    ["title" => "Microbiology", "desc" => "Advanced microbiology labs for infection detection and control."]
];

// Fetch labs data from database
$sql = "SELECT id, name, city, address, phone, services, image FROM labs ORDER BY name";
$result = $conn->query($sql);

$labs = [];
$labs_count = 0;
if ($result->num_rows > 0) {
    $labs_count = $result->num_rows;
    while($row = $result->fetch_assoc()) {
        $lab_key = $row['id']; // Use id as key
        $labs[$lab_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "city" => $row['city'],
            "address" => $row['address'],
            "phone" => $row['phone'],
            "services" => $row['services'] ? json_decode($row['services'], true) : [],
            "img" => $row['image'] ?: "https://picsum.photos/id/1012/600/300" // Default image if none
        ];
    }
} else {
    echo "<p>No labs are currently available. Please check back later.</p>";
}

// Filter by selected city if cookie is set
if (isset($_COOKIE['selected_city']) && $_COOKIE['selected_city'] !== '') {
    $selectedCity = strtolower($_COOKIE['selected_city']);
    $labs = array_filter($labs, function($lab) use ($selectedCity) {
        return strtolower($lab['city']) === $selectedCity;
    });
}

// Close the database connection
// $conn->close(); // Commented out to keep connection open for Admin.php
?>
