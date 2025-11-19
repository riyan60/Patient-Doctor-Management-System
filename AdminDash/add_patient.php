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

$page_title = 'Add Patient - Admin Dashboard';
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

// Handle patient insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_patient'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    $sql = "INSERT INTO users (username, email, password, full_name, phone, city, date_of_birth, gender, address, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'patient', 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $username, $email, $password, $full_name, $phone, $city, $date_of_birth, $gender, $address);

    if ($stmt->execute()) {
        echo "<script>alert('Patient added successfully!');</script>";
        // Refresh patients list
        $result_patients_list = $conn->query($sql_patients_list);
        $patients_list = [];
        if ($result_patients_list->num_rows > 0) {
            while($row = $result_patients_list->fetch_assoc()) {
                $patients_list[] = $row;
            }
        }
    } else {
        echo "<script>alert('Error adding patient: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Patient - Admin Dashboard</title>
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
    <!-- Add Patient -->
    <div class="section">
      <h2 class="mb-4">Add New Patient</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="patient_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="patient_username" name="username" required>
          </div>
          <div class="col-md-6">
            <label for="patient_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="patient_email" name="email" required>
          </div>
          <div class="col-md-6">
            <label for="patient_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="patient_password" name="password" required>
          </div>
          <div class="col-md-6">
            <label for="patient_full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="patient_full_name" name="full_name" required>
          </div>
          <div class="col-md-6">
            <label for="patient_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="patient_phone" name="phone" required>
          </div>
          <div class="col-md-6">
            <label for="patient_city" class="form-label">City</label>
            <select class="form-select" id="patient_city" name="city" required>
              <option value="">Select City</option>
              <?php foreach ($cities as $city) { ?>
              <option value="<?php echo htmlspecialchars($city['name'] ?? ''); ?>"><?php echo htmlspecialchars($city['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="patient_date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="patient_date_of_birth" name="date_of_birth" required>
          </div>
          <div class="col-md-6">
            <label for="patient_gender" class="form-label">Gender</label>
            <select class="form-select" id="patient_gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-12">
            <label for="patient_address" class="form-label">Address</label>
            <textarea class="form-control" id="patient_address" name="address" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_patient" class="btn custom-btn">Add Patient</button>
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
