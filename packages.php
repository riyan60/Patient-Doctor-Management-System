<?php
include __DIR__ . '/data/packages-data.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

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

  .filter-bar {
    background: #fff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }
</style>

<section class="packages py-5">
  <div class="container">

    <!-- Header -->
    <div class="text-center mb-5">
      <h2 class="fw-bold text-lightgreen">Health Checkup Packages</h2>
      <p class="text-muted">
        Choose from our lab-certified health packages designed to keep you healthy and detect issues early.
      </p>
    </div>

    <!-- Filter/Search Bar -->
    <div class="filter-bar mb-5 d-flex flex-wrap justify-content-between align-items-center">
      <div class="mb-2">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search packages...">
      </div>
      <div>
        <select id="sortSelect" class="form-select">
          <option value="">Sort by</option>
          <option value="low">Price: Low to High</option>
          <option value="high">Price: High to Low</option>
        </select>
      </div>
    </div>

    <div class="row g-4" id="packagesGrid">
      <?php foreach ($packages as $id => $pkg): ?>
        <?php $discount_text = isset($pkg['discounted_price']) ? round((1 - $pkg['discounted_price'] / $pkg['price']) * 100) . "% Off" : ""; ?>
        <div class="col-md-6 col-lg-4 package"
          data-name="<?= htmlspecialchars($pkg['name']); ?>"
          data-desc="<?= htmlspecialchars($pkg['desc']); ?>"
          data-features="<?= htmlspecialchars(implode(' ', $pkg['features'])); ?>"
          data-price="<?= isset($pkg['discounted_price']) ? $pkg['discounted_price'] : $pkg['price']; ?>">
          <div class="card package-card d-flex flex-column border-0 h-100">
            <div class="card-header text-white text-center py-4">
              <h5 class="mb-0 fw-semibold"><?= htmlspecialchars($pkg['name']); ?></h5>
            </div>
            <div class="card-body d-flex flex-column p-4">
              <p class="text-muted mb-3"><?= $pkg['desc']; ?></p>
              <small class="text-muted mb-3">Validity: <?= $pkg['duration']; ?></small>
              <ul class="mb-4 text-start list-unstyled">
                <?php foreach ($pkg['features'] as $f): ?>
                  <li><?= $f; ?></li>
                <?php endforeach; ?>
              </ul>
              <div class="mt-auto text-center">
                <?php if (isset($pkg['discounted_price'])): ?>
                  <div class="badge">
                    <span class="fw-bold  d-flex justify-content-center align-items-center gap-2 mb-2">₹<?= $pkg['discounted_price']; ?></span>
                    <span><small class="text-muted"><s>₹<?= $pkg['price']; ?></s></small></span>
                  </div>


                <?php else: ?>
                  <span class="badge d-block mb-2">₹<?= $pkg['price']; ?></span>
                <?php endif; ?>
                <?php if (isset($pkg['discounted_price'])): ?>
                  <small class="text-success d-block mb-3"><?= $discount_text; ?></small>
                <?php endif; ?>
                <a href="addtocart.php?type=package&key=<?= urlencode($id); ?>" class="btn custom-btn w-100">Add To Cart</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>


  </div>
</section>

<!-- Custom JS for Search & Sort -->
<script>
  const searchInput = document.getElementById("searchInput");
  const sortSelect = document.getElementById("sortSelect");
  const packagesGrid = document.getElementById("packagesGrid");
  const packages = Array.from(document.querySelectorAll(".package"));

  // Search function
  searchInput.addEventListener("input", () => {
    filterAndSort();
  });

  // Sort function
  sortSelect.addEventListener("change", () => {
    filterAndSort();
  });

  function filterAndSort() {
    const query = searchInput.value.toLowerCase();
    let visiblePackages = packages.filter(pkg => {
      const name = pkg.getAttribute("data-name").toLowerCase();
      const desc = pkg.getAttribute("data-desc").toLowerCase();
      const features = pkg.getAttribute("data-features").toLowerCase();
      return name.includes(query) || desc.includes(query) || features.includes(query);
    });

    // Sort by price
    if (sortSelect.value === "low") {
      visiblePackages.sort((a, b) => a.getAttribute("data-price") - b.getAttribute("data-price"));
    } else if (sortSelect.value === "high") {
      visiblePackages.sort((a, b) => b.getAttribute("data-price") - a.getAttribute("data-price"));
    }

    // Update DOM
    packagesGrid.innerHTML = "";
    visiblePackages.forEach(pkg => packagesGrid.appendChild(pkg));
  }

  // Handle initial search from URL
  const urlParams = new URLSearchParams(window.location.search);
  const initialSearch = urlParams.get('search');
  if (initialSearch) {
    searchInput.value = initialSearch;
    filterAndSort();
  }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
