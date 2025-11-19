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

$page_title = 'Cities List - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch cities
$sql_cities = "SELECT id, name FROM cities ORDER BY name";
$result_cities = $conn->query($sql_cities);
$cities = [];
if ($result_cities->num_rows > 0) {
    while($row = $result_cities->fetch_assoc()) {
        $cities[] = $row;
    }
}

// Handle city deletion
if (isset($_GET['delete_city'])) {
    $id = (int)$_GET['delete_city'];
    $sql = "DELETE FROM cities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('City deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting city: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Cities List - Admin Dashboard</title>
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
    <!-- Cities -->
    <div class="section">
      <h2 class="mb-4">Cities List</h2>
      <?php if (!empty($cities)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cities as $city) { ?>
          <tr>
            <td><?php echo htmlspecialchars($city['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($city['name'] ?? ''); ?></td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCityModal" onclick="editCity(<?php echo htmlspecialchars($city['id'] ?? ''); ?>, '<?php echo htmlspecialchars($city['name'] ?? ''); ?>')">Edit</button>
              <a href="?delete_city=<?php echo htmlspecialchars($city['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this city?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No cities are currently available. Please check back later.</p>
      <?php } ?>
    </div>
  </div>

  <!-- Edit City Modal -->
  <div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="editCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <input type="hidden" id="city_id" name="city_id">
            <div class="mb-3">
              <label for="edit_city_name" class="form-label">City Name</label>
              <input type="text" class="form-control" id="edit_city_name" name="edit_city_name" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_city" class="btn custom-btn">Update City</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
