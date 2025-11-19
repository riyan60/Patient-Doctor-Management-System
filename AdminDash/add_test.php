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

$page_title = 'Add Test - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch labs
$sql_labs = "SELECT id, name FROM labs ORDER BY name";
$result_labs = $conn->query($sql_labs);
$labs = [];
if ($result_labs->num_rows > 0) {
    while($row = $result_labs->fetch_assoc()) {
        $labs[] = $row;
    }
}

// Handle test insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_test'])) {
    $name = $_POST['name'];
    $lab_id = (int)$_POST['lab_id'];
    $price = (float)$_POST['price'];
    $discounted_price = !empty($_POST['discounted_price']) ? (float)$_POST['discounted_price'] : null;
    $icon = $_POST['icon'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    $sql = "INSERT INTO tests (name, lab_id, price, discounted_price, icon, description, duration, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidssssd", $name, $lab_id, $price, $discounted_price, $icon, $description, $duration, $is_available);

    if ($stmt->execute()) {
        echo "<script>alert('Test added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding test: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Test - Admin Dashboard</title>
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
    <!-- Add Test -->
    <div class="section">
      <h2 class="mb-4">Add New Test</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="test_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="test_name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="test_lab_id" class="form-label">Lab</label>
            <select class="form-select" id="test_lab_id" name="lab_id" required>
              <option value="">Select Lab</option>
              <?php foreach ($labs as $lab) { ?>
              <option value="<?php echo htmlspecialchars($lab['id'] ?? ''); ?>"><?php echo htmlspecialchars($lab['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="test_price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="test_price" name="price" required>
          </div>
          <div class="col-md-6">
            <label for="test_discounted_price" class="form-label">Discounted Price (optional)</label>
            <input type="number" step="0.01" class="form-control" id="test_discounted_price" name="discounted_price">
          </div>
          <div class="col-md-6">
            <label for="test_icon" class="form-label">Icon (emoji or HTML)</label>
            <input type="text" class="form-control" id="test_icon" name="icon" placeholder="&#129298;">
          </div>
          <div class="col-md-6">
            <label for="test_duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="test_duration" name="duration" required>
          </div>
          <div class="col-12">
            <label for="test_description" class="form-label">Description</label>
            <textarea class="form-control" id="test_description" name="description" rows="3" required></textarea>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="test_is_available" name="is_available" checked>
              <label class="form-check-label" for="test_is_available">
                Is Available
              </label>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_test" class="btn custom-btn">Add Test</button>
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
