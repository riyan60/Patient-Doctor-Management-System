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

$page_title = 'Add Lab - Admin Dashboard';
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

// Handle lab insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_lab'])) {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $services = json_encode(array_filter(explode("\n", $_POST['services'])));

    $sql = "INSERT INTO labs (name, city, address, phone, services) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $city, $address, $phone, $services);

    if ($stmt->execute()) {
        echo "<script>alert('Lab added successfully!');</script>";
        // Refresh labs list
        $result_labs = $conn->query($sql_labs);
        $labs = [];
        $labs_count = 0;
        if ($result_labs->num_rows > 0) {
            $labs_count = $result_labs->num_rows;
            while($row = $result_labs->fetch_assoc()) {
                $lab_key = $row['id'];
                $labs[$lab_key] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "city" => $row['city'],
                    "address" => $row['address'],
                    "phone" => $row['phone'],
                    "services" => $row['services'] ? json_decode($row['services'], true) : []
                ];
            }
        }
    } else {
        echo "<script>alert('Error adding lab: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Lab - Admin Dashboard</title>
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
    <!-- Add Lab -->
    <div class="section">
      <h2 class="mb-4">Add New Lab</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="lab_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="lab_name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="lab_city" class="form-label">City</label>
            <select class="form-select" id="lab_city" name="city" required>
              <option value="">Select City</option>
              <?php foreach ($cities as $city) { ?>
              <option value="<?php echo htmlspecialchars($city['name'] ?? ''); ?>"><?php echo htmlspecialchars($city['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="lab_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="lab_phone" name="phone" required>
          </div>
          <div class="col-md-6">
            <label for="lab_address" class="form-label">Address</label>
            <textarea class="form-control" id="lab_address" name="address" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <label for="lab_services" class="form-label">Services (one per line)</label>
            <textarea class="form-control" id="lab_services" name="services" rows="3" placeholder="Blood Test&#10;X-Ray&#10;MRI"></textarea>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_lab" class="btn custom-btn">Add Lab</button>
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
