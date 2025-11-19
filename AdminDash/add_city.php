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

$page_title = 'Add City - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Handle city insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_city'])) {
    $name = $_POST['name'];

    $sql = "INSERT INTO cities (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo "<script>alert('City added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding city: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add City - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../assets/css/bootstrap.css" />
  <link rel="stylesheet" href="../assets/css/bootstrap-grid.css" />
  <link rel="stylesheet" href="../assets/css/main.css" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
</head>

<body>
  <!-- Sidebar -->
 <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="content">
    <!-- Add City -->
    <div class="section">
      <h2 class="mb-4">Add New City</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="city_name" class="form-label">City Name</label>
            <input type="text" class="form-control" id="city_name" name="name" required>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_city" class="btn custom-btn">Add City</button>
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
