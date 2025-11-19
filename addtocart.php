<?php
include __DIR__ . '/includes/session.php';
include __DIR__ . '/config/db_connect.php';

// Include both packages and tests data
include __DIR__ . '/data/packages-data.php'; // $packages array
include __DIR__ . '/data/tests-data.php';    // $tests array

// Get item from GET
$itemType = $_GET['type'] ?? null; // "package" or "test"
$itemKey = $_GET['key'] ?? null;

$user_id = $_SESSION['user_id'] ?? null;

$itemData = null;
if ($itemType === "package" && isset($packages[$itemKey])) {
    $itemData = [
        "id" => $itemKey,
        "name" => $packages[$itemKey]['name'],
        "price" => isset($packages[$itemKey]['discounted_price']) ? $packages[$itemKey]['discounted_price'] : $packages[$itemKey]['price'],
        "type" => "package",
        "duration" => $packages[$itemKey]['duration']
    ];
} elseif ($itemType === "test" && isset($tests[$itemKey])) {
    $itemData = [
        "id" => $itemKey,
        "name" => $tests[$itemKey]['name'],
        "price" => isset($tests[$itemKey]['discounted_price']) ? $tests[$itemKey]['discounted_price'] : $tests[$itemKey]['price'],
        "type" => "test",
        "duration" => "N/A"
    ];
}

if ($itemData) {
    if ($user_id) {
        // Logged in: Save to DB
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, item_id, type, quantity) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->bind_param("iis", $user_id, $itemData['id'], $itemData['type']);
        $stmt->execute();
        $stmt->close();
    } else {
        // Anonymous: Save to session
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][] = $itemData;
    }
}

// Redirect to cart page
header("Location: cart.php");
exit;
