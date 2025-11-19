<?php
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../config/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo '<!DOCTYPE html><html><head><title>Admin Login Required</title><link rel="stylesheet" href="../assets/css/bootstrap.css"><style>body { filter: blur(5px); } .modal { display: block; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; outline: 0; background-color: rgba(0,0,0,0.5); } .modal-dialog { position: relative; width: auto; margin: 10rem auto; pointer-events: none; } .modal-content { position: relative; display: flex; flex-direction: column; width: 100%; pointer-events: auto; background-color: #fff; background-clip: padding-box; border: 1px solid rgba(0,0,0,.2); border-radius: .3rem; outline: 0; }</style></head><body>';
    echo '<div class="modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Admin Login Required</h5></div><div class="modal-body"><p>You need to log in as an admin to access this page.</p></div><div class="modal-footer"><a href="../login.php" class="btn custom-btn">Login</a></div></div></div></div>';
    echo '</body></html>';
    exit();
}

$page_title = 'Add Package - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Handle package insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_package'])) {
    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $discounted_price = !empty($_POST['discounted_price']) ? (float)$_POST['discounted_price'] : null;
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $features = json_encode(array_filter(explode("\n", $_POST['features'])));
    $image = $_POST['image'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "INSERT INTO packages (name, price, discounted_price, description, duration, features, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddssssd", $name, $price, $discounted_price, $description, $duration, $features, $image, $is_active);

    if ($stmt->execute()) {
        echo "<script>alert('Package added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding package: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Package - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../assets/css/bootstrap.css" />
  <link rel="stylesheet" href="../assets/css/bootstrap-grid.css" />
  <link rel="stylesheet" href="../assets/css/bootstrap-reboot.css" />
  <link rel="stylesheet" href="../assets/css/main.css" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
</head>

<body>
  <!-- Sidebar -->
 <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="content">
    <!-- Add Package -->
    <div class="section">
      <h2 class="mb-4">Add New Package</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="package_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="package_name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="package_price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="package_price" name="price" required>
          </div>
          <div class="col-md-6">
            <label for="package_discounted_price" class="form-label">Discounted Price (optional)</label>
            <input type="number" step="0.01" class="form-control" id="package_discounted_price" name="discounted_price">
          </div>
          <div class="col-md-6">
            <label for="package_duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="package_duration" name="duration" required>
          </div>
          <div class="col-12">
            <label for="package_description" class="form-label">Description</label>
            <textarea class="form-control" id="package_description" name="description" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <label for="package_features" class="form-label">Features (one per line)</label>
            <textarea class="form-control" id="package_features" name="features" rows="3" placeholder="Consultation&#10;Basic Tests&#10;Follow-up"></textarea>
          </div>
          <div class="col-md-6">
            <label for="package_image" class="form-label">Image URL</label>
            <input type="text" class="form-control" id="package_image" name="image">
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="package_is_active" name="is_active" checked>
              <label class="form-check-label" for="package_is_active">
                Is Active
              </label>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_package" class="btn custom-btn">Add Package</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
