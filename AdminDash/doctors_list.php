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

$page_title = 'Doctors List - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch doctors list for admin
$sql_admin_doctors = "SELECT d.id, u.full_name, s.name as specialty_name, d.availability, d.city, d.rating, d.phone, d.busi_email, d.experience, GROUP_CONCAT(sv.name SEPARATOR ', ') as services FROM doctors d JOIN users u ON d.user_id = u.id JOIN specialties s ON d.specialty_id = s.id LEFT JOIN doctor_services ds ON d.id = ds.doctor_id LEFT JOIN services sv ON ds.service_id = sv.id WHERE u.is_active = 1 AND d.is_active = 1 GROUP BY d.id ORDER BY u.full_name";
$result_admin_doctors = $conn->query($sql_admin_doctors);
$admin_doctors = [];
if ($result_admin_doctors->num_rows > 0) {
    while($row = $result_admin_doctors->fetch_assoc()) {
        $admin_doctors[] = $row;
    }
}

// Handle doctor deletion
if (isset($_GET['delete_doctor'])) {
    $id = (int)$_GET['delete_doctor'];
    $sql = "UPDATE doctors SET is_active = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Doctor deactivated successfully!');</script>";
        // Refresh the page to update the list
        header("Location: doctors_list.php");
        exit();
    } else {
        echo "<script>alert('Error deactivating doctor: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Doctors List - Admin Dashboard</title>
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
    <!-- Doctors -->
    <div class="section">
      <h2 class="mb-4">Doctors List</h2>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Specialization</th>
            <th>City</th>
            <th>Rating</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Experience</th>
            <th>Available Timing</th>
            <th>Services</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($admin_doctors as $doctor) { ?>
          <tr>
            <td><?php echo htmlspecialchars($doctor['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['full_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['specialty_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['city'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['rating'] ?? '0'); ?> ⭐</td>
            <td><?php echo htmlspecialchars($doctor['phone'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['busi_email'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['experience'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['availability'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['services'] ?? ''); ?></td>
            <td>
              <a href="?delete_doctor=<?php echo htmlspecialchars($doctor['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this doctor?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
