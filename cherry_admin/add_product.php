<?php
include "../config/config.php";

// Function to generate 4-character alphanumeric UID
function generateUID($length = 4) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $uid = '';
    for ($i = 0; $i < $length; $i++) {
        $uid .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $uid;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $wholesale = $_POST['wholesale'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $description = $_POST['description'];
    $colors = $_POST['colors'];

    // calculate profit
    $profit = $price - $wholesale;

    // handle multiple image uploads
    $images = [];
    $maxSize = 100 * 1024; // 100 KB in bytes
    if (!empty($_FILES['image']['name'][0])) {
        foreach ($_FILES['image']['tmp_name'] as $key => $tmpName) {
            $fileSize = $_FILES['image']['size'][$key];
            if ($fileSize > $maxSize) {
                die("Error: File " . $_FILES['image']['name'][$key] . " exceeds 100KB limit.");
            }

            $ext = pathinfo($_FILES['image']['name'][$key], PATHINFO_EXTENSION);
            $imagePath = 'uploads/' . uniqid() . '.' . $ext;
            move_uploaded_file($tmpName, "../" . $imagePath);
            $images[] = $imagePath;
        }
    }

    $imagesJson = json_encode($images);

    // generate 4-char UID and ensure uniqueness
    do {
        $uid = generateUID();
        $exists = $pdo->prepare("SELECT COUNT(*) FROM products WHERE uid=?");
        $exists->execute([$uid]);
    } while ($exists->fetchColumn() > 0);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO products (uid, images, name, category_id, supplier_id, wholesale, price, size, description, colors, profit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$uid, $imagesJson, $name, $category_id, $supplier_id, $wholesale, $price, $size, $description, $colors, $profit]);

    header("Location: products.php");
    exit;
}

// fetch categories and suppliers
$categories = $pdo->query("SELECT id, title FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container">
  <h2>Add Product</h2>
  <form method="post" enctype="multipart/form-data">
    <input class="input" type="text" name="name" placeholder="Product Name" required><br>
    <select name="category_id" class="input" required>
      <option value="">Select Category</option>
      <?php foreach($categories as $c): ?>
        <option value="<?= $c['id'] ?>"><?= $c['title'] ?></option>
      <?php endforeach; ?>
    </select><br>
    <select name="supplier_id" class="input" required>
      <option value="">Select Supplier</option>
      <?php foreach($suppliers as $s): ?>
        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
      <?php endforeach; ?>
    </select><br>
    <input class="input" type="number" required step="0.01" name="wholesale" placeholder="Wholesale Price" required><br>
    <input class="input" type="number" required step="0.01" name="price" placeholder="Price" required><br>
    <input class="input" type="text" required name="size" placeholder="Size"><br>
    <textarea class="input" name="description" placeholder="Description"></textarea><br>
    <input class="input" type="text" required name="colors" placeholder="Colors"><br>
    <input class="input" type="file" required name="image[]" accept=".jpg,.jpeg,.png" multiple><br><br>
    <button type="submit" class="button-primary">Add Product</button>
  </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
