<footer class="footer bg-light">
  <div class="container">
    <div class="row row-cols-2 row-cols-md-4 g-4">

      <!-- About -->
      <div class="col">
        <h5 class="fw-bold text-uppercase mb-3"><?php echo $company_name; ?></h5>
        <p>
          <?php echo $company_about_short; ?>
        </p>
      </div>

      <!-- Quick Links -->
      <div class="col">
        <h5 class="fw-bold text-uppercase mb-3">Quick Links</h5>
        <ul class="list-unstyled">
                    <li><a href="about.php">About Us</a></li>
          <li><a href="feedback.php">Feedback</a></li>
          <li><a href="index.php#specialities">Our Specialities</a></li>

          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="col">
        <h5 class="fw-bold text-uppercase mb-3">Contact</h5>
        <p class="small text-black mb-1"><i class="bi bi-geo-alt-fill me-2"></i> <?php echo $company_address; ?></p>
        <p class="small text-black mb-1"><i class="bi bi-telephone-fill me-2"></i> <?php echo $company_phone; ?></p>
        <p><i class="bi bi-envelope-fill me-2"></i> <?php echo $company_email; ?></p>
      </div>

      <!-- Social -->
      <div class="col">
        <h5 class="fw-bold text-uppercase mb-3">Follow Us</h5>
        <div class="d-flex gap-3">
          <a href="https://facebook.com" class="fs-5"><i class="bi bi-facebook"></i></a>
          <a href="#" class="fs-5"><i class="bi bi-twitter"></i></a>
          <a href="#" class="fs-5"><i class="bi bi-instagram"></i></a>
          <a href="#" class="fs-5"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

    </div>

    <hr class="mt-4 border-secondary">

    <div class="row">
      <div class="col-md-6 text-center text-md-start small">
        &copy; 2025 <strong>HealthConnect</strong>. All Rights Reserved.
      </div>
      
    </div>
  </div>
</footer>

<!-- Your main JS -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>