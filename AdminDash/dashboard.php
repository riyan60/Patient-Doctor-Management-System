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

$page_title = 'Admin Dashboard';
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

// Fetch tests
$sql_tests = "SELECT t.id, t.name, l.name as lab_name, t.price, t.discounted_price, t.icon, t.description, t.duration, t.is_available FROM tests t LEFT JOIN labs l ON t.lab_id = l.id ORDER BY t.name";
$result_tests = $conn->query($sql_tests);
$tests = [];
$tests_count = 0;
if ($result_tests->num_rows > 0) {
    $tests_count = $result_tests->num_rows;
    while($row = $result_tests->fetch_assoc()) {
        $test_key = $row['id'];
        $tests[$test_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "lab_name" => $row['lab_name'] ?: "N/A",
            "price" => (float)$row['price'],
            "discounted_price" => $row['discounted_price'] ? (float)$row['discounted_price'] : (float)$row['price'],
            "icon" => $row['icon'] ?: "&#129298;",
            "desc" => $row['description'] ?: "Medical test for health assessment.",
            "duration" => $row['duration'] ?: "N/A"
        ];
    }
}

// Fetch packages
$sql_packages = "SELECT id, name, price, discounted_price, description, duration, features, image, is_active FROM packages ORDER BY name";
$result_packages = $conn->query($sql_packages);
$packages = [];
if ($result_packages->num_rows > 0) {
    while($row = $result_packages->fetch_assoc()) {
        $package_key = $row['id'];
        $packages[$package_key] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => (float)$row['price'],
            "discounted_price" => $row['discounted_price'] ? (float)$row['discounted_price'] : (float)$row['price'],
            "desc" => $row['description'] ?: "",
            "duration" => $row['duration'] ?: "",
            "features" => $row['features'] ? json_decode($row['features'], true) : [],
            "image" => $row['image'] ?: "",
            "is_active" => (int)$row['is_active']
        ];
    }
}

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

// Fetch doctors count
$sql_doctors = "SELECT COUNT(*) as count FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.is_active = 1 AND d.is_active = 1";
$result_doctors = $conn->query($sql_doctors);
$doctor_count = $result_doctors->fetch_assoc()['count'];

// Fetch patients count
$sql_patients = "SELECT COUNT(*) as count FROM users WHERE role = 'patient' AND is_active = 1";
$result_patients = $conn->query($sql_patients);
$patient_count = $result_patients->fetch_assoc()['count'];

// Fetch doctors list for admin
$sql_admin_doctors = "SELECT d.id, u.full_name, s.name as specialty_name, d.availability, d.city, d.rating, d.phone, d.busi_email, d.experience, GROUP_CONCAT(sv.name SEPARATOR ', ') as services FROM doctors d JOIN users u ON d.user_id = u.id JOIN specialties s ON d.specialty_id = s.id LEFT JOIN doctor_services ds ON d.id = ds.doctor_id LEFT JOIN services sv ON ds.service_id = sv.id WHERE u.is_active = 1 AND d.is_active = 1 GROUP BY d.id ORDER BY u.full_name";
$result_admin_doctors = $conn->query($sql_admin_doctors);
$admin_doctors = [];
if ($result_admin_doctors->num_rows > 0) {
    while($row = $result_admin_doctors->fetch_assoc()) {
        $admin_doctors[] = $row;
    }
}

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

// Fetch pending doctors count
$sql_pending_doctors_count = "SELECT COUNT(*) as count FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.role='doctor' AND u.is_active=0";
$result_pending_doctors_count = $conn->query($sql_pending_doctors_count);
$pending_doctors_count = $result_pending_doctors_count->fetch_assoc()['count'];

// Fetch pending doctors list
$sql_pending_doctors = "SELECT d.id, u.full_name, s.name as specialty_name, d.availability, d.city, d.phone, d.busi_email, d.experience FROM doctors d JOIN users u ON d.user_id = u.id JOIN specialties s ON d.specialty_id = s.id WHERE u.role='doctor' AND u.is_active=0";
$result_pending_doctors = $conn->query($sql_pending_doctors);
$pending_doctors = [];
if ($result_pending_doctors->num_rows > 0) {
    while($row = $result_pending_doctors->fetch_assoc()) {
        $pending_doctors[] = $row;
    }
}

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

