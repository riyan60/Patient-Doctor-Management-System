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

$page_title = 'Add Doctor - Admin Dashboard';
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

// Fetch specialties
$sql_specialties = "SELECT id, name FROM specialties ORDER BY name";
$result_specialties = $conn->query($sql_specialties);
$specialties = [];
if ($result_specialties->num_rows > 0) {
    while($row = $result_specialties->fetch_assoc()) {
        $specialties[] = $row;
    }
}

// Handle doctor insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_doctor'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $specialty_id = (int)$_POST['specialty_id'];
    $city = $_POST['city'];
    $experience = $_POST['experience'];
    $availability = $_POST['availability'];
    $busi_email = $_POST['busi_email'];

    // Insert into users table
    $sql_user = "INSERT INTO users (username, email, password, full_name, phone, city, role, is_active) VALUES (?, ?, ?, ?, ?, ?, 'doctor', 1)";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("ssssss", $username, $email, $password, $full_name, $phone, $city);

    if ($stmt_user->execute()) {
        $user_id = $stmt_user->insert_id;
        // Insert into doctors table
        $sql_doctor = "INSERT INTO doctors (user_id, specialty_id, city, phone, busi_email, experience, availability, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt_doctor = $conn->prepare($sql_doctor);
        $stmt_doctor->bind_param("iisssss", $user_id, $specialty_id, $city, $phone, $busi_email, $experience, $availability);

        if ($stmt_doctor->execute()) {
            echo "<script>alert('Doctor added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding doctor details: " . $stmt_doctor->error . "');</script>";
        }
        $stmt_doctor->close();
    } else {
        echo "<script>alert('Error adding doctor user: " . $stmt_user->error . "');</script>";
    }
    $stmt_user->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Doctor - Admin Dashboard</title>
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
    <!-- Add Doctor -->
    <div class="section">
      <h2 class="mb-4">Add New Doctor</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="doctor_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="doctor_username" name="username" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="doctor_email" name="email" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="doctor_password" name="password" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="doctor_full_name" name="full_name" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="doctor_phone" name="phone" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_specialty_id" class="form-label">Specialty</label>
            <select class="form-select" id="doctor_specialty_id" name="specialty_id" required>
              <option value="">Select Specialty</option>
              <?php foreach ($specialties as $specialty) { ?>
              <option value="<?php echo htmlspecialchars($specialty['id'] ?? ''); ?>"><?php echo htmlspecialchars($specialty['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="doctor_city" class="form-label">City</label>
            <select class="form-select" id="doctor_city" name="city" required>
              <option value="">Select City</option>
              <?php foreach ($cities as $city) { ?>
              <option value="<?php echo htmlspecialchars($city['name'] ?? ''); ?>"><?php echo htmlspecialchars($city['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="doctor_experience" class="form-label">Experience</label>
            <input type="text" class="form-control" id="doctor_experience" name="experience" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_availability" class="form-label">Availability</label>
            <input type="text" class="form-control" id="doctor_availability" name="availability" required>
          </div>
          <div class="col-md-6">
            <label for="doctor_busi_email" class="form-label">Business Email</label>
            <input type="email" class="form-control" id="doctor_busi_email" name="busi_email" required>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_doctor" class="btn custom-btn">Add Doctor</button>
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
