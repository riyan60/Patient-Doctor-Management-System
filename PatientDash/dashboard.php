<?php
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../config/db_connect.php';

// Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$stmt = $conn->prepare("SELECT full_name, email, phone, city, date_of_birth, gender, address FROM users WHERE id = ?");
if (!$stmt) {
    error_log("Failed to prepare user profile query: " . $conn->error);
    $user = [];
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to execute user profile query: " . $stmt->error);
        $user = [];
    } else {
        $result = $stmt->get_result();
        if ($result) {
            $user = $result->fetch_assoc();
        } else {
            error_log("Failed to fetch user profile result: " . $stmt->error);
            $user = [];
        }
    }
    $stmt->close();
}

// Fetch counts
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?");
if (!$stmt) {
    error_log("Failed to prepare appointments count query: " . $conn->error);
    $appointments_count = 0;
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to execute appointments count query: " . $stmt->error);
        $appointments_count = 0;
    } else {
        $result = $stmt->get_result();
        if ($result) {
            $appointments_count = $result->fetch_assoc()['count'];
        } else {
            error_log("Failed to fetch appointments count result: " . $stmt->error);
            $appointments_count = 0;
        }
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM medical_history WHERE patient_id = ?");
if (!$stmt) {
    error_log("Failed to prepare medical history count query: " . $conn->error);
    $history_count = 0;
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to execute medical history count query: " . $stmt->error);
        $history_count = 0;
    } else {
        $result = $stmt->get_result();
        if ($result) {
            $history_count = $result->fetch_assoc()['count'];
        } else {
            error_log("Failed to fetch medical history count result: " . $stmt->error);
            $history_count = 0;
        }
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM prescriptions WHERE patient_id = ?");
if (!$stmt) {
    error_log("Failed to prepare prescriptions count query: " . $conn->error);
    $prescriptions_count = 0;
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to execute prescriptions count query: " . $stmt->error);
        $prescriptions_count = 0;
    } else {
        $result = $stmt->get_result();
        if ($result) {
            $prescriptions_count = $result->fetch_assoc()['count'];
        } else {
            error_log("Failed to fetch prescriptions count result: " . $stmt->error);
            $prescriptions_count = 0;
        }
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
if (!$stmt) {
    error_log("Failed to prepare cart count query: " . $conn->error);
    $cart_count = 0;
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to execute cart count query: " . $stmt->error);
        $cart_count = 0;
    } else {
        $result = $stmt->get_result();
        if ($result) {
            $cart_count = $result->fetch_assoc()['count'] ?? 0;
        } else {
            error_log("Failed to fetch cart count result: " . $stmt->error);
            $cart_count = 0;
        }
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE user_id = ?");
if (!$stmt) {
    error_log("Failed to prepare feedback count query: " . $conn->error);
    $feedback_count = 0;
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to execute feedback count query: " . $stmt->error);
        $feedback_count = 0;
    } else {
        $result = $stmt->get_result();
        if ($result) {
            $feedback_count = $result->fetch_assoc()['count'];
        } else {
            error_log("Failed to fetch feedback count result: " . $stmt->error);
            $feedback_count = 0;
        }
    }
    $stmt->close();
}

// Fetch appointments
$stmt = $conn->prepare("SELECT a.appointment_date, a.appointment_time, a.status, a.notes, u.full_name as doctor_name, s.name as specialty_name FROM appointments a JOIN doctors d ON a.doctor_id = d.id JOIN users u ON d.user_id = u.id LEFT JOIN specialties s ON a.specialty_id = s.id WHERE a.patient_id = ? ORDER BY FIELD(a.status, 'pending', 'confirmed', 'cancelled', 'completed'), a.appointment_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch upcoming appointments
$stmt = $conn->prepare("SELECT a.appointment_date, a.appointment_time, a.status, a.notes, u.full_name as doctor_name, s.name as specialty_name FROM appointments a JOIN doctors d ON a.doctor_id = d.id JOIN users u ON d.user_id = u.id LEFT JOIN specialties s ON a.specialty_id = s.id WHERE a.patient_id = ? AND a.status IN ('pending', 'confirmed') AND a.appointment_date >= CURDATE() ORDER BY a.appointment_date ASC, a.appointment_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcoming_appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch medical history
$stmt = $conn->prepare("SELECT visit_date, conditions, allergies, notes, u.full_name as doctor_name FROM medical_history h JOIN doctors d ON h.doctor_id = d.id JOIN users u ON d.user_id = u.id WHERE h.patient_id = ? ORDER BY visit_date DESC");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $history = [];
    error_log("Failed to fetch medical history: " . $stmt->error);
}
$stmt->close();

// Fetch prescriptions
$stmt = $conn->prepare("SELECT p.created_at, p.medication, p.notes, p.file_path, u.full_name as doctor_name FROM prescriptions p JOIN doctors d ON p.doctor_id = d.id JOIN users u ON d.user_id = u.id WHERE p.patient_id = ? ORDER BY p.created_at DESC");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $prescriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $prescriptions = [];
    error_log("Failed to fetch prescriptions: " . $stmt->error);
}
$stmt->close();

// Fetch cart items
$stmt = $conn->prepare("SELECT ci.item_id, ci.type, ci.quantity, ci.added_at, CASE WHEN ci.type = 'package' THEN p.name ELSE t.name END as name, CASE WHEN ci.type = 'package' THEN p.price ELSE t.price END as price, CASE WHEN ci.type = 'package' THEN p.discounted_price ELSE t.discounted_price END as discounted_price FROM cart_items ci LEFT JOIN packages p ON ci.item_id = p.id AND ci.type = 'package' LEFT JOIN tests t ON ci.item_id = t.id AND ci.type = 'test' WHERE ci.user_id = ? ORDER BY ci.added_at DESC");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $cart_items = [];
    error_log("Failed to fetch cart items: " . $stmt->error);
}
$stmt->close();

// Fetch feedback
$stmt = $conn->prepare("SELECT rating, comment, created_at FROM feedback WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $feedback_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $feedback_list = [];
    error_log("Failed to fetch feedback: " . $stmt->error);
}
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, city = ?, date_of_birth = ?, gender = ?, address = ? WHERE id = ?");
    if (!$stmt) {
        error_log("Failed to prepare profile update query: " . $conn->error);
        $_SESSION['error'] = 'Database error. Please try again.';
        header('Location: dashboard.php?section=profile');
        exit;
    }
    $stmt->bind_param("ssssssi", $full_name, $phone, $city, $date_of_birth, $gender, $address, $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Profile updated successfully!';
        header('Location: dashboard.php?section=profile');
        exit;
    } else {
        error_log("Failed to execute profile update: " . $stmt->error);
        $_SESSION['error'] = 'Failed to update profile.';
        header('Location: dashboard.php?section=profile');
        exit;
    }
    $stmt->close();
}

// Handle feedback submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
        if (!$stmt) {
            error_log("Failed to prepare feedback insert query: " . $conn->error);
            $_SESSION['error'] = 'Database error. Please try again.';
            header('Location: dashboard.php?section=feedback');
            exit;
        }
        $stmt->bind_param("iis", $user_id, $rating, $comment);
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Feedback submitted successfully!';
            header('Location: dashboard.php?section=feedback');
            exit;
        } else {
            error_log("Failed to execute feedback insert: " . $stmt->error);
            $_SESSION['error'] = 'Failed to submit feedback.';
            header('Location: dashboard.php?section=feedback');
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Invalid rating or comment.';
    }
}

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    error_log("Appointment booking POST received: " . print_r($_POST, true));

    $doctor_id = (int)$_POST['doctor_id'];
    $specialty_id = (int)$_POST['specialty_id'];
    $appointment_date = $_POST['date'];
    $appointment_time = $_POST['time'];
    $appointment_type = $_POST['appointment_type'];
    $notes = trim($_POST['notes']);

    error_log("Parsed data - doctor_id: $doctor_id, specialty_id: $specialty_id, date: $appointment_date, time: $appointment_time, type: $appointment_type, user_id: $user_id");

    // Check database connection
    if ($conn->connect_error) {
        error_log("Database connection error: " . $conn->connect_error);
        $_SESSION['error'] = "Database connection failed.";
        header('Location: dashboard.php?section=book-appointment');
        exit;
    }

    // Check for conflicting appointment
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
    if (!$stmt) {
        error_log("Prepare failed for conflict check: " . $conn->error);
        $_SESSION['error'] = "Database error during conflict check.";
        header('Location: dashboard.php?section=book-appointment');
        exit;
    }
    $stmt->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    if (!$stmt->execute()) {
        error_log("Execute failed for conflict check: " . $stmt->error);
        $_SESSION['error'] = "Database error during conflict check.";
        $stmt->close();
        header('Location: dashboard.php?section=book-appointment');
        exit;
    }
    $conflict_result = $stmt->get_result();
    if ($conflict_result->num_rows > 0) {
        error_log("Conflict detected for doctor $doctor_id on $appointment_date at $appointment_time");
        $_SESSION['error'] = "This time slot is already booked. Please choose a different time.";
        $stmt->close();
        header('Location: dashboard.php?section=book-appointment');
        exit;
    }
    $stmt->close();

    // Combine notes with appointment type
    if (!empty($appointment_type)) {
        $notes = "Type: $appointment_type\n" . $notes;
    }

    error_log("Final notes: $notes");

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, specialty_id, appointment_date, appointment_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    if (!$stmt) {
        error_log("Prepare failed for insert: " . $conn->error);
        $_SESSION['error'] = "Database error during appointment booking.";
        header('Location: dashboard.php?section=book-appointment');
        exit;
    }
    $stmt->bind_param("iiisss", $user_id, $doctor_id, $specialty_id, $appointment_date, $appointment_time, $notes);
    if ($stmt->execute()) {
        $appointment_id = $conn->insert_id;
        error_log("Appointment inserted successfully with ID: $appointment_id");

        // Get doctor name for confirmation
        $stmt = $conn->prepare("SELECT u.full_name FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.id = ?");
        if (!$stmt) {
            error_log("Prepare failed for doctor name: " . $conn->error);
            $_SESSION['error'] = "Database error retrieving doctor info.";
            header('Location: dashboard.php?section=book-appointment');
            exit;
        }
        $stmt->bind_param("i", $doctor_id);
        if (!$stmt->execute()) {
            error_log("Execute failed for doctor name: " . $stmt->error);
            $_SESSION['error'] = "Database error retrieving doctor info.";
            $stmt->close();
            header('Location: dashboard.php?section=book-appointment');
            exit;
        }
        $doctor_result = $stmt->get_result();
        $doctor_name = $doctor_result->fetch_assoc()['full_name'];
        $stmt->close();

        // Store in session for confirmation page
        $_SESSION['appointment_confirmation'] = [
            'id' => $appointment_id,
            'name' => $user['full_name'],
            'doctor' => $doctor_name,
            'date' => $appointment_date,
            'time' => $appointment_time,
            'type' => $appointment_type,
            'email' => $user['email']
        ];

        header('Location: appointment-confirmation.php');
        exit;
    } else {
        $error_msg = "Failed to book appointment: " . $stmt->error;
        error_log($error_msg);
        $_SESSION['error'] = $error_msg;
        $stmt->close();
        header('Location: dashboard.php?section=book-appointment');
        exit;
    }
}

