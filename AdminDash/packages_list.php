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

$page_title = 'Packages List - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch packages
$sql_packages = "SELECT id, name, price, discounted_price, description, duration, features, image, is_active FROM packages ORDER BY name";
$result_packages = $conn->query($sql_packages);
$packages = [];
if ($result_packages->num_rows > 0) {
    while($row = $result_packages->fetch_assoc()) {
        $package_key = $row['id'];
        $packages[$package_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => (float)$row['price'],
            "discounted_price" => $row['discounted_price'] ? (float)$row['discounted_price'] : (float)$row['price'],
            "desc" => $row['description'] ?: "",
            "duration" => $row['duration'] ?: "",
            "features" => $row['features'] ? json_decode($row['features'], true) : [],
            "image" => $row['image'] ?: "",
            "is_active" => (int)$row['is_active']
        ];
    }
}

// Handle package deletion
if (isset($_GET['delete_package'])) {
    $id = (int)$_GET['delete_package'];
    $sql = "DELETE FROM packages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Package deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting package: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle package status toggle
if (isset($_GET['toggle_package'])) {
    $id = (int)$_GET['toggle_package'];
    $sql = "UPDATE packages SET is_active = 1 - is_active WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Package status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating package status: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Packages List - Admin Dashboard</title>
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
    <!-- Packages List -->
    <div class="section">
      <h2 class="mb-4">Active Packages</h2>
      <?php if (!empty($packages)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Discounted Price</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Features</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($packages as $package) { ?>
          <tr>
            <td><?php echo htmlspecialchars($package['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($package['name'] ?? ''); ?></td>
            <td><?php echo number_format($package['price'], 2); ?></td>
            <td><?php echo number_format($package['discounted_price'], 2); ?></td>
            <td><?php echo htmlspecialchars($package['desc'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($package['duration'] ?? ''); ?></td>
            <td>
              <ul>
                <?php foreach ($package['features'] as $feature) { ?>
                <li><?php echo htmlspecialchars($feature ?? ''); ?></li>
                <?php } ?>
              </ul>
            </td>
            <td>
              <a href="?delete_package=<?php echo htmlspecialchars($package['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this package?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No active packages are currently available. Please check back later.</p>
      <?php } ?>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
