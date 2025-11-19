<?php
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../config/db_connect.php';

// Check if doctor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
  echo '<!DOCTYPE html><html><head><title>Doctor Login Required</title><link rel="stylesheet" href="../assets/css/bootstrap.css"><link rel="stylesheet" href="../assets/css/dashboard.css"></head><body class="blur">';
  echo '<div class="modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Doctor Login Required</h5></div><div class="modal-body"><p>You need to log in as a doctor to access this page.</p></div><div class="modal-footer"><a href="../login.php" class="btn custom-btn">Login</a></div></div></div></div>';
  echo '</body></html>';
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch doctor profile
$sql_profile = "SELECT u.id, u.full_name, u.email, u.phone, u.city, u.date_of_birth, u.gender, u.address, d.experience, d.availability, d.rating, d.busi_email, d.image FROM users u JOIN doctors d ON u.id = d.user_id WHERE u.id = ? AND u.role = 'doctor'";
$stmt_profile = $conn->prepare($sql_profile);
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
$profile = $result_profile->fetch_assoc();
$stmt_profile->close();

// Fetch appointments
$sql_appointments = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes, p.full_name as patient_name, s.name as specialty_name FROM appointments a JOIN users p ON a.patient_id = p.id JOIN specialties s ON a.specialty_id = s.id WHERE a.doctor_id = (SELECT id FROM doctors WHERE user_id = ?) ORDER BY a.appointment_date, a.appointment_time";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $user_id);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();
$appointments = [];
while ($row = $result_appointments->fetch_assoc()) {
  $appointments[] = $row;
}
$stmt_appointments->close();

// Fetch patients
$sql_patients = "SELECT DISTINCT u.id, u.full_name, u.email, u.phone FROM users u JOIN appointments a ON u.id = a.patient_id WHERE a.doctor_id = (SELECT id FROM doctors WHERE user_id = ?) ORDER BY u.full_name";
$stmt_patients = $conn->prepare($sql_patients);
$stmt_patients->bind_param("i", $user_id);
$stmt_patients->execute();
$result_patients = $stmt_patients->get_result();
$patients = [];
while ($row = $result_patients->fetch_assoc()) {
  $patients[] = $row;
}
$stmt_patients->close();

// Fetch prescriptions
$sql_prescriptions = "SELECT p.id, p.medication, p.notes, p.file_path, p.created_at, u.full_name as patient_name FROM prescriptions p JOIN users u ON p.patient_id = u.id WHERE p.doctor_id = (SELECT id FROM doctors WHERE user_id = ?) ORDER BY p.created_at DESC";
$stmt_prescriptions = $conn->prepare($sql_prescriptions);
$stmt_prescriptions->bind_param("i", $user_id);
$stmt_prescriptions->execute();
$result_prescriptions = $stmt_prescriptions->get_result();
$prescriptions = [];
while ($row = $result_prescriptions->fetch_assoc()) {
  $prescriptions[] = $row;
}
$stmt_prescriptions->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
  $full_name = $_POST['full_name'];
  $phone = $_POST['phone'];
  $city = $_POST['city'];
  $date_of_birth = $_POST['date_of_birth'];
  $gender = $_POST['gender'];
  $address = $_POST['address'];
  $experience = $_POST['experience'];
  $availability = $_POST['availability'];
  $busi_email = $_POST['busi_email'];

  $sql_update_user = "UPDATE users SET full_name = ?, phone = ?, city = ?, date_of_birth = ?, gender = ?, address = ? WHERE id = ?";
  $stmt_update_user = $conn->prepare($sql_update_user);
  $stmt_update_user->bind_param("ssssssi", $full_name, $phone, $city, $date_of_birth, $gender, $address, $user_id);

  $sql_update_doctor = "UPDATE doctors SET experience = ?, availability = ?, busi_email = ? WHERE user_id = ?";
  $stmt_update_doctor = $conn->prepare($sql_update_doctor);
  $stmt_update_doctor->bind_param("sssi", $experience, $availability, $busi_email, $user_id);

  if ($stmt_update_user->execute() && $stmt_update_doctor->execute()) {
    $_SESSION['message'] = 'Profile updated successfully!';
    // Refresh profile
    $stmt_profile = $conn->prepare($sql_profile);
    $stmt_profile->bind_param("i", $user_id);
    $stmt_profile->execute();
    $result_profile = $stmt_profile->get_result();
    $profile = $result_profile->fetch_assoc();
    $stmt_profile->close();
  } else {
    $_SESSION['error'] = 'Error updating profile: ' . $stmt_update_user->error;
  }
  $stmt_update_user->close();
  $stmt_update_doctor->close();
}



