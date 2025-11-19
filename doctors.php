<?php
// Include doctor data from Doctor's.php
include __DIR__ . '/data/doctors-data.php';

// Get specialty from URL if available
$specialtySlug = isset($_GET['specialty']) ? $_GET['specialty'] : '';
$filteredDoctors = [];

if ($specialtySlug && isset($doctors[$specialtySlug])) {
  $filteredDoctors[$specialtySlug] = $doctors[$specialtySlug];
} else {
  $filteredDoctors = $doctors; // show all if no specialty is selected
}

?>

<?php
$searchQuery = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>




<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<div class="container" style="margin-top: 80px;">
  <h2 class="fw-bold mb-3 text-center">Find a Doctor</h2>
  <!-- Search Bar -->
  <form class="d-flex justify-content-center mb-4" id="doctorSearchForm">
    <input type="text" id="doctorSearchInput" class="form-control me-2" style="max-width: 400px;" placeholder="Search by name or specialty..."
      value="<?= $searchQuery ?>" onkeyup="filterDoctors()">
    <button type="button" class="btn custom-btn" onclick="filterDoctors()">Search</button>
  </form>


  <h1 style="text-align: center; margin-bottom: 40px; font-weight:bold;">Our Doctors</h1>

  <div class="row" id="doctorCards">
    <?php foreach ($filteredDoctors as $specialty => $docList): ?>
      <?php foreach ($docList as $doc): ?>
        <div class="col-md-4 mb-4 doctor-card-wrapper" data-name="<?php echo strtolower($doc['name']); ?>" data-specialty="<?php echo strtolower($specialty); ?>">
          <div class="card doctor-card shadow-sm h-100">
            <div class="card-body text-center">
              <img src="<?php echo htmlspecialchars($doc['img']); ?>" alt="<?php echo htmlspecialchars($specialty); ?>" class="mb-3 rounded-circle" style="width:100px;height:100px;object-fit:cover;border:4px solid #00796b;">
              <h5 class="card-title"><strong><?php echo htmlspecialchars($doc['name']); ?></strong></h5>
              <p class="card-text"><strong><?php echo htmlspecialchars($specialty); ?></strong></p>
              <p class="card-text"><strong>City:</strong> <?php echo htmlspecialchars($doc['city']); ?></p>
              <p class="card-text"><strong>Rating:</strong> <?php echo str_repeat('⭐', floor($doc['rating'])) . str_repeat('☆', 5 - floor($doc['rating'])); ?> (<?php echo $doc['rating']; ?>/5)</p>
              <p class="card-text"><strong>Contact:</strong> <a href="tel:<?php echo htmlspecialchars($doc['phone']); ?>">Call</a> | <a href="mailto:<?php echo htmlspecialchars($doc['email']); ?>">Email</a></p>
              <p class="card-text"><strong>Services:</strong> <?php echo implode(', ', $doc['services']); ?></p>
              <p class="card-text"><strong>Experience:</strong> <?php echo htmlspecialchars($doc['experience']); ?></p>
              <p class="card-text"><strong>Available:</strong> <?php echo htmlspecialchars($doc['available']); ?></p>
              <a href="PatientDash/dashboard.php?section=book-appointment&doctor=<?php echo urlencode($doc['name']); ?>&specialty=<?php echo urlencode($specialty); ?>" class="btn custom-btn">Book Appointment</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
</div>

<script>
  function filterDoctors() {
    let input = document.getElementById('doctorSearchInput').value.toLowerCase();
    let cards = document.getElementsByClassName('doctor-card-wrapper');
    for (let i = 0; i < cards.length; i++) {
      let name = cards[i].dataset.name;
      let specialty = cards[i].dataset.specialty;
      if (name.includes(input) || specialty.includes(input)) {
        cards[i].style.display = '';
      } else {
        cards[i].style.display = 'none';
      }
    }
  }

  // Trigger filter automatically if there is a search query
  window.addEventListener('DOMContentLoaded', () => {
    <?php if ($searchQuery): ?>
      filterDoctors();
    <?php endif; ?>
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
