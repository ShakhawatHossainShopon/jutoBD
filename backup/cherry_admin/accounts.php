<?php
include "../config/config.php";
$title = "Supplier Report";

// Fetch report data grouped by supplier
$sql = "SELECT s.id AS supplier_id, s.name AS supplier_name,
               COUNT(o.id) AS total_orders,
               SUM(p.wholesale * o.quantity) AS total_wholesale,
               SUM(p.price * o.quantity) AS total_sold,
               SUM((p.price - p.wholesale) * o.quantity) AS profit
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN suppliers s ON p.supplier_id = s.id
        WHERE o.status='completed'
        GROUP BY s.id
        ORDER BY s.name ASC";
$report = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container">
  <h2>Supplier Report</h2>

  <table border="1" cellpadding="5" cellspacing="0" style="width:100%; margin-top:1rem;">
    <tr style="background:#f0f0f0;">
      <th>Supplier</th>
      <th>Total Orders</th>
      <th>Total Wholesale (TK)</th>
      <th>Total Sold (TK)</th>
      <th>Profit (TK)</th>
      <th>Action</th>
    </tr>

    <?php if($report): ?>
      <?php foreach($report as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['supplier_name']) ?></td>
        <td><?= $r['total_orders'] ?></td>
        <td><?= number_format($r['total_wholesale'], 2) ?></td>
        <td><?= number_format($r['total_sold'], 2) ?></td>
        <td><?= number_format($r['profit'], 2) ?></td>
        <td>
          <a href="supplier_orders.php?id=<?= $r['supplier_id'] ?>" style="color:blue;font-weight:600;">View</a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6" style="text-align:center;color:red;">No completed orders found</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
