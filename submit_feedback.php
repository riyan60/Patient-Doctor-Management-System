<?php
include __DIR__ . '/includes/session.php';
include __DIR__ . '/config/db_connect.php';

// Feedback submission handler with database integration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $rating = trim($_POST['rating'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    $errors = [];

    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($rating)) $errors[] = "Rating is required";
    if (empty($message)) $errors[] = "Feedback message is required";

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    if (empty($errors)) {
        // Check if user exists, if not create one
        $user_id = null;

        // Check if user exists by email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
        } else {
            // Create new user (assuming patient role)
            $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
            $password = password_hash('default123', PASSWORD_DEFAULT); // Default password

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'patient')");
            $stmt->bind_param("ssss", $username, $email, $password, $name);
            $stmt->execute();
            $user_id = $conn->insert_id;
        }

        // Insert feedback
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $rating, $message);

        if ($stmt->execute()) {
            // Success - redirect back with success message
            $_SESSION['feedback_success'] = "Thank you for your feedback!";
            $conn->close();
            header('Location: feedback.php');
            exit;
        } else {
            $errors[] = "Failed to save feedback. Please try again.";
        }
    }

    if (!empty($errors)) {
        // Store errors in session and redirect back
        $_SESSION['feedback_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;

        $conn->close();
        header('Location: feedback.php');
        exit;
    }
} else {
    // Close connection
    $conn->close();

    // Redirect if accessed directly
    header('Location: feedback.php');
    exit;
}
?>
