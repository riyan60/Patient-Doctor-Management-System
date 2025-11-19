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

$page_title = 'Patients List - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch patients list
$sql_patients_list = "SELECT id, full_name, email, phone, city, date_of_birth, gender, address, created_at FROM users WHERE role = 'patient' AND is_active = 1 ORDER BY full_name";
$result_patients_list = $conn->query($sql_patients_list);
$patients_list = [];
if ($result_patients_list->num_rows > 0) {
    while($row = $result_patients_list->fetch_assoc()) {
        $patients_list[] = $row;
    }
}

// Handle patient details view
$patient_details = null;
$patient_appointments = [];
$patient_medical_history = [];
$patient_prescriptions = [];
if (isset($_GET['patient_id'])) {
    $patient_id = (int)$_GET['patient_id'];
    // Fetch patient details
    $sql_patient = "SELECT id, full_name, email, phone, city, date_of_birth, gender, address, created_at FROM users WHERE id = ? AND role = 'patient' AND is_active = 1";
    $stmt = $conn->prepare($sql_patient);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient_details = $result->fetch_assoc();
    $stmt->close();

    if ($patient_details) {
        // Fetch appointments
        $sql_appts = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes, d.full_name as doctor_name, s.name as specialty_name FROM appointments a JOIN doctors doc ON a.doctor_id = doc.id JOIN users d ON doc.user_id = d.id JOIN specialties s ON a.specialty_id = s.id WHERE a.patient_id = ? ORDER BY a.appointment_date DESC";
        $stmt_appts = $conn->prepare($sql_appts);
        $stmt_appts->bind_param("i", $patient_id);
        $stmt_appts->execute();
        $result_appts = $stmt_appts->get_result();
        while($row = $result_appts->fetch_assoc()) {
            $patient_appointments[] = $row;
        }
        $stmt_appts->close();

        // Fetch medical history
        $sql_history = "SELECT mh.id, mh.visit_date, mh.conditions, mh.allergies, mh.notes, d.full_name as doctor_name FROM medical_history mh JOIN doctors doc ON mh.doctor_id = doc.id JOIN users d ON doc.user_id = d.id WHERE mh.patient_id = ? ORDER BY mh.visit_date DESC";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->bind_param("i", $patient_id);
        $stmt_history->execute();
        $result_history = $stmt_history->get_result();
        while($row = $result_history->fetch_assoc()) {
            $patient_medical_history[] = $row;
        }
        $stmt_history->close();

        // Fetch prescriptions
        $sql_pres = "SELECT p.id, p.medication, p.notes, p.created_at, d.full_name as doctor_name FROM prescriptions p JOIN doctors doc ON p.doctor_id = doc.id JOIN users d ON doc.user_id = d.id WHERE p.patient_id = ? ORDER BY p.created_at DESC";
        $stmt_pres = $conn->prepare($sql_pres);
        $stmt_pres->bind_param("i", $patient_id);
        $stmt_pres->execute();
        $result_pres = $stmt_pres->get_result();
        while($row = $result_pres->fetch_assoc()) {
            $patient_prescriptions[] = $row;
        }
        $stmt_pres->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Patients List - Admin Dashboard</title>
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
    <!-- Patients -->
    <div id="patients_list" class="section">
      <h2 class="mb-4">Registered Patients</h2>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Address</th>
            <th>Registration Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($patients_list as $patient) { ?>
          <tr>
            <td><?php echo htmlspecialchars($patient['id'] ?? ''); ?></td>
            <td><a href="?patient_id=<?php echo htmlspecialchars($patient['id']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($patient['full_name'] ?? ''); ?></a></td>
            <td><?php echo htmlspecialchars($patient['email'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($patient['phone'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($patient['city'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($patient['date_of_birth'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($patient['gender'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($patient['address'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($patient['created_at'] ?? ''); ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Patient Details -->
    <?php if ($patient_details): ?>
    <div id="patient_details" class="section">
      <h2 class="mb-4">Patient Details: <?php echo htmlspecialchars($patient_details['full_name']); ?></h2>
      <a href="patients_list.php" class="btn btn-secondary mb-3">Back to Patients List</a>

      <!-- Basic Info -->
      <div class="card mb-4">
        <div class="card-header">
          <h5>Basic Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Full Name:</strong> <?php echo htmlspecialchars($patient_details['full_name']); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($patient_details['email']); ?></p>
              <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient_details['phone']); ?></p>
            </div>
            <div class="col-md-6">
              <p><strong>City:</strong> <?php echo htmlspecialchars($patient_details['city']); ?></p>
              <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient_details['date_of_birth']); ?></p>
              <p><strong>Gender:</strong> <?php echo htmlspecialchars($patient_details['gender']); ?></p>
            </div>
          </div>
          <p><strong>Address:</strong> <?php echo htmlspecialchars($patient_details['address']); ?></p>
          <p><strong>Registration Date:</strong> <?php echo htmlspecialchars($patient_details['created_at']); ?></p>
        </div>
      </div>

      <!-- Appointments -->
      <div class="card mb-4">
        <div class="card-header">
          <h5>Appointments</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($patient_appointments)): ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Specialty</th>
                <th>Status</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($patient_appointments as $appt): ?>
              <tr>
                <td><?php echo htmlspecialchars($appt['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($appt['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($appt['specialty_name']); ?></td>
                <td><?php echo htmlspecialchars($appt['status']); ?></td>
                <td><?php echo htmlspecialchars($appt['notes']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p>No appointments found for this patient.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Medical History -->
      <div class="card mb-4">
        <div class="card-header">
          <h5>Medical History</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($patient_medical_history)): ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Visit Date</th>
                <th>Doctor</th>
                <th>Conditions</th>
                <th>Allergies</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($patient_medical_history as $history): ?>
              <tr>
                <td><?php echo htmlspecialchars($history['visit_date']); ?></td>
                <td><?php echo htmlspecialchars($history['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($history['conditions']); ?></td>
                <td><?php echo htmlspecialchars($history['allergies']); ?></td>
                <td><?php echo htmlspecialchars($history['notes']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p>No medical history found for this patient.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Prescriptions -->
      <div class="card mb-4">
        <div class="card-header">
          <h5>Prescriptions</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($patient_prescriptions)): ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Medication</th>
                <th>Doctor</th>
                <th>Notes</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($patient_prescriptions as $pres): ?>
              <tr>
                <td><?php echo htmlspecialchars($pres['medication']); ?></td>
                <td><?php echo htmlspecialchars($pres['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($pres['notes']); ?></td>
                <td><?php echo htmlspecialchars($pres['created_at']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p>No prescriptions found for this patient.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
