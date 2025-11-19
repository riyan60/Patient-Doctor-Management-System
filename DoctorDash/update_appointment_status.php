<?php
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../config/db_connect.php';

// Check if doctor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $status = $_POST['status'];

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = 'Invalid status.';
        header('Location: dashboard.php');
        exit;
    }

    // Check if appointment belongs to the doctor
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND doctor_id = (SELECT id FROM doctors WHERE user_id = ?)");
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Appointment not found or access denied.';
        $stmt->close();
        header('Location: dashboard.php');
        exit;
    }
    $stmt->close();

    // Update status
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Appointment status updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update appointment status.';
    }
    $stmt->close();
}

$conn->close();
header('Location: dashboard.php');
exit;
?>
