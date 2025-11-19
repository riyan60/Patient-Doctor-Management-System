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

$page_title = 'Appointments - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Handle filters and pagination for appointments
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // appointments per page
$offset = ($page - 1) * $limit;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where_clauses = [];
$params = [];
$types = '';

if (!empty($date_from)) {
    $where_clauses[] = "a.appointment_date >= ?";
    $params[] = $date_from;
    $types .= 's';
}
if (!empty($date_to)) {
    $where_clauses[] = "a.appointment_date <= ?";
    $params[] = $date_to;
    $types .= 's';
}
if (!empty($status_filter)) {
    $where_clauses[] = "a.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Count total appointments with filters

$sql_count = "SELECT COUNT(*) as total FROM appointments a JOIN users p ON a.patient_id = p.id JOIN doctors doc ON a.doctor_id = doc.id JOIN users d ON doc.user_id = d.id JOIN specialties s ON a.specialty_id = s.id $where_sql";

if (!empty($params)) {

    $stmt_count = $conn->prepare($sql_count);

    if (!$stmt_count) {

        error_log("Prepare count failed: " . $conn->error);

        $total = 0;

    } else {

        $stmt_count->bind_param($types, ...$params);

        if (!$stmt_count->execute()) {

            error_log("Execute count failed: " . $stmt_count->error);

            $total = 0;

        } else {

            $result = $stmt_count->get_result();

            if (!$result) {

                error_log("Get result count failed: " . $stmt_count->error);

                $total = 0;

            } else {

                $total = $result->fetch_assoc()['total'] ?? 0;

            }

        }

        $stmt_count->close();

    }

} else {

    $result_count = $conn->query($sql_count);

    if (!$result_count) {

        error_log("Count query failed: " . $conn->error);

        $total = 0;

    } else {

        $total = $result_count->fetch_assoc()['total'] ?? 0;

    }

}

$total_pages = ceil($total / $limit);

// Fetch appointments with pagination and filters
$sql_appointments = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes, p.full_name as patient_name, d.full_name as doctor_name, s.name as specialty_name FROM appointments a JOIN users p ON a.patient_id = p.id JOIN doctors doc ON a.doctor_id = doc.id JOIN users d ON doc.user_id = d.id JOIN specialties s ON a.specialty_id = s.id $where_sql ORDER BY a.appointment_date DESC, a.appointment_time DESC LIMIT $limit OFFSET $offset";
if (!empty($params)) {
    $stmt = $conn->prepare($sql_appointments);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result_appointments = $stmt->get_result();
} else {
    $result_appointments = $conn->query($sql_appointments);
}
$appointments = [];
if ($result_appointments->num_rows > 0) {
    while($row = $result_appointments->fetch_assoc()) {
        $appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Appointments - Admin Dashboard</title>
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
    <!-- Appointments -->
    <div class="section">
      <h2 class="mb-4">All Appointments</h2>

      <!-- Filters -->
      <form method="GET" class="mb-4">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="date_from" class="form-label">Date From</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
          </div>
          <div class="col-md-3">
            <label for="date_to" class="form-label">Date To</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
              <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
              <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn custom-btn">Filter</button>
            <a href="appointments.php" class="btn btn-secondary ms-2">Clear</a>
          </div>
        </div>
      </form>

      <?php if (!empty($appointments)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Specialty</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($appointments as $appointment) { ?>
          <tr>
            <td><?php echo htmlspecialchars($appointment['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['patient_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['doctor_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['specialty_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['status'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <nav aria-label="Appointments pagination">
        <ul class="pagination justify-content-center">
          <?php if ($page > 1) { ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&status=<?php echo urlencode($status_filter); ?>">Previous</a>
          </li>
          <?php } ?>

          <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
          <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&status=<?php echo urlencode($status_filter); ?>"><?php echo $i; ?></a>
          </li>
          <?php } ?>

          <?php if ($page < $total_pages) { ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&status=<?php echo urlencode($status_filter); ?>">Next</a>
          </li>
          <?php } ?>
        </ul>
      </nav>
      <?php } else { ?>
      <p>No appointments are currently available.</p>
      <?php } ?>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
