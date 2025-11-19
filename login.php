<?php
include __DIR__ . '/config/db_connect.php';
include __DIR__ . '/includes/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['login'])) {
    // Handle login
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];
    // $otp = $_POST['otp'];

    // Authenticate user
    $hashed_password = md5($password);
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$hashed_password' AND (is_active = 1 OR role = 'admin')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['full_name'] = $user['full_name'];
      $_SESSION['email'] = $user['email'];
      session_write_close();

      // Redirect based on role
      if ($user['role'] == 'patient') {
        header("Location: index.php");
      } elseif ($user['role'] == 'doctor') {
        header("Location: DoctorDash/dashboard.php");
      } elseif ($user['role'] == 'admin') {
        header("Location: AdminDash/dashboard.php");
      }
      exit();
    } else {
      echo "<script>alert('Invalid email or password');</script>";
    }
  } elseif (isset($_POST['create_patient'])) {
    // Handle patient signup
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    if (strlen($phone) == 10) $phone = substr($phone, 0, 5) . ' ' . substr($phone, 5);
    $date_of_birth = $_POST['date_of_birth'];
    $email = strtolower($_POST['email']);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    // Check if username already exists
    $check_username_sql = "SELECT id FROM users WHERE username = '$username'";
    $username_result = $conn->query($check_username_sql);

    // Check if email already exists
    $check_email_sql = "SELECT id FROM users WHERE email = '$email'";
    $email_result = $conn->query($check_email_sql);

    // Check if phone already exists
    $check_phone_sql = "SELECT id FROM users WHERE phone = '$phone'";
    $phone_result = $conn->query($check_phone_sql);

    if ($username_result->num_rows > 0) {
      echo "<script>alert('Username already exists. Please choose another username.');</script>";
    } elseif ($email_result->num_rows > 0) {
      echo "<script>alert('Email already exists. Please use another email.');</script>";
    } elseif ($phone_result->num_rows > 0) {
      echo "<script>alert('Phone number already exists. Please use another phone number.');</script>";
    } else {
      // Insert patient into users table
      $hashed_password = md5($password);
      $sql = "INSERT INTO users (username, full_name, phone, date_of_birth, email, password, city, gender, address, role, is_active) VALUES ('$username', '$fullname', '$phone', '$date_of_birth', '$email', '$hashed_password', '$city', '$gender', '$address', 'patient', 1)";
      if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Patient account created for $fullname');</script>";
      } else {
        echo "<script>alert('Error creating patient account: " . $conn->error . "');</script>";
      }
    }
  } elseif (isset($_POST['create_doctor'])) {
    // Handle doctor signup
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    if (strlen($phone) == 10) $phone = substr($phone, 0, 5) . ' ' . substr($phone, 5);
    $username = $_POST['username'];
    $email = strtolower($_POST['email']);
    $busi_email = strtolower($_POST['busi_email']);
    $password = $_POST['password'];
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $speciality = $_POST['speciality'];
    $experience = $_POST['experience'] . " Years";
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $availability = $_POST['availability'];

    // Calculate age
    if (empty($date_of_birth)) {
      echo "<script>alert('Please enter your date of birth.');</script>";
    } else {

      $dob = date_create($date_of_birth);
      $today = date_create('today');
      $age = date_diff($dob, $today)->y;

      if ($age < 21) {
        echo "<script>alert('Doctor must be at least 21 years old.');</script>";
      } else {
        // Check if username already exists
      $check_username_sql = "SELECT id FROM users WHERE username = '$username'";
      $username_result = $conn->query($check_username_sql);

      // Check if email already exists
      $check_email_sql = "SELECT id FROM users WHERE email = '$email'";
      $email_result = $conn->query($check_email_sql);

      // Check if phone already exists
      $check_phone_sql = "SELECT id FROM users WHERE phone = '$phone'";
      $phone_result = $conn->query($check_phone_sql);

      if ($username_result->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose another username.');</script>";
      } elseif ($email_result->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use another email.');</script>";
      } elseif ($phone_result->num_rows > 0) {
        echo "<script>alert('Phone number already exists. Please use another phone number.');</script>";
      } else {
        // Insert into users table
        $hashed_password = md5($password);
        $sql_user = "INSERT INTO users (username, full_name, phone, date_of_birth, email, password, city, gender, address, role, is_active) VALUES ('$username', '$fullname', '$phone', '$date_of_birth', '$email', '$hashed_password', '$city', '$gender', '$address', 'doctor', 1)";
        if ($conn->query($sql_user) === TRUE) {
          $user_id = $conn->insert_id;

          // Get specialty_id
          $sql_specialty = "SELECT id FROM specialties WHERE name = '$speciality'";
          $result_specialty = $conn->query($sql_specialty);
          if ($result_specialty->num_rows > 0) {
            $specialty_row = $result_specialty->fetch_assoc();
            $specialty_id = $specialty_row['id'];

            // Insert into doctors table
            $sql_doctor = "INSERT INTO doctors (user_id, specialty_id, experience, availability, city, phone, busi_email, is_active) VALUES ($user_id, $specialty_id, '$experience', '$availability', '$city', '$phone', '$busi_email', 1)";
            if ($conn->query($sql_doctor) === TRUE) {
              echo "<script>alert('Doctor account created for $fullname');</script>";
            } else {
              echo "<script>alert('Error creating doctor profile: " . $conn->error . "');</script>";
            }
          } else {
            echo "<script>alert('Invalid specialty selected');</script>";
          }
        } else {
          echo "<script>alert('Error creating user account: " . $conn->error . "');</script>";
        }
      }
    }
  }
}
}

