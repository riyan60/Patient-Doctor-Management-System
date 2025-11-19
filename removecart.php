
<?php
include __DIR__ . '/includes/session.php';
include __DIR__ . '/config/db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;
$index = $_GET['index'] ?? null;

if ($user_id && $index !== null) {
    // Logged in: Remove from DB
    // Get the item at index from cart (need to load cart first)
    include __DIR__ . '/data/packages-data.php';
    include __DIR__ . '/data/tests-data.php';

    $stmt = $conn->prepare("SELECT item_id, type FROM cart_items WHERE user_id = ? ORDER BY added_at ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (isset($cart_items[$index])) {
        $item = $cart_items[$index];
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND item_id = ? AND type = ? LIMIT 1");
        $stmt->bind_param("iis", $user_id, $item['item_id'], $item['type']);
        $stmt->execute();
        $stmt->close();
    }
} elseif ($index !== null) {
    // Anonymous: Remove from session
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex after removal
    }
}

header("Location: cart.php");
exit;

