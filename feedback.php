<?php include __DIR__ . '/includes/session.php'; ?>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<?php
// Display success or error messages
if (isset($_SESSION['feedback_success'])) {
    echo '<div class="alert alert-success text-center">' . $_SESSION['feedback_success'] . '</div>';
    unset($_SESSION['feedback_success']);
}

if (isset($_SESSION['feedback_errors'])) {
    echo '<div class="alert alert-danger"><ul>';
    foreach ($_SESSION['feedback_errors'] as $error) {
        echo '<li>' . $error . '</li>';
    }
    echo '</ul></div>';
    unset($_SESSION['feedback_errors']);
}
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

    .card {
      border: none;
      background-color: var(--bs-white);
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      border-radius: var(--bs-br);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    }

    .form-control,
    .form-select {
      border-radius: var(--bs-br);
      border: 1px solid var(--bs-teal);
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #00796b;
      box-shadow: 0 0 8px rgba(13, 110, 253, 0.3);
    }
  </style>
</head>
<div class="container">
  <div class="row justify-content-center my-5">
    <div class="col-sm-12 col-lg-6">
      <div class="card shadow-lg p-4">
        <div class="card-body">
          <h3 class="text-center mb-4">
            <i class="bi bi-chat-dots-fill me-2"></i>We Value Your Feedback
          </h3>

          <form method="POST" action="submit_feedback.php">
            <div class="mb-3">
              <label for="name" class="form-label">Your Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  name="name"
                  placeholder="Enter your name" />
              </div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Your Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Enter your email" />
              </div>
            </div>

            <div class="mb-3">
              <label for="rating" class="form-label">Rate Us</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-star-fill"></i></span>
                <select class="form-select" id="rating" name="rating">
                  <option value="">Choose...</option>
                  <option value="5">Excellent</option>
                  <option value="4">Good</option>
                  <option value="3">Average</option>
                  <option value="2">Poor</option>
                  <option value="1">Very Poor</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="message" class="form-label">Your Feedback</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-pencil-fill"></i></span>
                <textarea
                  class="form-control"
                  id="message"
                  name="message"
                  rows="4"
                  placeholder="Write your feedback here..."></textarea>
              </div>
            </div>

            <button type="submit" class="btn custom-btn w-100">
              <i class="bi bi-send-fill me-2"></i>Submit
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
