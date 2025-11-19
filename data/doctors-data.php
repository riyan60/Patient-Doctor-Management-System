<?php
include __DIR__ . '/../config/db_connect.php';

// Fetch doctors data from database
$sql = "SELECT d.id, d.user_id, d.specialty_id, d.experience, d.availability, d.city, d.phone, d.busi_email, u.full_name, u.email, s.name as specialty_name, s.image as specialty_image, GROUP_CONCAT(serv.name SEPARATOR ', ') as doctor_services FROM doctors d JOIN users u ON d.user_id = u.id JOIN specialties s ON d.specialty_id = s.id LEFT JOIN doctor_services ds ON d.id = ds.doctor_id LEFT JOIN services serv ON ds.service_id = serv.id WHERE u.is_active = 1 AND d.is_active = 1 GROUP BY d.id ORDER BY s.name, u.full_name";
$result = $conn->query($sql);

$doctors = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $specialty = $row['specialty_name'];
        if (!isset($doctors[$specialty])) {
            $doctors[$specialty] = [];
        }
        $img = str_replace('pics/', 'assets/images/', $row['specialty_image']) ?: "assets/images/default.png";
        $services = !empty($row['doctor_services']) ? explode(', ', $row['doctor_services']) : ["General Consultation"];

        $doctors[$specialty][] = [
            "id" => $row['id'],
            "name" => "Dr. " . $row['full_name'],
            "img" => $img,
            "experience" => $row['experience'] . "+ years",
            "available" => $row['availability'],
            "city" => $row['city'],
            "rating" => 4.5, // Default rating, can be from db if added
            "phone" => $row['phone'],
            "email" => $row['email'],
            "busi_email" => $row['busi_email'],
            "services" => $services
        ];
    }
} else {
    echo "<p>No doctors are currently available. Please check back later.</p>";
}

// Connection will be closed by the including script

if (isset($_COOKIE['selected_city']) && $_COOKIE['selected_city'] !== '') {
    $selectedCity = strtolower($_COOKIE['selected_city']);
    foreach ($doctors as $specialty => &$docList) {
        $docList = array_filter($docList, function($doc) use ($selectedCity) {
            return strtolower($doc['city']) === $selectedCity;
        });
    }
}
?>