// Handle prescription addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_prescription'])) {
  $patient_id = $_POST['patient_id'];
  $medication = json_encode($_POST['medication']);
  $notes = $_POST['notes'];

  $sql_add_prescription = "INSERT INTO prescriptions (doctor_id, patient_id, medication, notes) VALUES ((SELECT id FROM doctors WHERE user_id = ?), ?, ?, ?)";
  $stmt_add_prescription = $conn->prepare($sql_add_prescription);
  $stmt_add_prescription->bind_param("iiss", $user_id, $patient_id, $medication, $notes);

  if ($stmt_add_prescription->execute()) {
    $_SESSION['message'] = 'Prescription added successfully!';
    // Refresh prescriptions
    $stmt_prescriptions = $conn->prepare($sql_prescriptions);
    $stmt_prescriptions->bind_param("i", $user_id);
    $stmt_prescriptions->execute();
    $result_prescriptions = $stmt_prescriptions->get_result();
    $prescriptions = [];
    while ($row = $result_prescriptions->fetch_assoc()) {
      $prescriptions[] = $row;
    }
    $stmt_prescriptions->close();
  } else {
    $_SESSION['error'] = 'Error adding prescription: ' . $stmt_add_prescription->error;
  }
  $stmt_add_prescription->close();
}

// Handle combined prescription + medical history submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_all'])) {
  $appointment_id = intval($_POST['appointment_id']);

  // Validate appointment_id
  if ($appointment_id <= 0) {
    $_SESSION['error'] = 'Invalid appointment selected.';
    header('Location: dashboard.php');
    exit;
  }

  // Fetch appointment securely
  $stmt = $conn->prepare("SELECT patient_id FROM appointments WHERE id = ? AND doctor_id = (SELECT id FROM doctors WHERE user_id = ?)");
  $stmt->bind_param("ii", $appointment_id, $user_id);
  $stmt->execute();
  $appointment = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!$appointment) {
    $_SESSION['error'] = 'Invalid appointment selected.';
    header('Location: dashboard.php');
    exit;
  }
  $patient_id = $appointment['patient_id'];

  // Sanitize inputs
  $notes = trim($_POST['notes'] ?? '');
  $conditions_input = trim($_POST['conditions'] ?? '');
  $allergies_input = trim($_POST['allergies'] ?? '');

  // Validate and process medications
  $medications = [];
  if (!empty($_POST['medication_name']) && is_array($_POST['medication_name'])) {
    foreach ($_POST['medication_name'] as $i => $name) {
      $name = trim($name);
      $dose = trim($_POST['medication_dose'][$i] ?? '');
      if (!empty($name) && !empty($dose)) {
        $medications[] = ['name' => htmlspecialchars($name), 'dose' => htmlspecialchars($dose)];
      }
    }
  }
  if (empty($medications)) {
    $_SESSION['error'] = 'At least one medication is required.';
    header('Location: dashboard.php');
    exit;
  }
  $med_json = json_encode($medications);

  // Process conditions and allergies
  $conditions = json_encode(array_map('htmlspecialchars', array_map('trim', explode(',', $conditions_input))));
  $allergies = json_encode(array_map('htmlspecialchars', array_map('trim', explode(',', $allergies_input))));

  // Handle file upload with validation
  $file_path = null;
  if (!empty($_FILES['prescription_file']['name'])) {
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $file_name = $_FILES['prescription_file']['name'];
    $file_tmp = $_FILES['prescription_file']['tmp_name'];
    $file_size = $_FILES['prescription_file']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
      $_SESSION['error'] = 'Invalid file type. Only PDF, JPG, PNG allowed.';
      header('Location: dashboard.php');
      exit;
    }
    if ($file_size > $max_size) {
      $_SESSION['error'] = 'File size too large. Max 5MB.';
      header('Location: dashboard.php');
      exit;
    }

    $dir = "../uploads/";
    if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
    }
    $safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file_name));
    $file_path = $dir . $safe_name;
    if (!move_uploaded_file($file_tmp, $file_path)) {
      $_SESSION['error'] = 'Failed to upload file.';
      header('Location: dashboard.php');
      exit;
    }
  }

  // Start transaction
  $conn->begin_transaction();

  try {
    // Insert prescription
    $stmt_pres = $conn->prepare("INSERT INTO prescriptions (doctor_id, patient_id, appointment_id, medication, notes, file_path, created_at) VALUES ((SELECT id FROM doctors WHERE user_id = ?), ?, ?, ?, ?, ?, NOW())");
    $stmt_pres->bind_param("iissss", $user_id, $patient_id, $appointment_id, $med_json, $notes, $file_path);
    $stmt_pres->execute();
    $stmt_pres->close();

    // Insert medical history
    $stmt_hist = $conn->prepare("INSERT INTO medical_history (patient_id, doctor_id, visit_date, conditions, allergies, notes, created_at) VALUES (?, (SELECT id FROM doctors WHERE user_id = ?), CURDATE(), ?, ?, ?, NOW())");
    $stmt_hist->bind_param("issss", $patient_id, $user_id, $conditions, $allergies, $notes);
    $stmt_hist->execute();
    $stmt_hist->close();

    // Update appointment status
    $stmt_upd = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
    $stmt_upd->bind_param("i", $appointment_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    $conn->commit();
    $_SESSION['message'] = 'Prescription and medical history saved successfully!';
  } catch (Exception $e) {
    $conn->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    $_SESSION['error'] = 'Failed to save data. Please try again.';
  }

  header('Location: dashboard.php');
  exit;
}

