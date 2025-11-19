<?php
$filterSpecialty = isset($_GET['specialty']) ? $_GET['specialty'] : 'all';
?>


<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
include __DIR__ . '/data/speciality-data.php';
?>

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

    body{
      background-color: #fff;
    }

    .specialty-card {
      border: none;
      border-radius: var(--bs-br);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .specialty-card:hover {
      transform: translateY(-8px);
      border: 1px solid var(--bs-teal);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .specialty-img {
      height: 180px;
      object-fit: contain;
      margin-top: 10px;
    }

    .doctor-badge {
      display: inline-block;
      margin: 3px;
      padding: 5px 10px;
      background: var(--bs-white);
      color: var(--bs-teal);
      border: .8px solid var(--bs-teal);
      border-radius: var(--bs-br);
      font-size: 1rem;
    }

    p,
    h6 {
      font-family: "Times New Roman", serif;
    }
  </style>
</head>

<div class="container py-5">
  <h1 class="text-center mb-4">Our Specialties</h1>

  <!-- Filter Dropdown -->
  <div class="mb-4 text-center">
    <select id="specialtyFilter" class="form-select w-auto d-inline-block">
      <option value="all" <?= $filterSpecialty === 'all' ? 'selected' : '' ?>>Show All</option>
      <?php foreach ($specialties as $name => $data): ?>
        <option value="<?= $name ?>" <?= $filterSpecialty === $name ? 'selected' : '' ?>><?= $name ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Cards -->
  <div class="row g-4" id="specialtyCards">
    <?php foreach ($specialties as $name => $data): ?>
      <div class="col-md-12 col-lg-10 specialty-item mx-auto" data-specialty="<?= $name ?>">
        <div class="card specialty-card h-100">
          <img src="<?= $data['image'] ?>" class="card-img-top specialty-img" alt="<?= $name ?>">
          <div class="card-body">
            <h3 class="card-title text-center text-uppercase"><?= $name ?></h3>
            <p class="card-text fs-4"><?= $data['description'] ?></p>

            <?php if (!empty($data['extra'])): ?>
              <ul class="list-unstyled">
                <?php foreach ($data['extra'] as $title => $info): ?>
                  <li><strong><?= $title ?>:</strong> <?= $info ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <h6 class="mt-3">Available Doctors:</h6>
            <?php foreach ($data['doctors'] as $doc): ?>
              <span class="doctor-badge"><?= $doc ?></span>
            <?php endforeach; ?>
          </div>
          <div class="card-footer text-center bg-white border-0">
            <a href="doctors.php?specialty=<?= urlencode($name) ?>" class="btn custom-btn">View Doctors</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  // Filter specialties on dropdown change
  document.getElementById('specialtyFilter').addEventListener('change', function() {
    let selected = this.value;
    let items = document.querySelectorAll('.specialty-item');

    items.forEach(item => {
      if (selected === 'all' || item.getAttribute('data-specialty') === selected) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  });

  // Filter on page load based on PHP parameter
  window.addEventListener('DOMContentLoaded', () => {
    let selected = "<?= $filterSpecialty ?>";
    let items = document.querySelectorAll('.specialty-item');

    items.forEach(item => {
      if (selected === 'all' || item.getAttribute('data-specialty') === selected) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
