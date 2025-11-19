<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
include __DIR__ . '/data/packages-data.php';
include __DIR__ . '/data/tests-data.php';
include __DIR__ . '/data/labs-data.php';
include __DIR__ . '/data/speciality-data.php';
include __DIR__ . '/data/faq-data.php';
include __DIR__ . '/data/blog-data.php';
?>

<section class="hero" id="hero">
  <div class="slideshow">

    <!-- Slide 1 -->
    <div class="slide active" style="background-image: url('assets/images/img1.jpg');">
      <div class="overlay"></div>
      <div class="hero-content container">
        <h1>Your Health, Our Priority</h1>
        <p>Find trusted doctors, book appointments, and consult online — all in one place.</p>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="slide" style="background-image: url('assets/images/img2.webp');">
      <div class="overlay"></div>
      <div class="hero-content container">
        <h1>Expert Care, Anytime</h1>
        <p>Book instant consultations with specialists across every field.</p>
        <a href="#specialities" class="btn slide-btn">Explore Specialities</a>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="slide" style="background-image: url('assets/images/img3.jpg');">
      <div class="overlay"></div>
      <div class="hero-content container">
        <h1>Seamless Appointments</h1>
        <p>Easy scheduling with top-rated doctors near you.</p>
        <a href="PatientDash/dashboard.php?section=book-appointment" class="btn slide-btn">Book Appointment</a>
      </div>
    </div>

  </div>
</section>


<section class="blood-test-section" id="blood-test-section">
  <div class="container">
    <div class="row align-items-center g-5">

      <!-- Left Side: Image -->
      <div class="col-lg-6">
        <img src="assets/images/docter.jpg" height="100" alt="Medical Test" class="img-fluid rounded-3 w-100">
      </div>

      <!-- Right Side: Content -->
      <div class="col-lg-6">
        <!-- <h2 class="fw-bold mb-3">Find a Doctor</h2> -->

        <!-- Search Bar -->
        <!-- <form action="Doctor's.php" method="get" class="d-flex justify-content-center mb-4" id="doctorSearchForm">
          <input type="text" name="search" id="doctorSearchInput" class="form-control me-2" style="max-width: 400px;" placeholder="Search by name or specialty..." onkeyup="filterDoctors()">
          <button type="submit" class="btn custom-btn">Search</button>
        </form> -->
        <h2 class="fw-bold mb-3">Find the Right Test for You</h2>
        <p class="text-muted mb-4">
          Choose from thousands of lab-certified tests and packages. Book online, visit nearby labs, or get samples collected at home with ease.
        </p>

        <!-- Features -->
        <div class="row g-4">
          <div class="col-6">
            <div class="d-flex align-items-start">
              <i class="bi bi-hospital fs-2 text-secondary me-3"></i>
              <div>
                <p class="mb-0 text-muted">Choose from<br><strong><?php echo $tests_count; ?>+ Tests</strong></p>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="d-flex align-items-start">
              <i class="bi bi-microscope fs-2 text-secondary me-3"></i>
              <div>
                <p class="mb-0 text-muted">Partnered with<br><strong><?php echo $labs_count; ?>+ Labs</strong></p>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="d-flex align-items-start">
              <i class="bi bi-truck fs-2 text-secondary me-3"></i>
              <div>
                <p class="mb-0 text-muted">Sample Collection<br><strong>at Your Home</strong></p>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="d-flex align-items-start">
              <i class="bi bi-file-earmark-text fs-2 text-secondary me-3"></i>
              <div>
                <p class="mb-0 text-muted">Get Reports<br><strong>Online & Fast</strong></p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</section>



