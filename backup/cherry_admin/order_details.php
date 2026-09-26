<?php
include "../config/config.php";
$title = "Order Details";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid Order ID");

// Fetch order with product info
$stmt = $pdo->prepare("
    SELECT o.*, p.name AS product_name, p.uid AS product_uid
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) die("Order not found");

ob_start();
?>

<div class="container">
  <h2>Order Details</h2>
  <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
  <p><strong>Product:</strong> <?= $order['product_name'] ?> (UID: <?= $order['product_uid'] ?>)</p>
  <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['name']) ?></p>
  <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
  <p><strong>City:</strong> <?= htmlspecialchars($order['city']) ?></p>
  <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
  <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
  <p><strong>Color:</strong> <?= htmlspecialchars($order['color']) ?></p>
  <p><strong>Size:</strong> <?= htmlspecialchars($order['size']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
  <p><strong>Created At:</strong> <?= date("h:i A, d-m-Y", strtotime($order['created_at'])) ?></p>

  <br>
  <a href="order_board.php" class="button-primary">Back to Orders</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
