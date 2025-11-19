<?php
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../config/db_connect.php';

// Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $doctor_id = (int)($_POST['doctor_id'] ?? 0);
    $specialty_id = (int)($_POST['specialty_id'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $appointment_type = trim($_POST['appointment_type'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Fetch patient details from database
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $errors[] = "User not found.";
    } else {
        $name = $user['full_name'];
        $email = $user['email'];
        $phone = $user['phone'];
    }

    // Basic validation
    $errors = [];

    if (empty($doctor_id)) $errors[] = "Please select a doctor";
    if (empty($specialty_id)) $errors[] = "Specialty is required";
    if (empty($date)) $errors[] = "Date is required";
    if (empty($time)) $errors[] = "Time is required";

    // Date validation (must be today or future)
    if ($date < date('Y-m-d')) {
        $errors[] = "Please select a date today or in the future";
    }

    // Check if doctor exists
    $stmt = $conn->prepare("SELECT id FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 0) {
        $errors[] = "Selected doctor does not exist";
    }

    // Check if specialty exists
    $stmt = $conn->prepare("SELECT id FROM specialties WHERE id = ?");
    $stmt->bind_param("i", $specialty_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 0) {
        $errors[] = "Selected specialty does not exist";
    }

    // Check for conflicting appointment
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
    $stmt->bind_param("iss", $doctor_id, $date, $time);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "This time slot is already booked. Please choose a different time.";
    }

    if (empty($errors)) {
        $patient_id = $_SESSION['user_id'];

        // Combine notes with appointment type
        if (!empty($appointment_type)) {
            $notes = "Type: $appointment_type\n" . $notes;
        }

        // Insert appointment
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, specialty_id, appointment_date, appointment_time, status, notes) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
        $stmt->bind_param("iiisss", $patient_id, $doctor_id, $specialty_id, $date, $time, $notes);

        if ($stmt->execute()) {
            $appointment_id = $conn->insert_id;

            // Get doctor name for confirmation
            $stmt = $conn->prepare("SELECT u.full_name FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.id = ?");
            $stmt->bind_param("i", $doctor_id);
            $stmt->execute();
            $doctor_result = $stmt->get_result();
            $doctor_name = $doctor_result->fetch_assoc()['full_name'];

            // Store in session for confirmation page
            $_SESSION['appointment_confirmation'] = [
                'id' => $appointment_id,
                'name' => $name,
                'doctor' => $doctor_name,
                'date' => $date,
                'time' => $time,
                'type' => $appointment_type,
                'email' => $email
            ];

            // Close connection
            $conn->close();

            // Redirect to confirmation page
            header('Location: appointment-confirmation.php');
            exit;
        } else {
            $errors[] = "Failed to save appointment. Please try again.";
        }
    }

    if (!empty($errors)) {
        // Store errors in session and redirect back
        $_SESSION['appointment_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;

        // Close connection
        $conn->close();

        header('Location: dashboard.php?section=book-appointment');
        exit;
    }
} else {
    // Close connection
    $conn->close();

    // Redirect if accessed directly
    header('Location: dashboard.php?section=book-appointment');
    exit;
}
?>