// Fetch doctors for booking
$stmt = $conn->prepare("SELECT d.id, u.full_name as name, s.name as specialty, d.city, d.experience, d.availability, d.image, s.id as specialty_id FROM doctors d JOIN users u ON d.user_id = u.id JOIN specialties s ON d.specialty_id = s.id WHERE d.is_active = 1");
if ($stmt->execute()) {
    $doctors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $doctors = [];
    error_log("Failed to fetch doctors: " . $stmt->error);
}
$stmt->close();

$conn->close();
?>

<?php
$page_title = 'Patient Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';
?>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h3 class="text-center text-white pb-3">Patient Panel</h3>
    <a href="../index.php">Home</a>
    <a href="#" class="active" onclick="showSection('dashboard', this)">Dashboard</a>
    <a href="#" onclick="showSection('profile', this)">Profile</a>
    <a href="#" onclick="showSection('appointments', this)">Appointments</a>
    <a href="#" onclick="showSection('book-appointment', this)">Book Appointment</a>
    <a href="#" onclick="showSection('history', this)">Medical History</a>
    <a href="#" onclick="showSection('prescriptions', this)">Prescriptions</a>
    <a href="#" onclick="showSection('cart', this)">Cart</a>
    <a href="#" onclick="showSection('feedback', this)">Feedback</a>
    <a href="../logout.php" class="text-warning">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <!-- Dashboard -->
    <div id="dashboard" class="section">
      <h2 class="mb-4">Dashboard Overview</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <a href="#" onclick="showSection('appointments')" class="text-decoration-none">
            <div class="card shadow-lg text-center p-4">
              <h3>Total Appointments</h3>
              <h1 id="countAppointments">0</h1>
              <p>All Visits</p>
            </div>
          </a>
        </div>
        <div class="col-md-4">
          <a href="#" onclick="showSection('history')" class="text-decoration-none">
            <div class="card shadow-lg text-center p-4">
              <h3>Medical Records</h3>
              <h1 id="countHistory">0</h1>
              <p>History Entries</p>
            </div>
          </a>
        </div>
        <div class="col-md-4">
          <a href="#" onclick="showSection('prescriptions')" class="text-decoration-none">
            <div class="card shadow-lg text-center p-4">
              <h3>Prescriptions</h3>
              <h1 id="countPrescriptions">0</h1>
              <p>Active Prescriptions</p>
            </div>
          </a>
        </div>
      </div>
      <div class="row g-4 mt-3">
        <div class="col-md-6">
          <a href="#" onclick="showSection('cart')" class="text-decoration-none">
            <div class="card shadow-lg text-center p-4">
              <h3>Cart Items</h3>
              <h1 id="countCart">0</h1>
              <p>Items in Cart</p>
            </div>
          </a>
        </div>
        <div class="col-md-6">
          <a href="#" onclick="showSection('feedback')" class="text-decoration-none">
            <div class="card shadow-lg text-center p-4">
              <h3>Feedback Given</h3>
              <h1 id="countFeedback">0</h1>
              <p>Reviews Submitted</p>
            </div>
          </a>
        </div>
      </div>
    </div>

    <!-- Profile -->
    <div id="profile" class="section d-none">
      <h2 class="mb-4">My Profile</h2>
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <div class="card shadow-lg p-4">
        <form id="profileForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" name="full_name" value="" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" readonly>
            </div>
            <div class="col-md-6">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="phone" name="phone" value="">
            </div>
            <div class="col-md-6">
              <label for="city" class="form-label">City</label>
              <input type="text" class="form-control" id="city" name="city" value="">
            </div>
            <div class="col-md-6">
              <label for="date_of_birth" class="form-label">Date of Birth</label>
              <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="">
            </div>
            <div class="col-md-6">
              <label for="gender" class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-12">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="3"></textarea>
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
      <div id="appointmentsContainer"></div>
    </div>

    <!-- Book Appointment -->
    <div id="book-appointment" class="section d-none">
      <h2 class="mb-4">Book Appointment</h2>

      <!-- Selected Doctor Info -->
      <div id="selectedDoctorInfo" class="selected-doctor-info hidden" style="background: #e8f5e8; border: 2px solid #00796b; border-radius: 0.3em; padding: 20px; margin-bottom: 30px; text-align: center;">
        <h4><span style="margin-right:8px;">✔</span>Selected Doctor</h4>
        <p id="selectedDoctorText"></p>
      </div>

      <!-- Booking Form -->
      <div class="booking-form-section" id="bookingFormSection" style="background: white; border-radius: 0.3em; padding: 40px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
        <h2>Appointment Details</h2>
        <div id="alertContainer"></div>

        <form id="appointmentForm" action="submit_appointment.php" method="post">
          <input type="hidden" name="doctor_id" id="selectedDoctor" required>
          <input type="hidden" name="specialty_id" id="selectedSpecialty" required>

          <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
              <label class="form-label" for="doctorSelect">Select Doctor *</label>
              <select class="form-select" id="doctorSelect" required>
                <option value="" disabled selected>Choose a doctor</option>
                <!-- Options will be populated by JS -->
              </select>
            </div>



            <div class="form-group">
              <label class="form-label" for="appointmentDate">Preferred Date *</label>
              <input type="date" class="form-control" id="appointmentDate" name="date" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="appointmentTime">Preferred Time *</label>
              <select class="form-select" id="appointmentTime" name="time" required>
                <option value="" disabled selected>Choose time slot</option>
                <option value="09:00">9:00 AM</option>
                <option value="09:30">9:30 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="10:30">10:30 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="11:30">11:30 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="12:30">12:30 PM</option>
                <option value="14:00">2:00 PM</option>
                <option value="14:30">2:30 PM</option>
                <option value="15:00">3:00 PM</option>
                <option value="15:30">3:30 PM</option>
                <option value="16:00">4:00 PM</option>
                <option value="16:30">4:30 PM</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="appointmentType">Appointment Type</label>
              <select class="form-select" id="appointmentType" name="appointment_type">
                <option value="consultation">General Consultation</option>
                <option value="follow-up">Follow-up Visit</option>
                <option value="check-up">Routine Check-up</option>
                <option value="emergency">Emergency</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="patientNotes">Additional Notes (Optional)</label>
            <textarea class="form-control form-textarea" id="patientNotes" name="notes" placeholder="Describe symptoms or concerns..." style="resize: vertical; min-height: 100px;"></textarea>
          </div>

          <button type="submit" name="book_appointment" class="btn custom-btn submit-btn d-block mx-auto mt-5 w-25" id="submitBtn">
            <span id="btnText">Submit</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Medical History -->
    <div id="history" class="section d-none">
      <h2 class="mb-4">Medical History</h2>
      <div id="historyContainer"></div>
    </div>

    <!-- Prescriptions -->
    <div id="prescriptions" class="section d-none">
      <h2 class="mb-4">My Prescriptions</h2>
      <div id="prescriptionsContainer"></div>
    </div>

    <!-- Cart -->
    <div id="cart" class="section d-none">
      <h2 class="mb-4">My Cart</h2>
      <div id="cartContainer"></div>
    </div>

    <!-- Feedback -->
    <div id="feedback" class="section d-none">
      <h2 class="mb-4">My Feedback</h2>
      <div class="card shadow-lg p-4 mb-4">
        <h3>Submit New Feedback</h3>
        <form id="feedbackForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="rating" class="form-label">Rating (1-5)</label>
              <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
            </div>
            <div class="col-12">
              <label for="comment" class="form-label">Comment</label>
              <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn custom-btn">Submit Feedback</button>
            </div>
          </div>
        </form>
      </div>

      <div id="feedbackListContainer"></div>
    </div>
  </div>

  <!-- Keep your existing JS links -->
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/bootstrap.bundle.js"></script>
  <script src="../assets/js/jquery-3.7.1.min.js"></script>
  <script src="../assets/js/main.js"></script>

  <script>
    /***********************
     * PHP DATA *
     ***********************/
    const userProfile = <?php echo json_encode($user); ?>;
    const appointmentsCount = <?php echo $appointments_count; ?>;
    const historyCount = <?php echo $history_count; ?>;
    const prescriptionsCount = <?php echo $prescriptions_count; ?>;
    const cartCount = <?php echo $cart_count; ?>;
    const feedbackCount = <?php echo $feedback_count; ?>;

    const appointmentsData = <?php echo json_encode($appointments); ?>;

    const upcomingAppointmentsData = <?php echo json_encode($upcoming_appointments); ?>;

    const historyData = <?php echo json_encode($history); ?>;

    const prescriptionsData = <?php echo json_encode($prescriptions); ?>;

    const cartData = <?php echo json_encode($cart_items); ?>;

    const feedbackData = <?php echo json_encode($feedback_list); ?>;

    const doctorsData = <?php echo json_encode($doctors); ?>;

    /************************
     * UTIL & RENDER METHODS
     ************************/
    function showSection(sectionId, el) {
      // Hide all sections
      document.querySelectorAll(".section").forEach(sec => sec.classList.add("d-none"));
      // Show selected
      const target = document.getElementById(sectionId);
      if (target) target.classList.remove("d-none");

      // Update active link
      document.querySelectorAll(".sidebar a").forEach(link => link.classList.remove("active"));
      if (el) el.classList.add("active");
    }

    function formatPhone(input) {
      let value = input.value.replace(/\D/g, '');
      if (value.length > 10) value = value.slice(0, 10);
      if (value.length >= 6) {
        value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6);
      } else if (value.length >= 3) {
        value = value.slice(0, 3) + '-' + value.slice(3);
      }
      input.value = value;
    }

    function showAlert(message, type = 'success') {
      const alertContainer = document.getElementById('alertContainer');
      const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
      alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
      setTimeout(() => { alertContainer.innerHTML = ''; }, 5000);
    }

    /**********************
     * RENDER DASHBOARD DATA
     **********************/
    function renderCounts() {
      document.getElementById('countAppointments').textContent = appointmentsCount;
      document.getElementById('countHistory').textContent = historyCount;
      document.getElementById('countPrescriptions').textContent = prescriptionsCount;
      document.getElementById('countCart').textContent = cartCount;
      document.getElementById('countFeedback').textContent = feedbackCount;
    }

    function renderProfile() {
      document.getElementById('full_name').value = userProfile.full_name || '';
      document.getElementById('email').value = userProfile.email || '';
      document.getElementById('phone').value = userProfile.phone || '';
      document.getElementById('city').value = userProfile.city || '';
      document.getElementById('date_of_birth').value = userProfile.date_of_birth || '';
      document.getElementById('gender').value = userProfile.gender || '';
      document.getElementById('address').value = userProfile.address || '';
    }

    function renderAppointments() {
      const container = document.getElementById('appointmentsContainer');
      if (!upcomingAppointmentsData.length) {
        container.innerHTML = '<p>No upcoming appointments.</p>';
        return;
      }
      // Build table like original
      let html = `<table class="table shadow-sm"><thead>
        <tr><th>Date</th><th>Time</th><th>Doctor</th><th>Specialty</th><th>Status</th><th>Notes</th></tr>
      </thead><tbody>`;
      for (const appt of upcomingAppointmentsData) {
        html += `<tr>
          <td>${escapeHtml(appt.appointment_date)}</td>
          <td>${escapeHtml(appt.appointment_time)}</td>
          <td>${escapeHtml(appt.doctor_name)}</td>
          <td>${escapeHtml(appt.specialty_name || 'N/A')}</td>
          <td>${escapeHtml(appt.status)}</td>
          <td>${escapeHtml(appt.notes)}</td>
        </tr>`;
      }
      html += `</tbody></table>`;
      container.innerHTML = html;
    }

    function renderHistory() {
      const container = document.getElementById('historyContainer');
      if (!historyData.length) { container.innerHTML = '<p>No medical history records.</p>'; return; }
      let html = `<table class="table shadow-sm"><thead>
        <tr><th>Visit Date</th><th>Doctor</th><th>Conditions</th><th>Allergies</th><th>Notes</th></tr>
      </thead><tbody>`;
      for (const h of historyData) {
        const conditions = JSON.parse(h.conditions || '[]').join(', ');
        const allergies = JSON.parse(h.allergies || '[]').join(', ');
        html += `<tr>
          <td>${escapeHtml(h.visit_date)}</td>
          <td>${escapeHtml(h.doctor_name)}</td>
          <td>${escapeHtml(conditions)}</td>
          <td>${escapeHtml(allergies)}</td>
          <td>${escapeHtml(h.notes)}</td>
        </tr>`;
      }
      html += `</tbody></table>`;
      container.innerHTML = html;
    }

    function renderPrescriptions() {
      const container = document.getElementById('prescriptionsContainer');
      if (!prescriptionsData.length) { container.innerHTML = '<p>No prescriptions available.</p>'; return; }
      let html = `<table class="table shadow-sm"><thead>
        <tr><th>Date</th><th>Doctor</th><th>Medication</th><th>Notes</th><th>File</th></tr>
      </thead><tbody>`;
      for (const p of prescriptionsData) {
        let medsDisplay = '';
        try {
          const meds = JSON.parse(p.medication || '[]');
          if (Array.isArray(meds)) {
            if (meds.length > 0 && meds[0].hasOwnProperty('name')) {
              // New format: array of objects
              medsDisplay = meds.map(m => `${m.name} - ${m.dose}`).join(', ');
            } else {
              // Old format: simple array
              medsDisplay = meds.join(', ');
            }
          }
        } catch (e) {
          medsDisplay = p.medication || '';
        }
        html += `<tr>
          <td>${escapeHtml(p.created_at)}</td>
          <td>${escapeHtml(p.doctor_name)}</td>
          <td>${escapeHtml(medsDisplay)}</td>
          <td>${escapeHtml(p.notes)}</td>
          <td>${p.file_path ? `<a href="${escapeAttr(p.file_path)}" target="_blank">Download</a>` : 'N/A'}</td>
        </tr>`;
      }
      html += `</tbody></table>`;
      container.innerHTML = html;
    }

    function renderCart() {
      const container = document.getElementById('cartContainer');
      if (!cartData.length) { container.innerHTML = '<p>Your cart is empty.</p>'; return; }
      let html = `<table class="table shadow-sm"><thead>
        <tr><th>Item</th><th>Price</th><th>Discounted Price</th><th>Quantity</th><th>Added At</th></tr>
      </thead><tbody>`;
      for (const c of cartData) {
        html += `<tr>
          <td>${escapeHtml(c.name)}</td>
          <td>${Number(c.price).toFixed(2)}</td>
          <td>${Number(c.discounted_price ?? c.price).toFixed(2)}</td>
          <td>${escapeHtml(String(c.quantity))}</td>
          <td>${escapeHtml(c.added_at)}</td>
        </tr>`;
      }
      html += `</tbody></table>`;
      container.innerHTML = html;
    }

    function renderFeedbackList() {
      const container = document.getElementById('feedbackListContainer');
      if (!feedbackData.length) { container.innerHTML = '<p>No feedback yet.</p>'; return; }
      let html = `<h3>Previous Feedback</h3><table class="table shadow-sm"><thead>
        <tr><th>Date</th><th>Rating</th><th>Comment</th></tr>
      </thead><tbody>`;
      for (const f of feedbackData) {
        html += `<tr>
          <td>${escapeHtml(f.created_at)}</td>
          <td>${escapeHtml(String(f.rating))} ⭐</td>
          <td>${escapeHtml(f.comment)}</td>
        </tr>`;
      }
      html += `</tbody></table>`;
      container.innerHTML = html;
    }

    function populateDoctorSelect() {
      const select = document.getElementById('doctorSelect');
      select.innerHTML = `<option value="" disabled selected>Choose a doctor</option>`;
      for (const doc of doctorsData) {
        const opt = document.createElement('option');
        opt.value = doc.id;
        opt.setAttribute('data-name', doc.name);
        opt.setAttribute('data-specialty', doc.specialty);
        opt.setAttribute('data-specialty-id', doc.specialty_id);
        opt.textContent = `${doc.name} - ${doc.specialty}`;
        select.appendChild(opt);
      }
    }

    function escapeHtml(str) {
      if (str === null || str === undefined) return '';
      return String(str).replace(/[&<>"']/g, function (m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
      });
    }

    function escapeAttr(str) {
      return encodeURI(String(str));
    }

    /************************
     * FORM HANDLERS (SIM)
     ************************/
    let selectedDoctorData = null;
    function selectDoctorFromOption(option) {
      if (!option || !option.value) return;
      const name = option.getAttribute('data-name');
      const specialty = option.getAttribute('data-specialty');
      const specialtyId = option.getAttribute('data-specialty-id');
      const id = option.value;
      selectedDoctorData = { name, specialty, id, specialty_id: specialtyId };
      document.getElementById('selectedDoctor').value = id;
      document.getElementById('selectedSpecialty').value = specialtyId;
      document.getElementById('selectedDoctorText').textContent = `${name} - ${specialty}`;
      document.getElementById('selectedDoctorInfo').classList.remove('hidden');
    }

    // appointment form submit - now connects to backend
    document.addEventListener('DOMContentLoaded', function () {
      // doctor select change
      document.getElementById('doctorSelect').addEventListener('change', function () {
        selectDoctorFromOption(this.options[this.selectedIndex]);
      });

      // appointment form submit - validate and submit
      document.getElementById('appointmentForm').addEventListener('submit', function (e) {
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('btnText');

        // Validate required fields
        const requiredFields = ['appointmentDate', 'appointmentTime'];
        let isValid = true;
        requiredFields.forEach(fieldId => {
          const field = document.getElementById(fieldId);
          if (!field || !field.value.trim()) {
            if (field) field.style.borderColor = '#dc3545';
            isValid = false;
          } else {
            if (field) field.style.borderColor = '';
          }
        });

        if (!selectedDoctorData) {
          showAlert('Please select a doctor first', 'error');
          e.preventDefault();
          return false;
        }

        if (!isValid) {
          showAlert('Please fill in all required fields', 'error');
          e.preventDefault();
          return false;
        }

        // Additional validation: ensure date and time are in the future
        const selectedDate = new Date(document.getElementById('appointmentDate').value);
        const selectedTime = document.getElementById('appointmentTime').value;
        const now = new Date();
        const selectedDateTime = new Date(selectedDate.toDateString() + ' ' + selectedTime);
        if (selectedDateTime <= now) {
          showAlert('Please select a future date and time slot.', 'error');
          e.preventDefault();
          return false;
        }

        submitBtn.disabled = true;
        submitText.textContent = 'Booking...';
        // Form will submit POST to backend
      });

      // profile update (simulated)
      document.getElementById('profileForm').addEventListener('submit', function (e) {
        e.preventDefault();
        // Update sampleProfile
        sampleProfile.full_name = document.getElementById('full_name').value;
        sampleProfile.phone = document.getElementById('phone').value;
        sampleProfile.city = document.getElementById('city').value;
        sampleProfile.date_of_birth = document.getElementById('date_of_birth').value;
        sampleProfile.gender = document.getElementById('gender').value;
        sampleProfile.address = document.getElementById('address').value;
        showAlert('Profile updated successfully!', 'success');
      });

      // feedback submit (simulated)
      document.getElementById('feedbackForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const rating = Number(document.getElementById('rating').value);
        const comment = document.getElementById('comment').value.trim();
        if (!rating || !comment) { alert('Provide rating and comment'); return; }
        const newFb = { id: Date.now(), rating, comment, created_at: new Date().toISOString().split('T')[0] };
        sampleFeedback.unshift(newFb); // add to top
        renderFeedbackList();
        renderCounts();
        this.reset();
      });

      // set min date for appointment date input to today
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('appointmentDate').setAttribute('min', today);

      // initial render
      populateDoctorSelect();
      renderProfile();
      renderCounts();
      renderAppointments();
      renderHistory();
      renderPrescriptions();
      renderCart();
      renderFeedbackList();
    });

    // small helper to safely attach click to sidebar links (if user navigates via keyboard etc)
    document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', function (e) {
        // Prevent anchor default scroll behavior
        if (this.getAttribute('onclick')) e.preventDefault();
      });
    });
  </script>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
