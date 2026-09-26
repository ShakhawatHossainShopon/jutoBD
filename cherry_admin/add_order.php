<?php
include "../config/config.php";
$title = "Add Order";

// Fetch products for dropdown
$products = $pdo->query("SELECT id, name, uid FROM products")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $quantity = $_POST['quantity'];
    $color = $_POST['color'];
    $size = $_POST['size'];
    $status = 'pending'; // default

    $stmt = $pdo->prepare("INSERT INTO orders (product_id, name, phone, city, address, quantity, color, size, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_id, $name, $phone, $city, $address, $quantity, $color, $size, $status]);

    header("Location: order_board.php");
    exit;
}

ob_start();
?>

<div class="container">
  <h2>Add Order</h2>
  <br>
  <form method="post">
      <select name="product_id" class="input" required>
          <option value="">Select Product</option>
          <?php foreach($products as $p): ?>
            <option value="<?= $p['id'] ?>"><?= $p['name'] ?> (<?= $p['uid'] ?>)</option>
          <?php endforeach; ?>
      </select>

      <input class="input" type="text" name="name" placeholder="Customer Name" required>
      <input class="input" type="text" name="phone" placeholder="Phone Number" required>
      <input class="input" type="text" name="city" placeholder="City" required>
      <textarea class="input" name="address" placeholder="Address" required></textarea>
      <input class="input" type="number" name="quantity" placeholder="Quantity" required>
      <input class="input" type="text" name="color" required placeholder="Color">
      <input class="input" type="text" name="size" required placeholder="Size">
      <br><br>
      <button class="button-primary" type="submit">Create Order</button>
  </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
