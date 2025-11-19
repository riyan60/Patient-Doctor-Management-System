<?php
include __DIR__ . '/includes/session.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
include __DIR__ . '/config/db_connect.php';

// Include data for names/prices
include __DIR__ . '/data/packages-data.php';
include __DIR__ . '/data/tests-data.php';

$user_id = $_SESSION['user_id'] ?? null;
$cart = [];

if ($user_id) {
    // Logged in: Load from DB
    $stmt = $conn->prepare("SELECT item_id, type, quantity FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $item_id = $row['item_id'];
        $type = $row['type'];
        $quantity = $row['quantity'];

        $itemData = null;
        if ($type === 'package' && isset($packages[$item_id])) {
            $pkg = $packages[$item_id];
            $itemData = [
                "id" => $item_id,
                "name" => $pkg['name'],
                "price" => isset($pkg['discounted_price']) ? $pkg['discounted_price'] : $pkg['price'],
                "type" => "Package",
                "duration" => $pkg['duration'],
                "quantity" => $quantity
            ];
        } elseif ($type === 'test' && isset($tests[$item_id])) {
            $tst = $tests[$item_id];
            $itemData = [
                "id" => $item_id,
                "name" => $tst['name'],
                "price" => isset($tst['discounted_price']) ? $tst['discounted_price'] : $tst['price'],
                "type" => "Test",
                "duration" => "N/A",
                "quantity" => $quantity
            ];
        }

        if ($itemData) {
            $cart[] = $itemData;
        }
    }
    $stmt->close();
} else {
    // Anonymous: Load from session
    $cart = $_SESSION['cart'] ?? [];
}

// Handle form submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Simple validation
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($cart)) {
        // In a real app, you would store order in database here
        $success = true;

        // Clear cart
        if ($user_id) {
            // Clear from DB
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Clear from session
            unset($_SESSION['cart']);
        }
    }
}
?>

<style>
    :root {
        --bs-bg: #e3f3f6;
        --bs-black: #000 !important;
        --bs-white: #fff !important;
        --bs-teal: #00796b;
        --bs-lteal: #72c5bb;
        --bs-br: 0.3em !important;
        --bs-font: 'Roboto', sans-serif !important;
        --bs-hfont: "Montserrat", sans-serif;
        --bs-gradient: linear-gradient(to right, red, blue) !important;
    }

    .table {
        background-color: var(--bs-white);
        border-radius: var(--bs-br);
    }

    thead {
        background-color: var(--bs-teal);
        color: white;
    }
</style>

<section class="checkout-section py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Checkout</h2>

        <?php if ($success): ?>
            <div class="alert alert-success text-center">
                <h4>Thank you, <?= htmlspecialchars($name); ?>!</h4>
                <p>Your order has been placed successfully.</p>
                <a href="packages.php" class="btn custom-btn mt-2">Back to Home</a>
            </div>
        <?php elseif (!empty($cart)): ?>
            <h4>Order Summary</h4>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td><?= htmlspecialchars($item['type'] ?? 'Unknown'); ?></td>
                            <td><?= htmlspecialchars($item['duration'] ?? 'N/A'); ?></td>
                            <td>₹<?= $item['price']; ?></td>
                        </tr>
                        <?php $total += $item['price'] ?? 0; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h5>Total: ₹<?= $total; ?></h5>

            <hr>

            <h4 class="my-4">Enter Your Details</h4>
            <form method="POST" class="formm">
                <div class="mb-3">
                    <label class="form-label"><strong>Full Name:</strong></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Email:</strong></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Phone Number:</strong></label>
                    <input type="tel" name="phone" class="form-control" required oninput="formatPhone(this)" maxlength="12">
                </div>

                <!-- Responsive centered button -->
                <div class="d-flex justify-content-center mt-5">
                    <button type="submit" class="btn custom-btn w-25">
                        Place Order
                    </button>
                </div>
            </form>


        <?php else: ?>
            <p class="text-center">Your cart is empty.</p>
            <div class="text-center mt-3">
                <a href="packages.php" class="btn custom-btn">Back to Packages</a>
                <a href="tests.php" class="btn custom-btn">Back to Tests</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="main.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