?>
<?php include __DIR__ . '/data/speciality-data.php'; ?>
<?php include __DIR__ . '/data/city-data.php'; ?>

<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<head>
  <style>
    :root {
      --bs-bg: #e3f3f6;
      --bs-black: #000 !important;
      --bs-white: #fff !important;
      --bs-teal: #00796b;
      --bs-lteal: #72c5bb;
      --bs-br: 0.3em !important;
      --bs-font: 'Roboto', sans-serif !important;
      --bs-hfont: "Montserrat", sans-serif;
      --bs-gradient: linear-gradient(to right, red, blue) !important;
    }

    .form-box {
      background: white;
      padding: 25px;
      border-radius: var(--bs-br);
      width: 400px;
      max-height: 100vh;
      overflow-y: auto;
      box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2);
    }

    .toggle-btn {
      background-color: #00796b;
      color: #fff;
      border-radius: var(--bs-br);
      margin: 15px;
      width: 150px;
    }

    .toggle-btn:hover {
      background-color: #004d40;
      color: #fff;
    }

    input,
    select {
      border: 1px solid var(--bs-teal);
      border-radius: var(--bs-br);
      padding: 10px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    }

    .otp-input {
      width: 40px;
      text-align: center;
      font-size: 18px;
      margin: 5px;
    }

    .log-btn,
    .otp-btn,
    .pat-doc {
      color: #fff;
      border: none;
      border-radius: var(--bs-br);
      padding: 6px;
      width: 80%;
      margin: auto;
    }

    .otp-btn,
    .pat-doc {
      margin-bottom: 10px;
      background-color: #3bc4b8ff;
    }

    input[type="radio"] {
      accent-color: var(--bs-teal);
      transform: scale(1.2);
      margin-left: 10px;
      margin-bottom: 5px;
    }
  </style>
</head>


