<?php
// Redirect to the improved version
header('Location: dashboard.php?section=book-appointment');
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Redirecting...</title>
  <meta http-equiv="refresh" content="0; url=dashboard.php?section=book-appointment">
</head>
<body>
  <p>Redirecting to improved appointment booking page...</p>
  <p>If you are not redirected automatically, <a href="dashboard.php?section=book-appointment">click here</a>.</p>
</body>
</html>