// Total appointments for dashboard
$sql_total_appointments = "SELECT COUNT(*) as count FROM appointments";
$result_total = $conn->query($sql_total_appointments);
$appointments_count = $result_total->fetch_assoc()['count'];

// Fetch blogs
$sql_blogs = "SELECT id, title, content as description, image FROM blogs ORDER BY id DESC";
$result_blogs = $conn->query($sql_blogs);
$blogs = [];
if ($result_blogs->num_rows > 0) {
    while($row = $result_blogs->fetch_assoc()) {
        $blogs[] = $row;
    }
}



// Handle package insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_package'])) {
    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $discounted_price = !empty($_POST['discounted_price']) ? (float)$_POST['discounted_price'] : null;
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $features = json_encode(array_filter(explode("\n", $_POST['features'])));
    $image = $_POST['image'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "INSERT INTO packages (name, price, discounted_price, description, duration, features, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddssssd", $name, $price, $discounted_price, $description, $duration, $features, $image, $is_active);

    if ($stmt->execute()) {
        echo "<script>alert('Package added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding package: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle package deletion
if (isset($_GET['delete_package'])) {
    $id = (int)$_GET['delete_package'];
    $sql = "DELETE FROM packages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Package deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting package: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle package status toggle
if (isset($_GET['toggle_package'])) {
    $id = (int)$_GET['toggle_package'];
    $sql = "UPDATE packages SET is_active = 1 - is_active WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Package status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating package status: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle test deletion
if (isset($_GET['delete_test'])) {
    $id = (int)$_GET['delete_test'];
    $sql = "DELETE FROM tests WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Test deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting test: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle test status toggle
if (isset($_GET['toggle_test'])) {
    $id = (int)$_GET['toggle_test'];
    $sql = "UPDATE tests SET is_available = 1 - is_available WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Test status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating test status: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle test insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_test'])) {
    $name = $_POST['name'];
    $lab_id = (int)$_POST['lab_id'];
    $price = (float)$_POST['price'];
    $discounted_price = !empty($_POST['discounted_price']) ? (float)$_POST['discounted_price'] : null;
    $icon = $_POST['icon'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Make duration optional: set to null if empty
    if (empty($duration)) {
        $duration = null;
    }

    $sql = "INSERT INTO tests (name, lab_id, price, discounted_price, icon, description, duration, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidssssi", $name, $lab_id, $price, $discounted_price, $icon, $description, $duration, $is_available);

    if ($stmt->execute()) {
        echo "<script>alert('Test added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding test: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle city insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_city'])) {
    $name = $_POST['name'];
    $sql = "INSERT IGNORE INTO cities (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('City added successfully!');</script>";
            // Refresh cities
            $result_cities = $conn->query($sql_cities);
            $cities = [];
            if ($result_cities->num_rows > 0) {
                while($row = $result_cities->fetch_assoc()) {
                    $cities[] = $row;
                }
            }
        } else {
            echo "<script>alert('City already exists!');</script>";
        }
    } else {
        echo "<script>alert('Error adding city: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle city deletion
if (isset($_GET['delete_city'])) {
    $id = (int)$_GET['delete_city'];
    $sql = "DELETE FROM cities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('City deleted successfully!');</script>";
        // Refresh cities
        $result_cities = $conn->query($sql_cities);
        $cities = [];
        if ($result_cities->num_rows > 0) {
            while($row = $result_cities->fetch_assoc()) {
                $cities[] = $row;
            }
        }
    } else {
        echo "<script>alert('Error deleting city: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle city update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_city'])) {
    $id = (int)$_POST['city_id'];
    $name = $_POST['name'];
    $sql = "UPDATE cities SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        echo "<script>alert('City updated successfully!');</script>";
        // Refresh cities
        $result_cities = $conn->query($sql_cities);
        $cities = [];
        if ($result_cities->num_rows > 0) {
            while($row = $result_cities->fetch_assoc()) {
                $cities[] = $row;
            }
        }
    } else {
        echo "<script>alert('Error updating city: " . $stmt->error . "');</script>";
    }
    $stmt->close();
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
        header("Location: Admin.php");
        exit();
    } else {
        echo "<script>alert('Error deactivating doctor: " . $stmt->error . "');</script>";
    }
    $stmt->close();
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
        header("Location: Admin.php");
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
        header("Location: Admin.php");
        exit();
    } else {
        echo "<script>alert('Error rejecting doctor: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle blog insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_blog'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $sql = "INSERT INTO blogs (title, content, image) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $description, $image);
    if ($stmt->execute()) {
        echo "<script>alert('Blog added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding blog: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle blog deletion
if (isset($_GET['delete_blog'])) {
    $id = (int)$_GET['delete_blog'];
    $sql = "DELETE FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Blog deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting blog: " . $stmt->error . "');</script>";
    }
    $stmt->close();
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
            // Refresh doctors list
            $result_admin_doctors = $conn->query($sql_admin_doctors);
            $admin_doctors = [];
            if ($result_admin_doctors->num_rows > 0) {
                while($row = $result_admin_doctors->fetch_assoc()) {
                    $admin_doctors[] = $row;
                }
            }
        } else {
            echo "<script>alert('Error adding doctor details: " . $stmt_doctor->error . "');</script>";
        }
        $stmt_doctor->close();
    } else {
        echo "<script>alert('Error adding doctor user: " . $stmt_user->error . "');</script>";
    }
    $stmt_user->close();
}

// Handle lab insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_lab'])) {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $services = json_encode(array_filter(explode("\n", $_POST['services'])));

    $sql = "INSERT INTO labs (name, city, address, phone, services) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $city, $address, $phone, $services);

    if ($stmt->execute()) {
        echo "<script>alert('Lab added successfully!');</script>";
        // Refresh labs list
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
    } else {
        echo "<script>alert('Error adding lab: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
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
    <!-- Dashboard -->
    <div class="section">
      <h2 class="mb-4">Dashboard Overview</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <a href="doctors_list.php">
            <div class="card shadow-lg text-center p-4">
              <h3>Doctors</h3>
              <h1><?php echo $doctor_count; ?></h1>
              <p>Total Registered Doctors</p>
            </div>
          </a>
        </div>
        <div class="col-md-4">
          <a href="patients_list.php">
            <div class="card shadow-lg text-center p-4">
              <h3>Patients</h3>
              <h1><?php echo $patient_count; ?></h1>
              <p>Total Registered Patients</p>
            </div>
          </a>
        </div>
        <div class="col-md-4">
          <a href="tests_list.php">
            <div class="card shadow-lg text-center p-4">
              <h3>Tests</h3>
              <h1><?php echo $tests_count ?? 0; ?></h1>
              <p>Total Available Tests</p>
            </div>
          </a>
        </div>
      </div>
      <div class="row g-4 mt-3">
        <div class="col-md-4">
          <a href="packages_list.php">
            <div class="card shadow-lg text-center p-4">
              <h3>Packages</h3>
              <h1><?php echo count($packages) ?? 0; ?></h1>
              <p>Total Active Packages</p>
            </div>
          </a>
        </div>
        <div class="col-md-4">
          <a href="labs_list.php">
            <div class="card shadow-lg text-center p-4">
              <h3>Labs</h3>
              <h1><?php echo $labs_count ?? 0; ?></h1>
              <p>Total Registered Labs</p>
            </div>
          </a>
        </div>
        <div class="col-md-4">
          <a href="appointments.php">
            <div class="card shadow-lg text-center p-4">
              <h3>Appointments</h3>
              <h1><?php echo $appointments_count; ?></h1>
              <p>Total Appointments</p>
            </div>
          </a>
        </div>
      </div>
    </div>

    <!-- Doctors -->
    <div id="doctors_list" class="section d-none">
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

    <!-- Pending Doctors -->
    <div id="pending_doctors" class="section d-none">
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

    <!-- Patients -->
    <div id="patients_list" class="section d-none">
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
      <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Patients List</a>

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

    <!-- Add Patient -->
    <div id="add_patient" class="section d-none">
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

    <!-- Appointments -->
    <div id="appointments" class="section d-none">
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
            <a href="dashboard.php" class="btn btn-secondary ms-2">Clear</a>
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

    <!-- Tests -->
    <div id="tests_list" class="section d-none">
      <h2 class="mb-4">Available Tests</h2>
      <?php if (!empty($tests)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Lab Name</th>
            <th>Price</th>
            <th>Discounted Price</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tests as $test) { ?>
          <tr>
            <td><?php echo htmlspecialchars($test['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($test['name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($test['lab_name'] ?? ''); ?></td>
            <td><?php echo number_format($test['price'], 2); ?></td>
            <td><?php echo number_format($test['discounted_price'], 2); ?></td>
            <td><?php echo htmlspecialchars($test['desc'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($test['duration'] ?? ''); ?></td>
            <td>
              <a href="?delete_test=<?php echo htmlspecialchars($test['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this test?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No medical tests are currently available. Please check back later.</p>
      <?php } ?>


    </div>

    <!-- Packages List -->
    <div id="packages_list" class="section d-none">
      <h2 class="mb-4">Active Packages</h2>
      <?php if (!empty($packages)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Discounted Price</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Features</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($packages as $package) { ?>
          <tr>
            <td><?php echo htmlspecialchars($package['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($package['name'] ?? ''); ?></td>
            <td><?php echo number_format($package['price'], 2); ?></td>
            <td><?php echo number_format($package['discounted_price'], 2); ?></td>
            <td><?php echo htmlspecialchars($package['desc'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($package['duration'] ?? ''); ?></td>
            <td>
              <ul>
                <?php foreach ($package['features'] as $feature) { ?>
                <li><?php echo htmlspecialchars($feature ?? ''); ?></li>
                <?php } ?>
              </ul>
            </td>
            <td>
              <a href="?delete_package=<?php echo htmlspecialchars($package['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this package?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No active packages are currently available. Please check back later.</p>
      <?php } ?>


    </div>

    <!-- Labs List -->
    <div id="labs_list" class="section d-none">
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

    <!-- Cities List -->
    <div id="cities_list" class="section d-none">
      <h2 class="mb-4">Available Cities</h2>
      <?php if (!empty($cities)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cities as $city) { ?>
          <tr>
            <td><?php echo htmlspecialchars($city['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($city['name'] ?? ''); ?></td>
            <td>
              <a href="?delete_city=<?php echo htmlspecialchars($city['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this city?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No cities are currently available. Please check back later.</p>
      <?php } ?>


    </div>

    <!-- Blogs List -->
    <div id="blogs_list" class="section d-none">
      <h2 class="mb-4">Blogs</h2>
      <?php if (!empty($blogs)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($blogs as $blog) { ?>
          <tr>
            <td><?php echo htmlspecialchars($blog['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['title'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['description'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['image'] ?? ''); ?></td>
            <td>
              <a href="?delete_blog=<?php echo htmlspecialchars($blog['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this blog?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No blogs are currently available. Please check back later.</p>
      <?php } ?>

      <h3 class="mt-4">Add New Blog</h3>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="blog_title" class="form-label">Title</label>
            <input type="text" class="form-control" id="blog_title" name="title" required>
          </div>
          <div class="col-md-6">
            <label for="blog_image" class="form-label">Image URL</label>
            <input type="text" class="form-control" id="blog_image" name="image">
          </div>
          <div class="col-12">
            <label for="blog_description" class="form-label">Description</label>
            <textarea class="form-control" id="blog_description" name="description" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_blog" class="btn custom-btn">Add Blog</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Add Doctor -->
    <div id="add_doctor" class="section d-none">
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

    <!-- Add Lab -->
    <div id="add_lab" class="section d-none">
      <h2 class="mb-4">Add New Lab</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="lab_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="lab_name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="lab_city" class="form-label">City</label>
            <select class="form-select" id="lab_city" name="city" required>
              <option value="">Select City</option>
              <?php foreach ($cities as $city) { ?>
              <option value="<?php echo htmlspecialchars($city['name'] ?? ''); ?>"><?php echo htmlspecialchars($city['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="lab_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="lab_phone" name="phone" required>
          </div>
          <div class="col-md-6">
            <label for="lab_address" class="form-label">Address</label>
            <textarea class="form-control" id="lab_address" name="address" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <label for="lab_services" class="form-label">Services (one per line)</label>
            <textarea class="form-control" id="lab_services" name="services" rows="3" placeholder="Blood Test&#10;X-Ray&#10;MRI"></textarea>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_lab" class="btn custom-btn">Add Lab</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Add Package -->
    <div id="add_package" class="section d-none">
      <h2 class="mb-4">Add New Package</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="package_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="package_name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="package_price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="package_price" name="price" required>
          </div>
          <div class="col-md-6">
            <label for="package_discounted_price" class="form-label">Discounted Price (optional)</label>
            <input type="number" step="0.01" class="form-control" id="package_discounted_price" name="discounted_price">
          </div>
          <div class="col-md-6">
            <label for="package_duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="package_duration" name="duration" required>
          </div>
          <div class="col-12">
            <label for="package_description" class="form-label">Description</label>
            <textarea class="form-control" id="package_description" name="description" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <label for="package_features" class="form-label">Features (one per line)</label>
            <textarea class="form-control" id="package_features" name="features" rows="3" placeholder="Consultation&#10;Basic Tests&#10;Follow-up"></textarea>
          </div>
          <div class="col-md-6">
            <label for="package_image" class="form-label">Image URL</label>
            <input type="text" class="form-control" id="package_image" name="image">
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="package_is_active" name="is_active" checked>
              <label class="form-check-label" for="package_is_active">
                Is Active
              </label>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_package" class="btn custom-btn">Add Package</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Add Test -->
    <div id="add_test" class="section d-none">
      <h2 class="mb-4">Add New Test</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="test_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="test_name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="lab_id" class="form-label">Lab</label>
            <select class="form-select" id="lab_id" name="lab_id" required>
              <option value="">Select Lab</option>
              <?php foreach ($labs as $lab) { ?>
              <option value="<?php echo htmlspecialchars($lab['id'] ?? ''); ?>"><?php echo htmlspecialchars($lab['name'] ?? ''); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="test_price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="test_price" name="price" required>
          </div>
          <div class="col-md-6">
            <label for="test_discounted_price" class="form-label">Discounted Price (optional)</label>
            <input type="number" step="0.01" class="form-control" id="test_discounted_price" name="discounted_price">
          </div>
          <div class="col-md-6">
            <label for="icon" class="form-label">Icon (HTML entity)</label>
            <input type="text" class="form-control" id="icon" name="icon" placeholder="&#129298;">
          </div>
          <div class="col-md-6">
            <label for="test_duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="test_duration" name="duration">
          </div>
          <div class="col-12">
            <label for="test_description" class="form-label">Description</label>
            <textarea class="form-control" id="test_description" name="description" rows="3" required></textarea>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="is_available" name="is_available" checked>
              <label class="form-check-label" for="is_available">
                Is Available
              </label>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_test" class="btn custom-btn">Add Test</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Add City -->
    <div id="add_city" class="section d-none">
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

    <!-- Add Blog -->
    <div id="add_blog" class="section d-none">
      <h2 class="mb-4">Add New Blog</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="blog_title" class="form-label">Title</label>
            <input type="text" class="form-control" id="blog_title" name="title" required>
          </div>
          <div class="col-md-6">
            <label for="blog_image" class="form-label">Image URL</label>
            <input type="text" class="form-control" id="blog_image" name="image">
          </div>
          <div class="col-12">
            <label for="blog_description" class="form-label">Description</label>
            <textarea class="form-control" id="blog_description" name="description" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_blog" class="btn custom-btn">Add Blog</button>
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
