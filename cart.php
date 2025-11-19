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
?>

<style>
  :root {
    --bs-bg: #e3f3f6;
    --bs-black: #000 !important;
    --bs-white: #fff !important;
    --bs-red: #db0808ff !important;
    --bs-teal: #00796b;
    --bs-lteal: #72c5bb;
    --bs-br: 0.3em !important;
    --bs-font: 'Roboto', sans-serif !important;
    --bs-hfont: "Montserrat", sans-serif;
    --bs-gradient: linear-gradient(to right, red, blue) !important;
  }

  .top {
    margin-bottom: 15px;
    padding: 10px 5px;
  }

  .btn-danger {
    background: transparent;
    color: var(--bs-red);
    font-family: var(--bs-font);
    border: 2px solid var(--bs-red);
    border-radius: var(--bs-br);
    font-weight: 600;
    transition: 0.3s;
    text-decoration: none !important;
  }

  .btn-danger:hover {
    background: var(--bs-red);
    color: var(--bs-white) !important;
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

<section class="cart-section py-5">
  <div class="container">
    <div class="top">
      <h2 class="text-center">🛒 My Cart</h2>
    </div>

    <?php if (!empty($cart)): ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item</th>
            <th>Type</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart as $index => $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']); ?></td>
              <td><?= htmlspecialchars($item['type'] ?? 'Unknown'); ?></td>
              <td><?= htmlspecialchars($item['duration'] ?? 'N/A'); ?></td>
              <td>₹<?= htmlspecialchars($item['price']); ?></td>
              <td>
                <a href="removecart.php?index=<?= $index; ?>" class="btn btn-danger btn-sm">Remove</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="text-end mt-3">
        <?php
        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
          $total += $item['price'] ?? 0;
        }
        ?>
        <h4>Total: ₹<?= $total; ?></h4>
        <a href="checkout.php" class="btn custom-btn mt-2">Proceed to Checkout</a>
      </div>

    <?php else: ?>
      <p class="text-center">Your cart is empty.</p>
      <div class="text-center mt-3">
        <a href="packages.php" class="btn custom-btn">Back to Packages</a>
        <a href="tests.php" class="btn custom-btn">Back to Tests</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