$page_title = 'Doctor Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';
?>
  <!-- Sidebar -->
  <div class="sidebar">
    <h3 class="text-center text-white pb-3">Doctor Panel</h3>
    <a href="#" class="active" onclick="showSection('dashboard')">Dashboard</a>
    <a href="#" onclick="showSection('profile')">Profile</a>
    <a href="#" onclick="showSection('appointments')">Appointments</a>
    <a href="#" onclick="showSection('patients')">Patients</a>

    <a href="#" onclick="showSection('add-prescription-history')">Add Prescription & History</a>
    <a href="../logout.php" class="text-warning">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['message']);
                                        unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']);
                                      unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <!-- Dashboard -->
    <div id="dashboard" class="section">
      <h2 class="mb-4">Dashboard Overview</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow-lg text-center p-4">
            <h3>Total Appointments</h3>
            <h1><?php echo count($appointments); ?></h1>
            <p>All Time</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-lg text-center p-4">
            <h3>Upcoming Appointments</h3>
            <h1><?php echo count(array_filter($appointments, function ($appt) {
                  return $appt['status'] == 'confirmed' && $appt['appointment_date'] >= date('Y-m-d');
                })); ?></h1>
            <p>Scheduled</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-lg text-center p-4">
            <h3>Patients</h3>
            <h1><?php echo count($patients); ?></h1>
            <p>Unique Patients</p>
          </div>
        </div>
      </div>
      <div class="row g-4 mt-3">
        <div class="col-md-6">
          <div class="card shadow-lg text-center p-4">
            <h3>Completed Appointments</h3>
            <h1><?php echo count(array_filter($appointments, function ($appt) {
                  return $appt['status'] == 'completed';
                })); ?></h1>
            <p>Finished</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-lg text-center p-4">
            <h3>Prescriptions Issued</h3>
            <h1><?php echo count($prescriptions); ?></h1>
            <p>Total</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile -->
    <div id="profile" class="section d-none">
      <h2 class="mb-4">My Profile</h2>
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['message']);
                                          unset($_SESSION['message']); ?></div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <div class="card shadow-lg p-4">
        <form method="POST">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="city" class="form-label">City</label>
              <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="date_of_birth" class="form-label">Date of Birth</label>
              <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($profile['date_of_birth'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="gender" class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender">
                <option value="male" <?php echo ($profile['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($profile['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                <option value="other" <?php echo ($profile['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
            <div class="col-12">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6">
              <label for="experience" class="form-label">Experience</label>
              <input type="text" class="form-control" id="experience" name="experience" value="<?php echo htmlspecialchars($profile['experience'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="availability" class="form-label">Availability</label>
              <input type="text" class="form-control" id="availability" name="availability" value="<?php echo htmlspecialchars($profile['availability'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="busi_email" class="form-label">Business Email</label>
              <input type="email" class="form-control" id="busi_email" name="busi_email" value="<?php echo htmlspecialchars($profile['busi_email'] ?? ''); ?>">
            </div>
            <div class="col-12">
              <button type="submit" name="update_profile" class="btn custom-btn">Update Profile</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Appointments -->
    <div id="appointments" class="section d-none">
      <h2 class="mb-4">My Appointments</h2>
      <?php if (!empty($appointments)) { ?>
        <table class="table shadow-sm">
          <thead>
            <tr>
              <th>Date</th>
              <th>Time</th>
              <th>Patient</th>
              <th>Specialty</th>
              <th>Status</th>
              <th>Notes</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($appointments as $appt) { ?>
              <tr>
                <td><?php echo htmlspecialchars($appt['appointment_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($appt['appointment_time'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($appt['patient_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($appt['specialty_name'] ?? ''); ?></td>
                <td class="status-<?php echo htmlspecialchars($appt['status'] ?? ''); ?>"><?php echo htmlspecialchars($appt['status'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($appt['notes'] ?? ''); ?></td>
              <td>
                  <form action="update_appointment_status.php" method="POST" style="display:inline;">
                    <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                    <select name="status" class="form-select form-select-sm" style="width:auto; display:inline;">
                      <option value="pending" <?php echo $appt['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="confirmed" <?php echo $appt['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                      <option value="completed" <?php echo $appt['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $appt['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-sm custom-btn">Update</button>
                  </form>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <p>No appointments.</p>
      <?php } ?>
    </div>

    <!-- Patients -->
    <div id="patients" class="section d-none">
      <h2 class="mb-4">My Patients</h2>
      <?php if (!empty($patients)) { ?>
        <table class="table shadow-sm">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($patients as $pat) { ?>
              <tr>
                <td><?php echo htmlspecialchars($pat['full_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($pat['email'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($pat['phone'] ?? ''); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <p>No patients.</p>
      <?php } ?>
    </div>



    <!-- Add Prescription & History -->
    <div id="add-prescription-history" class="section d-none">
      <h2 class="mb-4">Add Prescription & Medical History</h2>
      <div class="card shadow-lg p-4">
        <form method="POST" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="appointment_id" class="form-label">Select Appointment *</label>
              <select class="form-select" id="appointment_id" name="appointment_id" required>
                <option value="">Select an appointment</option>
                <?php
                $pending_appointments = array_filter($appointments, function ($appt) {
                  return in_array($appt['status'], ['pending', 'confirmed']);
                });
                foreach ($pending_appointments as $appt) {
                ?>
                  <option value="<?php echo $appt['id']; ?>">
                    <?php echo htmlspecialchars($appt['patient_name'] . ' - ' . $appt['appointment_date'] . ' ' . $appt['appointment_time']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Medications</label>
              <div id="medication-rows">
                <div class="row mb-2 medication-row">
                  <div class="col-md-5">
                    <input type="text" class="form-control" name="medication_name[]" placeholder="Medication Name" required>
                  </div>
                  <div class="col-md-5">
                    <input type="text" class="form-control" name="medication_dose[]" placeholder="Dose (e.g., 500mg daily)" required>
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-med">Remove</button>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-secondary mt-2" onclick="addMedicationRow()">Add Medication</button>
            </div>
            <div class="col-md-6">
              <label for="conditions" class="form-label">Conditions (comma-separated)</label>
              <input type="text" class="form-control" id="conditions" name="conditions" placeholder="e.g., Hypertension, Diabetes">
            </div>
            <div class="col-md-6">
              <label for="allergies" class="form-label">Allergies (comma-separated)</label>
              <input type="text" class="form-control" id="allergies" name="allergies" placeholder="e.g., Penicillin, Nuts">
            </div>
            <div class="col-12">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
            </div>
            <div class="col-12">
              <label for="prescription_file" class="form-label">Prescription File (optional)</label>
              <input type="file" class="form-control" id="prescription_file" name="prescription_file" accept=".pdf,.jpg,.png">
            </div>
            <div class="col-12">
              <button type="submit" name="save_all" class="btn custom-btn">Save Prescription & History</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>