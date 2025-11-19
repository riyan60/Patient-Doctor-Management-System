<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

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

  section {
    padding: 60px 40px;
    border-radius: var(--bs-br);
    background: white;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }

  .card {
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  .doctor {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .doctor img {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #2c7be5;
  }

  .lab-card img {
    border-radius: 12px;
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
</style>

<div class="container my-5">
  <!-- Company Info -->
  <section>
    <h2 class="text-center mb-5">Who We Are</h2>
    <p class="lead text-center">
      <?php echo $company_about_long; ?>
    </p>
  </section>

  <!-- Mission & Vision -->
  <section>
    <h2 class="text-center mb-5">Our Vision & Mission</h2>
    <div class="row text-center">
      <div class="col-md-6">
        <h5 class="text-primary">🌍 Vision</h5>
        <p>
          To be the most reliable healthcare network where every patient has
          access to quality medical services and expert doctors.
        </p>
      </div>
      <div class="col-md-6">
        <h5 class="text-primary">🎯 Mission</h5>
        <p>
          To connect people with the right doctors and labs, ensuring better
          health outcomes through innovation and compassion.
        </p>
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section>
    <h2 class="text-center mb-5">Why Choose HealthConnect?</h2>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="icon-box">
          <i class="fas fa-users"></i>
          <h6>Trusted Network</h6>
          <p>
            Thousands of patients rely on us for quality care every day.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="icon-box">
          <i class="fas fa-hospital"></i>
          <h6>Modern Facilities</h6>
          <p>
            Partnered with top hospitals and labs equipped with the latest
            technology.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="icon-box">
          <i class="fas fa-headset"></i>
          <h6>24/7 Support</h6>
          <p>
            Round-the-clock support to guide patients whenever they need
            help.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <!-- <section class="text-center" id="contact">
    <h2>Contact Us</h2>
    <p>Email: <strong>support@healthconnect.com</strong></p>
    <p>Phone: <strong>+1 234 567 890</strong></p>
    <p>Address: 123 Wellness Avenue, New York, USA</p>
  </section> -->
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
