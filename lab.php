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

    .card {
      border-radius: var(--bs-br);
      transition: transform .25s, box-shadow .25s;
    }

    .card:hover {
      border: 1px solid var(--bs-teal);
      transform: translateY(-8px);
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
    }
  </style>
</head>

<?php include __DIR__ . '/data/labs-data.php'; ?>

<!-- Services Section -->
<div id="services" class="container my-5">
  <h2 class="text-center mb-4">Our Lab Services</h2>
  <div class="row g-4">
    <?php
    foreach ($services as $service) {
      echo '<div class="col-md-3">
              <div class="card p-3 text-center">
                <h5>' . $service['title'] . '</h5>
                <p>' . $service['desc'] . '</p>
              </div>
            </div>';
    }
    ?>
  </div>
</div>

<!-- Laboratories by City Section -->
<div id="labs" class="container my-5">
  <h2 class="text-center mb-4">Our Laboratories Across Cities</h2>

  <!-- Search -->
  <div class="search-box text-center mb-4">
    <input
      type="text"
      id="labSearch"
      class="form-control w-50 mx-auto"
      placeholder="Search by city name..." />
  </div>

  <div class="row g-4" id="labList">
    <?php
    foreach ($labs as $lab) {
      echo '<div class="col-md-4">
              <div class="card highlight-card">
                <img src="' . $lab['img'] . '" alt="' . $lab['city'] . ' Lab" />
                <div class="card-body">
                  <h5><i class="bi bi-hospital me-2"></i> ' . $lab['city'] . '</h5>
                  <p>Located in ' . $lab['city'] . ', ' . (is_array($lab['services']) ? implode(', ', $lab['services']) : $lab['services']) . '.</p>
                  <p><strong><i class="bi bi-telephone-forward"></i> Contact:</strong> ' . $lab['phone'] . '</p>
                  <a href="#" class="btn btn-light btn-sm"><i class="bi bi-geo-alt"></i> Get Directions</a>
                </div>
              </div>
            </div>';
    }
    ?>
  </div>
</div>

<script>
  // ✅ Search filter for labs
  document.getElementById("labSearch").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let labs = document.querySelectorAll("#labList .highlight-card");
    labs.forEach(function(card) {
      let text = card.innerText.toLowerCase();
      card.parentElement.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // Handle initial search from URL
  const urlParams = new URLSearchParams(window.location.search);
  const initialSearch = urlParams.get('search');
  if (initialSearch) {
    document.getElementById("labSearch").value = initialSearch;
    document.getElementById("labSearch").dispatchEvent(new Event('keyup'));
  }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
