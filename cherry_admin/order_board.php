<?php
include "../config/config.php";
$title = "Order Board";

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Count total pending/processing orders
$totalSql = "SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'processing')";
$totalOrders = $pdo->query($totalSql)->fetchColumn();
$totalPages = ceil($totalOrders / $perPage);

// Fetch orders for current page (with price and wholesale)
$sql = "SELECT o.*,
               p.name AS product_name,
               p.uid AS product_uid,
               p.price AS price,
               p.wholesale AS wholesale
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.status IN ('pending', 'processing')
        ORDER BY o.created_at DESC
        LIMIT :offset, :perPage";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container">
  <h2>Order Board</h2>
  <br>
  <table>
    <tr>
      <th>ID</th>
      <th>Product</th>
      <th>UID</th>
      <th>Buyer Name</th>
      <th>Phone</th>
      <th>Quantity</th>
      <th>Total Wholesale</th>
      <th>Total Price</th>
      <th>Profit</th>
      <th>Created At</th>
      <th>Status</th>
      <th>Action</th>
      <th>View</th>
    </tr>
    <?php if($orders): ?>
      <?php foreach($orders as $o):
        $totalWholesale = $o['wholesale'] * $o['quantity'];
        $totalPrice = $o['price'] * $o['quantity'];
        $profit = $totalPrice - $totalWholesale;
      ?>
      <tr>
        <td><?= $o['id'] ?></td>
        <td>
          <a style="color:green;font-weight:600" href="product_details.php?id=<?= $o['product_id'] ?>">
            <?= htmlspecialchars($o['product_name']) ?>
          </a>
        </td>
        <td><?= $o['product_uid'] ?></td>
        <td><?= htmlspecialchars($o['name']) ?></td>
        <td><?= htmlspecialchars($o['phone']) ?></td>
        <td><?= $o['quantity'] ?></td>
        <td><?= number_format($totalWholesale, 2) ?> TK</td>
        <td><?= number_format($totalPrice, 2) ?> TK</td>
        <td><?= number_format($profit, 2) ?> TK</td>
        <td><?= date("h:i A, d-m-Y", strtotime($o['created_at'])) ?></td>
        <td><?= ucfirst($o['status']) ?></td>
        <td style="display:flex; align-items:center; gap:0.5rem;">
          <form method="post" action="update_order_status.php" style="display:flex;align-items:center; gap:0.5rem;">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <select name="status" class="input">
              <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Pending</option>
              <option value="processing" <?= $o['status']=='processing'?'selected':'' ?>>Processing</option>
              <option value="completed" <?= $o['status']=='completed'?'selected':'' ?>>Completed</option>
            </select>
            <button type="submit" style="padding:4px 8px;border:none; background-color:#65b741;color:white; border-radius:4px;cursor:pointer;">Update</button>
          </form>

          <!-- Delete button -->
          <form method="post" action="delete_order.php" onsubmit="return confirm('Are you sure you want to delete this order?');">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <button type="submit" style="padding:4px 8px;border:none; background-color:#e53935;color:white; border-radius:4px;cursor:pointer;">Delete</button>
          </form>
        </td>
        <td><a href="order_details.php?id=<?= $o['id'] ?>">View</a></td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="13" style="text-align:center;color:red;">No orders found</td></tr>
    <?php endif; ?>
  </table>

  <!-- Pagination Links -->
  <div style="margin-top:15px;">
    <?php if($totalPages > 1): ?>
      <?php for($i = 1; $i <= $totalPages; $i++): ?>
        <?php if($i == $page): ?>
          <strong><?= $i ?></strong>
        <?php else: ?>
          <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
        &nbsp;
      <?php endfor; ?>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
