<?php
include "../config/config.php";
$title = "Product Details";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid Product ID");

// Fetch product with category and supplier
$stmt = $pdo->prepare("
    SELECT p.*, c.title AS category_name, s.name AS supplier_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN suppliers s ON p.supplier_id = s.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) die("Product not found");

ob_start();
?>

<div class="container">
  <h2>Product Details</h2>
  <p><strong>UID:</strong> <?= $product['uid'] ?></p>
  <p><strong>Name:</strong> <?= $product['name'] ?></p>
  <p><strong>Category:</strong> <?= $product['category_name'] ?></p>
  <p><strong>Supplier:</strong> <?= $product['supplier_name'] ?></p>
  <p><strong>Price:</strong> <?= $product['price'] ?> TK</p>
  <p><strong>Wholesale:</strong> <?= $product['wholesale'] ?> TK</p>
  <p><strong>Size:</strong> <?= $product['size'] ?></p>
  <p><strong>Colors:</strong> <?= $product['colors'] ?></p>
  <p><strong>Profit:</strong> <?= $product['profit'] ?> TK</p>

  <?php if (!empty($product['images'])):
      $images = json_decode($product['images'], true);
  ?>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <?php foreach ($images as $img): ?>
              <img src="../<?= $img ?>" width="100">
          <?php endforeach; ?>
      </div>
  <?php endif; ?>

  <br>
  <a href="products.php" class="button-primary">Back to Products</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
