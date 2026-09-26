<?php
include "../config/config.php";
$title = "Supplier Orders";

$supplier_id = $_GET['id'] ?? 0;
if(!$supplier_id) {
    die("Supplier ID missing.");
}

// Fetch supplier info
$stmt = $pdo->prepare("SELECT name FROM suppliers WHERE id=?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$supplier) die("Supplier not found.");

// Fetch all completed orders by this supplier
$sql = "SELECT o.*, p.name AS product_name, p.wholesale, p.price
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.status='completed' AND p.supplier_id = ?
        ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$supplier_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container">
  <h2>Completed Orders - Supplier: <?= htmlspecialchars($supplier['name']) ?></h2>

  <table border="1" cellpadding="5" cellspacing="0" style="width:100%; margin-top:1rem;">
    <tr style="background:#f0f0f0;">
      <th>Order ID</th>
      <th>Product</th>
      <th>Quantity</th>
      <th>Wholesale (TK)</th>
      <th>Price (TK)</th>
      <th>Profit (TK)</th>
      <th>Buyer Name</th>
      <th>Phone</th>
      <th>Created At</th>
    </tr>

    <?php if($orders): ?>
      <?php foreach($orders as $o): ?>
      <tr>
        <td><?= $o['id'] ?></td>
        <td><?= htmlspecialchars($o['product_name']) ?></td>
        <td><?= $o['quantity'] ?></td>
        <td><?= number_format($o['wholesale'] * $o['quantity'], 2) ?></td>
        <td><?= number_format($o['price'] * $o['quantity'], 2) ?></td>
        <td><?= number_format(($o['price'] - $o['wholesale']) * $o['quantity'], 2) ?></td>
        <td><?= htmlspecialchars($o['name']) ?></td>
        <td><?= htmlspecialchars($o['phone']) ?></td>
        <td><?= date("h:i A, d-m-Y", strtotime($o['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="9" style="text-align:center;color:red;">No completed orders for this supplier.</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
