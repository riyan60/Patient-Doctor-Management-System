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

$page_title = 'Labs List - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch labs
$sql_labs = "SELECT id, name, city, address, phone, services FROM labs ORDER BY name";
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Labs List - Admin Dashboard</title>
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
    <!-- Labs List -->
    <div class="section">
      <h2 class="mb-4">Registered Labs</h2>
      <?php if (!empty($labs)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>Name</th>
            <th>City</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Services</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($labs as $lab) { ?>
          <tr>
            <td><?php echo htmlspecialchars($lab['name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($lab['city'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($lab['address'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($lab['phone'] ?? ''); ?></td>
            <td>
              <ul>
                <?php foreach ($lab['services'] as $service) { ?>
                <li><?php echo htmlspecialchars($service ?? ''); ?></li>
                <?php } ?>
              </ul>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No labs are currently available. Please check back later.</p>
      <?php } ?>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
