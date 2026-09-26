<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

// Fetch category
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) die("Category not found");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $des = $_POST['des'];

    // Handle image upload if new file provided
    $imagePath = $row['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid() . '.' . $ext;
        if (!is_dir('../uploads')) mkdir('../uploads', 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], "../" . $imagePath);
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE categories SET title=?, des=?, image=? WHERE id=?");
    $stmt->execute([$title, $des, $imagePath, $id]);

    header("Location: categories.php");
    exit;
}
ob_start();
?>
<div class="container">
  <form method="post" enctype="multipart/form-data">
      <input class="input" type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" required><br><br>
      <input class="input" type="text" name="des" value="<?= htmlspecialchars($row['des']) ?>" required><br><br>
      <?php if(!empty($row['image'])): ?>
          <img src="../<?= $row['image'] ?>" width="50"><br>
      <?php endif; ?>
      <input class="input" type="file" name="image" accept=".jpg,.jpeg,.png"><br><br>
      <button class="button-primary" type="submit">Update</button>
  </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