<section class="categories" id="specialities">
  <div class="container">
    <h2 class="mb-4 text-center">Our Specialties</h2>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 justify-content-center">
      <?php foreach ($specialties as $name => $data): ?>
      <div class="col">
        <a href="specialities.php?specialty=<?= urlencode($name) ?>" class="text-decoration-none">
          <div class="card h-100 text-center">
            <img src="<?= htmlspecialchars($data['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($name) ?>">
            <div class="card-body p-2">
              <p class="card-text"><?= htmlspecialchars($name) ?></p>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="packages" id="packages">
  <div class="container">

    <!-- Header -->
    <div class="text-center mb-5">
      <h2>Health Checkup Packages</h2>
      <p class="text-muted">
        Choose from our lab-certified health packages designed to keep you healthy and detect issues early.
      </p>
    </div>

    <!-- Packages Grid -->
    <div class="row g-4" id="packagesGrid">
      <?php
      // Show only first 3 packages
      $topPackages = array_slice($packages, 0, 3, true);
      foreach ($topPackages as $key => $pkg): ?>
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
                <a href="addtocart.php?type=package&key=<?= urlencode($key); ?>" class="btn custom-btn w-100">Add To Cart</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Explore More -->
    <div class="text-center">
      <a href="packages.php" class="btn custom-btn mt-5 px-4">Explore more Health Packages</a>
    </div>

  </div>
</section>


<section class="blog" id="blog">
  <div class="container">
    <h2 class="text-center mb-4">Latest Health Tips</h2>
    <div class="row g-4">
      <?php foreach ($blogs as $blog): ?>
      <div class="col-md-4">
        <div class="card br h-100">
          <img src="<?php echo htmlspecialchars($blog['image']); ?>" class="card-img-top br" alt="Blog">
          <div class="card-body">
            <h5 class="fw-bold"><?php echo htmlspecialchars($blog['title']); ?></h5>
            <p class="text-muted"><?php echo htmlspecialchars($blog['desc']); ?></p>
            <a href="<?php echo htmlspecialchars($blog['link']); ?>" class="btn custom-btn custom-btn-sm">Read More</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<section class="faq">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Frequently Asked Questions</h2>
    <div class="accordion" id="faqAccordion">
      <?php foreach ($faqs as $index => $faq):
        $id = $index + 1;
        $is_expanded = $faq['expanded'] ? ' show' : '';
        $btn_class = $faq['expanded'] ? '' : ' collapsed';
        $item_class = $index === 0 ? ' br' : '';
      ?>
        <div class="accordion-item<?php echo $item_class; ?>">
          <h2 class="accordion-header" id="q<?php echo $id; ?>">
            <button class="accordion-button<?php echo $btn_class; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#a<?php echo $id; ?>">
              <?php echo htmlspecialchars($faq['question']); ?>
            </button>
          </h2>
          <div id="a<?php echo $id; ?>" class="accordion-collapse collapse<?php echo $is_expanded; ?>" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              <?php echo htmlspecialchars($faq['answer']); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>




<section class="about py-5">
  <div class="container">
    <div class="row align-items-stretch g-3">

      <!-- Left Image Column -->
      <div class="col-md-6">
        <div class="about-img h-100">
        <img src="assets/images/about.webp" alt="img">
        </div>
      </div>

      <!-- Right Side Content (50%) -->
      <div class="col-md-6 d-flex flex-column justify-content-center">
        <h2 class="fw-bold">About Us</h2>
        <p class="text-muted mb-4">
          <?php echo $company_about_index; ?>
        </p>

        <ul class="list-unstyled mb-4">
          <li class="mb-2">
            <i class="bi bi-check-circle-fill me-2"></i>
            ISO Certified Facility
          </li>
          <li class="mb-2">
            <i class="bi bi-check-circle-fill me-2"></i>
            Advanced Diagnostic Equipment
          </li>
          <li class="mb-2">
            <i class="bi bi-check-circle-fill me-2"></i>
            Trusted by Leading Hospitals
          </li>
          <li class="mb-2">
            <i class="bi bi-check-circle-fill me-2"></i>
            24/7 Patient & Doctor Support
          </li>
        </ul>

      </div>

    </div>
  </div>
</section>

<a href="tel:<?php echo $company_phone; ?>"
  class="custom-btn d-flex align-items-center justify-content-center shadow-lg"
  style="position: fixed; bottom: 20px; right: 20px; z-index: 999;
          background: #00796b; color: #fff; font-weight: 600;
          border-radius: 50px; padding: 6px 10px; 
          box-shadow: 0 6px 16px rgba(0,0,0,0.3); 
          transition: all 0.3s ease;">
  <i class="bi bi-telephone-fill me-2 fs-5"></i> Call Now
</a>

<?php include __DIR__ . '/includes/footer.php'; ?>
