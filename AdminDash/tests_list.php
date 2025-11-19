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

$page_title = 'Tests List - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch tests
$sql_tests = "SELECT t.id, t.name, l.name as lab_name, t.price, t.discounted_price, t.icon, t.description, t.duration, t.is_available FROM tests t LEFT JOIN labs l ON t.lab_id = l.id ORDER BY t.name";
$result_tests = $conn->query($sql_tests);
$tests = [];
$tests_count = 0;
if ($result_tests->num_rows > 0) {
    $tests_count = $result_tests->num_rows;
    while($row = $result_tests->fetch_assoc()) {
        $test_key = $row['id'];
        $tests[$test_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "lab_name" => $row['lab_name'] ?: "N/A",
            "price" => (float)$row['price'],
            "discounted_price" => $row['discounted_price'] ? (float)$row['discounted_price'] : (float)$row['price'],
            "icon" => $row['icon'] ?: "&#129298;",
            "desc" => $row['description'] ?: "Medical test for health assessment.",
            "duration" => $row['duration'] ?: "N/A"
        ];
    }
}

// Handle test deletion
if (isset($_GET['delete_test'])) {
    $id = (int)$_GET['delete_test'];
    $sql = "DELETE FROM tests WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Test deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting test: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle test status toggle
if (isset($_GET['toggle_test'])) {
    $id = (int)$_GET['toggle_test'];
    $sql = "UPDATE tests SET is_available = 1 - is_available WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Test status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating test status: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Tests List - Admin Dashboard</title>
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
    <!-- Tests -->
    <div class="section">
      <h2 class="mb-4">Available Tests</h2>
      <?php if (!empty($tests)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Lab Name</th>
            <th>Price</th>
            <th>Discounted Price</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tests as $test) { ?>
          <tr>
            <td><?php echo htmlspecialchars($test['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($test['name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($test['lab_name'] ?? ''); ?></td>
            <td><?php echo number_format($test['price'], 2); ?></td>
            <td><?php echo number_format($test['discounted_price'], 2); ?></td>
            <td><?php echo htmlspecialchars($test['desc'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($test['duration'] ?? ''); ?></td>
            <td>
              <a href="?delete_test=<?php echo htmlspecialchars($test['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this test?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No medical tests are currently available. Please check back later.</p>
      <?php } ?>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