<div class="container d-flex justify-content-center align-items-center vh-100">

  <div class="form-box">
    <h3 class="text-center mb-3">HealthConnect</h3>

    <!-- Toggle Buttons -->
    <div class="d-flex justify-content-center mb-3">
      <button class="btn toggle-btn" id="loginTab">Login</button>
      <button class="btn toggle-btn" id="createTab">Create Account</button>
    </div>

    <!-- Login Form -->
    <form method="POST" id="loginForm">
      <div class="mb-3">
        <h5 class="text-center mb-4">Welcome Back!</h5>
        <label>Email:</label>
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>
      <div class="mb-3">
        <!-- <label>One-Time Password (OTP)</label>
        <div>
          <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
          <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
          <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
          <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
          <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
          <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
        </div> -->
      </div>
      <div class="d-grid gap-2">
        <button type="button" class="otp-btn">Send OTP</button>
        <button type="submit" class="btn custom-btn" name="login">Login</button>
      </div>
    </form>

    <!-- Account Type Selection -->
    <div id="accountType" style="display:none;">
      <h5 class="text-center">Select Account Type</h5>
      <div class="d-grid gap-2">
        <button class="btn pat-doc" id="patientBtn">Patient</button>
        <button class="btn pat-doc" id="doctorBtn">Doctor</button>
      </div>
    </div>

    <!-- Patient Form -->
    <form method="POST" id="patientForm" style="display:none;">
      <h5 class="text-center mb-4">Create Patient Account</h5>
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="fullname" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required oninput="formatPhone(this)" maxlength="12">
      </div>
      <div class="mb-3">
        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Create Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>City</label>
        <input list="cityList" name="city" class="form-control" placeholder="-- Select City --" required>
      </div>
      <div class="mb-3">
        <label>Gender</label><br>
        <input type="radio" name="gender" value="male" required> Male
        <input type="radio" name="gender" value="female" required> Female
        <input type="radio" name="gender" value="other" required> Other
      </div>
      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="3" placeholder="hno,landmark,city,state" required></textarea>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn custom-btn mt-3" name="create_patient">Create Account</button>
      </div>
    </form>

    <!-- Doctor Form -->
    <form method="POST" id="doctorForm" style="display:none;">
      <h5 class="text-center mb-4">Create Doctor Account</h5>
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="fullname" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required oninput="formatPhone(this)" maxlength="12">
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Business Email</label>
        <input type="email" name="busi_email" class="form-control">
      </div>
      <div class="mb-3">
        <label>Create Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Speciality</label>
        <select id="speciality" name="speciality" class="form-control" required>
          <option value="">-- Select Speciality --</option>
          <?php foreach ($specialties as $key => $value): ?>
            <option value="<?php echo strtolower($key); ?>"><?php echo $key; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Experience (Years)</label>
        <input type="number" name="experience" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>City</label>
        <input list="cityList" name="city" class="form-control" placeholder="-- Select City --" required>
      </div>
      <div class="mb-3">
        <label>Gender</label><br>
        <input type="radio" name="gender" value="male" required> Male
        <input type="radio" name="gender" value="female" required> Female
        <input type="radio" name="gender" value="other" required> Other
      </div>
      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="3" required></textarea>
      </div>
      <div class="mb-3">
        <label>Availability</label>
        <input type="text" name="availability" class="form-control" placeholder="e.g., Mon-Fri 9am-5pm" required>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn custom-btn mt3" name="create_doctor">Create Account</button>
      </div>
    </form>

    <datalist id="cityList">
      <?php foreach ($cities as $city): ?>
        <option value="<?php echo $city; ?>">
      <?php endforeach; ?>
    </datalist>
  </div>

</div>


<script src="main.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const headerCitySelect = document.querySelector('.city-select');
    const patientCityInput = document.querySelector('#patientForm input[name="city"]');
    const doctorCityInput = document.querySelector('#doctorForm input[name="city"]');

    if (headerCitySelect && patientCityInput && doctorCityInput) {
      headerCitySelect.addEventListener('change', function() {
        const selectedCity = this.options[this.selectedIndex].text;
        if (selectedCity !== 'All Cities') {
          patientCityInput.value = selectedCity;
          doctorCityInput.value = selectedCity;
        } else {
          patientCityInput.value = '';
          doctorCityInput.value = '';
        }
      });
    }
  });

  document.getElementById('loginTab').addEventListener('click', () => {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('accountType').style.display = 'none';
    document.getElementById('patientForm').style.display = 'none';
    document.getElementById('doctorForm').style.display = 'none';
  });

  document.getElementById('createTab').addEventListener('click', () => {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('accountType').style.display = 'block';
  });

  document.getElementById('patientBtn').addEventListener('click', () => {
    document.getElementById('loginTab').style.display = 'none';
    document.getElementById('createTab').style.display = 'none';
    document.getElementById('accountType').style.display = 'none';
    document.getElementById('patientForm').style.display = 'block';
  });

  document.getElementById('doctorBtn').addEventListener('click', () => {
    document.getElementById('loginTab').style.display = 'none';
    document.getElementById('createTab').style.display = 'none';
    document.getElementById('accountType').style.display = 'none';
    document.getElementById('doctorForm').style.display = 'block';
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
