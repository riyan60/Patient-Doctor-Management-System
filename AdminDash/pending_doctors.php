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

$page_title = 'Pending Doctors - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch pending doctors list
$sql_pending_doctors = "SELECT d.id, u.full_name, s.name as specialty_name, d.availability, d.city, d.phone, d.busi_email, d.experience FROM doctors d JOIN users u ON d.user_id = u.id JOIN specialties s ON d.specialty_id = s.id WHERE u.role='doctor' AND u.is_active=0";
$result_pending_doctors = $conn->query($sql_pending_doctors);
$pending_doctors = [];
if ($result_pending_doctors->num_rows > 0) {
    while($row = $result_pending_doctors->fetch_assoc()) {
        $pending_doctors[] = $row;
    }
}

// Handle accept doctor
if (isset($_GET['accept_doctor'])) {
    $id = (int)$_GET['accept_doctor'];
    $sql_user = "UPDATE users SET is_active = 1 WHERE id = (SELECT user_id FROM doctors WHERE id = ?)";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $id);

    $sql_doctor = "UPDATE doctors SET is_active = 1 WHERE id = ?";
    $stmt_doctor = $conn->prepare($sql_doctor);
    $stmt_doctor->bind_param("i", $id);

    if ($stmt_user->execute() && $stmt_doctor->execute()) {
        echo "<script>alert('Doctor accepted successfully!');</script>";
        header("Location: pending_doctors.php");
        exit();
    } else {
        echo "<script>alert('Error accepting doctor: " . $stmt_user->error . " " . $stmt_doctor->error . "');</script>";
    }
    $stmt_user->close();
    $stmt_doctor->close();
}

// Handle reject doctor
if (isset($_GET['reject_doctor'])) {
    $id = (int)$_GET['reject_doctor'];
    $sql = "DELETE FROM doctors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $sql_user = "DELETE FROM users WHERE id = (SELECT user_id FROM doctors WHERE id = ?)";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $id);
        $stmt_user->execute();
        $stmt_user->close();
        echo "<script>alert('Doctor rejected and removed successfully!');</script>";
        header("Location: pending_doctors.php");
        exit();
    } else {
        echo "<script>alert('Error rejecting doctor: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Pending Doctors - Admin Dashboard</title>
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
    <!-- Pending Doctors -->
    <div class="section">
      <h2 class="mb-4">Pending Doctors</h2>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Specialization</th>
            <th>City</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Experience</th>
            <th>Availability</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pending_doctors as $doctor) { ?>
          <tr>
            <td><?php echo htmlspecialchars($doctor['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['full_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['specialty_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['city'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['phone'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['busi_email'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['experience'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($doctor['availability'] ?? ''); ?></td>
            <td>
              <a href="?accept_doctor=<?php echo htmlspecialchars($doctor['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to accept this doctor?')" class="btn btn-success btn-sm">Accept</a>
              <a href="?reject_doctor=<?php echo htmlspecialchars($doctor['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to reject this doctor?')" class="btn btn-danger btn-sm">Reject</a>
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
