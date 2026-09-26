<?php
include "../config/config.php"; // DB connection
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
// Handle upload
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner'])) {
    $file = $_FILES['banner'];
    if($file['size'] > 102400) { // 100 KB limit
        $error = "File size must be less than 100KB";
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'uploads/banners/' . uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], '../' . $filename);
        $stmt = $pdo->prepare("INSERT INTO banners (image) VALUES (?)");
        $stmt->execute([$filename]);
        header("Location: admin_banners.php");
        exit;
    }
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM banners WHERE id=?");
    $stmt->execute([$id]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    if($banner) {
        @unlink('../' . $banner['image']); // delete file
        $stmt = $pdo->prepare("DELETE FROM banners WHERE id=?");
        $stmt->execute([$id]);
        header("Location: admin_banners.php");
        exit;
    }
}

// Fetch all banners
$banners = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);


ob_start();
?>

<?php if($error) echo "<p style='color:red'>$error</p>"; ?>
<div class="container">
  <h2>Completed Orders</h2>
  <form method="POST" enctype="multipart/form-data" style="margin:2rem 0rem">
      <input class="input" type="file" name="banner" required>
      <button type="submit" class="button-primary">Upload Banner</button>
  </form>

  <!-- Banner List -->
  <table border="1" cellpadding="10" cellspacing="0">
      <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Action</th>
      </tr>
      <?php foreach($banners as $b): ?>
          <tr>
              <td><?= $b['id'] ?></td>
              <td><img src="../<?= $b['image'] ?>" width="150"></td>
              <td><a href="?delete=<?= $b['id'] ?>" onclick="return confirm('Delete this banner?')">Delete</a></td>
          </tr>
      <?php endforeach; ?>
  </table>
</div>
<!-- Upload Form -->

<?php
$content = ob_get_clean();
include 'layout.php';
?>
