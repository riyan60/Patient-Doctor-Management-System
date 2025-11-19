<?php
include __DIR__ . '/../config/db_connect.php';

$cities = [];
$sql = "SELECT name FROM cities ORDER BY name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cities[] = $row['name'];
    }
}
$conn->close();
?>
