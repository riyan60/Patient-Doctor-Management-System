<?php
// Centralized session initialization
if (session_status() == PHP_SESSION_NONE) {
    // Set session cookie to root path for subdirectories
    session_set_cookie_params(0, '/');
    session_start();
}

// Generate a unique session ID if not already set
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid('session_', true);
}
?>
