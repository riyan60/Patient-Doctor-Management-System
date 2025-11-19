<?php
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../config/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo '<!DOCTYPE html><html><head><title>Admin Login Required</title><link rel="stylesheet" href="../assets/css/bootstrap.css"><style>body { filter: blur(5px); } .modal { display: block; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; outline: 0; background-color: rgba(0,0,0,0.5); } .modal-dialog { position: relative; width: auto; margin: 10rem auto; pointer-events: none; } .modal-content { position: relative; display: flex; flex-direction: column; width: 100%; pointer-events: auto; background-color: #fff; background-clip: padding-box; border: 1px solid rgba(0,0,0,.2); border-radius: .3rem; outline: 0; }</style></head><body>';
    echo '<div class="modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Admin Login Required</h5></div><div class="modal-body"><p>You need to log in as an admin to access this page.</p></div><div class="modal-footer"><a href="../login.php" class="btn custom-btn">Login</a></div></div></div></div>';
    echo '</body></html>';
    exit();
}

$page_title = 'Blogs - Admin Dashboard';
include __DIR__ . '/../includes/dashboard_head.php';

// Fetch blogs
$sql_blogs = "SELECT id, title, content, author, created_at, updated_at FROM blogs ORDER BY created_at DESC";
$result_blogs = $conn->query($sql_blogs);
$blogs = [];
if ($result_blogs->num_rows > 0) {
    while($row = $result_blogs->fetch_assoc()) {
        $blogs[] = $row;
    }
}

// Handle blog insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_blog'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    $sql = "INSERT INTO blogs (title, content, author) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $content, $author);

    if ($stmt->execute()) {
        echo "<script>alert('Blog added successfully!');</script>";
        // Refresh blogs list
        $result_blogs = $conn->query($sql_blogs);
        $blogs = [];
        if ($result_blogs->num_rows > 0) {
            while($row = $result_blogs->fetch_assoc()) {
                $blogs[] = $row;
            }
        }
    } else {
        echo "<script>alert('Error adding blog: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle blog deletion
if (isset($_GET['delete_blog'])) {
    $id = (int)$_GET['delete_blog'];
    $sql = "DELETE FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Blog deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting blog: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Blogs - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../assets/css/bootstrap.css" />
  <link rel="stylesheet" href="../assets/css/bootstrap-grid.css" />
  <link rel="stylesheet" href="../assets/css/bootstrap-reboot.css" />
  <link rel="stylesheet" href="../assets/css/main.css" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
</head>

<body>
  <!-- Sidebar -->
 <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="content">
    <!-- Blogs List -->
    <div class="section">
      <h2 class="mb-4">Blogs</h2>
      <?php if (!empty($blogs)) { ?>
      <table class="table shadow-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($blogs as $blog) { ?>
          <tr>
            <td><?php echo htmlspecialchars($blog['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['title'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['author'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['created_at'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($blog['updated_at'] ?? ''); ?></td>
            <td>
              <a href="?delete_blog=<?php echo htmlspecialchars($blog['id'] ?? ''); ?>" onclick="return confirm('Are you sure you want to delete this blog?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>No blogs are currently available. Please check back later.</p>
      <?php } ?>
    </div>

    <!-- Add Blog -->
    <div class="section">
      <h2 class="mb-4">Add New Blog</h2>
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-12">
            <label for="blog_title" class="form-label">Title</label>
            <input type="text" class="form-control" id="blog_title" name="title" required>
          </div>
          <div class="col-12">
            <label for="blog_author" class="form-label">Author</label>
            <input type="text" class="form-control" id="blog_author" name="author" required>
          </div>
          <div class="col-12">
            <label for="blog_content" class="form-label">Content</label>
            <textarea class="form-control" id="blog_content" name="content" rows="10" required></textarea>
          </div>
          <div class="col-12">
            <button type="submit" name="insert_blog" class="btn custom-btn">Add Blog</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
<?php
// Close the database connection at the end of the script
$conn->close();
?>
