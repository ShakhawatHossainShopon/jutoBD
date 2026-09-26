<?php
include "../config/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $des = $_POST['des'];

    // Handle image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../" . $imagePath);
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO categories (title, des, image) VALUES (?, ?, ?)");
    $stmt->execute([$title, $des, $imagePath]);

    // Redirect to suppliers page
    header("Location: categories.php");
    exit;
}

ob_start();
?>

<div class="container">
  <h2>Add Supplier</h2>

  <form enctype="multipart/form-data" action="" method="post">
    <div>
      <input required class="input" type="text" name="title" required placeholder="Title">
      <input required class="input" type="text" name="des" required placeholder="Description">
      <input required class="input"  type="file"  name="image" accept=".jpg,.jpeg,.png" placeholder="Upload Image">
    </div>
    <button type="submit" class="button-primary" name="button">Add Catgeory</button>
  </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
