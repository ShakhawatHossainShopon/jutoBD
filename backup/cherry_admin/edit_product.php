<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid Product ID");

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) die("Product not found");

// Fetch categories & suppliers for dropdown
$categories = $pdo->query("SELECT id, title FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $price = $_POST['price'];
    $wholesale = $_POST['wholesale'];
    $size = $_POST['size'];
    $description = $_POST['description'];
    $colors = $_POST['colors'];

    // calculate profit automatically
    $profit = $price - $wholesale;

    // Update product
    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, supplier_id=?, price=?, wholesale=?, size=?, description=?, colors=?, profit=? WHERE id=?");
    $stmt->execute([$name, $category_id, $supplier_id, $price, $wholesale, $size, $description, $colors, $profit, $id]);

    header("Location: products.php");
    exit;
}

ob_start();
?>

<div class="container">
  <h2>Edit Product</h2>
  <form method="post">
      <input class="input" type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

      <select name="category_id" class="input" required>
          <?php foreach($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>><?= $c['title'] ?></option>
          <?php endforeach; ?>
      </select><br><br>

      <select name="supplier_id" class="input" required>
          <?php foreach($suppliers as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $product['supplier_id']==$s['id']?'selected':'' ?>><?= $s['name'] ?></option>
          <?php endforeach; ?>
      </select><br><br>

      <input class="input" type="number" step="0.01" name="wholesale" value="<?= $product['wholesale'] ?>" placeholder="Wholesale Price" required><br><br>
      <input class="input" type="number" step="0.01" name="price" value="<?= $product['price'] ?>" placeholder="Price" required><br><br>
      <input class="input" type="text" name="size" value="<?= $product['size'] ?>" placeholder="Size"><br><br>
      <textarea class="input" name="description" placeholder="Description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>
      <input class="input" type="text" name="colors" value="<?= $product['colors'] ?>" placeholder="Colors"><br><br>

      <button class="button-primary" type="submit">Update Product</button>
  </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
