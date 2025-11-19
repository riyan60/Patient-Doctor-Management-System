<?php include 'session.php'; ?>
<header class="header_bg bg-light sticky-top">
  <div class="city-sel py-1">
    <div class="container d-flex justify-content-between align-items-center">
      <!-- City select -->
      <div>
<?php include __DIR__ . '/../data/city-data.php'; ?>
        <select class="form-select city-select border-0 bg-transparent" style="max-width: 150px;">
          <option value="">All Cities</option>
          <?php
          foreach ($cities as $city) {
            // Convert city name to lowercase for value attribute
            $value = strtolower($city);
            echo "<option value=\"$value\">$city</option>";
          }
          ?>
        </select>
      </div>
      <!-- Login/User -->
      <div>
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'patient'): ?>
          <div class="dropdown">
            <a class="dropdown-toggle" href="#" id="patientMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
              Welcome, <?php echo $_SESSION['full_name']; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="patientMenu">
              <a class="dropdown-item" href="PatientDash/dashboard.php">Patient Dashboard</a>
              <a class="dropdown-item logout-btn text-center" href="logout.php">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a class="nav-link log-btn text-white" href="login.php">Login/Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

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

    .logout-btn {
    background: transparent;
    color: #fff;
    font-family: var(--bs-font);
    font-weight: 600;
    transition: 0.3s;
    text-decoration: none !important;
  }

  .logout-btn:hover {
    color: var(--bs-red) !important;
  }

  #patientMenu{
    font-family: var(--bs-hfont);
    font-weight: bolder;
  }

  #patientMenu:hover{
    color: var(--bs-bg) !important;
  }
  </style>

  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <div class="navbar-brand fw-bold"><a href="index.php"><img src="<?php echo isset($company_logo) ? $company_logo : 'assets/images/logo.png'; ?>" alt="Logo" class=" logo_custom d-inline-block align-text-top"></a></div>


      <!-- Toggler -->
      <button class="navbar-toggler" type="button"
        data-bs-toggle="collapse" data-bs-target="#mainNav"
        aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span><img src="assets/images/menu.png" class="menu-icon" alt="menu"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          <li class="nav-item">
            <a class="nav-link" href="about.php">About</a>
          </li>

          <!-- Mega Menu -->
          <li class="nav-item dropdown position-static">
            <a class="nav-link dropdown-toggle" href="#" id="megaMenu" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Menu
            </a>
            <div class="dropdown-menu w-100 p-4 scrollable" aria-labelledby="megaMenu">
              <div class="row">

                <?php include __DIR__ . '/../data/packages-data.php'; ?>
                <div class="col-12 col-sm-6 col-md-4">
                  <h6 class="mt-3"><a href="packages.php">Packages</a></h6>
                  <ul class="list-unstyled">
                    <?php foreach (array_slice($packages, 0, 6) as $pkg): ?>
                      <li>
                        <a class="dropdown-item d-wrap" href="packages.php?search=<?= urlencode($pkg['name']); ?>">
                          <div class="d-flex flex-wrap justify-content-between">
                            <span><?= $pkg["name"] ?></span>
                            <span class="text-muted">₹<?= isset($pkg['discounted_price']) ? $pkg['discounted_price'] : $pkg['price']; ?></span>
                          </div>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <?php include __DIR__ . '/../data/tests-data.php'; ?>
                <div class="col-12 col-sm-6 col-md-4">
                  <h6 class="mt-3"><a href="tests.php">Tests</a></h6>
                  <ul class="list-unstyled">
                    <?php foreach (array_slice($tests, 0, 6) as $test): ?>
                      <li>
                        <a class="dropdown-item" href="tests.php?search=<?= urlencode($test['name']); ?>">
                          <div class="d-flex flex-wrap justify-content-between">
                            <span><?= $test["icon"] ?><?= $test["name"] ?></span>
                            <span class="text-muted">₹<?= isset($test['discounted_price']) ? $test['discounted_price'] : $test['price']; ?></span>
                          </div>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <?php include __DIR__ . '/../data/city-data.php'; ?>
                <div class="col-12 col-sm-6 col-md-4">
                  <h6 class="mt-3"><a href="lab.php">Labs</a></h6>
                  <ul class="list-unstyled">
                    <?php foreach (array_slice($cities, 0, 6) as $city): ?>
                      <li>
                        <a class="dropdown-item" href="lab.php?search=<?= urlencode($city); ?>">
                          <div class="d-flex flex-wrap justify-content-between">
                            <span><?= $city ?></span>
                          </div>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>

              </div>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="doctors.php">Doctors</a>
          </li>
          <div class="nav-item cart-img">
            <a href="cart.php"><img src="assets/images/cart.png" alt="Cart"></a>
          </div>
        </ul>
      </div>
    </div>
  </nav>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const citySelect = document.querySelector('.city-select');

      // Set initial value from cookie
      const selectedCity = getCookie('selected_city');
      if (selectedCity) {
        citySelect.value = selectedCity;
      }

      // On change, set cookie and refresh page
      citySelect.addEventListener('change', function() {
        if (this.value === '') {
          deleteCookie('selected_city');
        } else {
          setCookie('selected_city', this.value, 30);
        }
        location.reload();
      });
    });

    // Cookie functions
    function setCookie(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
      const expires = "expires=" + d.toUTCString();
      document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    function getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
    }

    function deleteCookie(name) {
      document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
  </script>

</header>
