<?php
// Example static data (replace with DB later)
$healthTips = [
  "boost-immunity" => [
    "image" => "assets/images/blog1.jpg",
    "title" => "5 Ways to Boost Immunity",
    "description" => "Boosting your immunity is essential for staying healthy. Here are some natural ways to strengthen your immune system.",
    "extra" => [
      "Eat Well" => "Include fruits, vegetables, and whole foods in your diet.",
      "Exercise" => "Stay active with at least 30 mins of daily exercise.",
      "Sleep" => "Get 7-8 hours of proper rest every night.",
      "Stress Management" => "Practice meditation, yoga, or relaxation techniques.",
      "Hydration" => "Drink 2–3 liters of water daily."
    ]
  ],
  "health-checkups" => [
    "image" => "assets/images/blog2.jpg",
    "title" => "Why Regular Health Checkups Matter",
    "description" => "Prevention is better than cure. Regular checkups ensure early detection of health issues.",
    "extra" => [
      "Early Detection" => "Identify problems before they get serious.",
      "Cost-Effective" => "Avoid expensive treatments through preventive care.",
      "Monitoring" => "Keep track of blood pressure, sugar, cholesterol, etc.",
      "Personalized Care" => "Doctors guide you with lifestyle and medications."
    ]
  ],
  "managing-diabetes" => [
    "image" => "assets/images/blog3.jpg",
    "title" => "Managing Diabetes Effectively",
    "description" => "Living with diabetes requires discipline, lifestyle management, and regular monitoring.",
    "extra" => [
      "Diet" => "Follow a low-sugar, high-fiber diet with whole grains.",
      "Exercise" => "Daily walks, yoga, or gym workouts improve insulin sensitivity.",
      "Medication" => "Take prescribed medicines and insulin on time.",
      "Monitoring" => "Check blood sugar levels frequently.",
      "Lifestyle" => "Avoid stress and get proper sleep."
    ]
  ]
];

$tipSlug = isset($_GET['tip']) ? $_GET['tip'] : '';
$tip = $healthTips[$tipSlug] ?? null;
?>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<head>
  <style>
    body{
      background-color: #fff;
    }
    .tip-card {
      border: none;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .tip-img {
      height: 800px;
      object-fit: cover;
      object-position: top;
    }

    .info-item {
      margin-bottom: 0.8rem;
    }

    .info-item strong {
      color: var(--bs-teal);
    }

  </style>
</head>

<div class="container py-5">
  <?php if ($tip): ?>
    <!-- <div class="row justify-content-center">
      <div class="col-md-10"> -->
        <div class="card tip-card">
          <img src="<?= $tip['image'] ?>" class="card-img-top tip-img" alt="<?= $tip['title'] ?>">
          <div class="card-body">
            <h2 class="card-title text-center my-4"><?= $tip['title'] ?></h2>
            <p class="card-text fs-5"><?= $tip['description'] ?></p>

            <?php if (!empty($tip['extra'])): ?>
              <ul class="list-unstyled mt-3">
                <?php foreach ($tip['extra'] as $title => $info): ?>
                  <li class="info-item"><strong><?= $title ?>:</strong> <?= $info ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <div class="card-footer text-center bg-white border-0">
            <a href="index.php?#blog" class="btn custom-btn">← Back to Tips</a>
          </div>
        </div>
      <!-- </div>
    </div> -->
  <?php else: ?>
    <div class="alert alert-danger text-center">Sorry, the requested health tip was not found.</div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>