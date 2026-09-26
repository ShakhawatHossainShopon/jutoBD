<?php
include "../config/config.php";
$title = "Completed Orders";

// Get filter values
$search_phone = $_GET['phone'] ?? '';
$search_supplier = $_GET['supplier'] ?? '';

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build SQL with filters (only completed orders)
$where = ["o.status = 'completed'"];
$params = [];

if ($search_phone) {
    $where[] = "o.phone LIKE ?";
    $params[] = "%$search_phone%";
}
if ($search_supplier) {
    $where[] = "p.supplier_id = ?";
    $params[] = $search_supplier;
}

$whereSQL = "WHERE " . implode(" AND ", $where);

// Fetch orders with product and supplier info
$sql = "SELECT o.*, p.name AS product_name, s.name AS supplier_name
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN suppliers s ON p.supplier_id = s.id
        $whereSQL
        ORDER BY o.created_at DESC
        LIMIT $offset, $limit";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch suppliers for filter dropdown
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Total rows & pages
$totalSQL = "SELECT COUNT(*) FROM orders o
             JOIN products p ON o.product_id = p.id
             WHERE o.status = 'completed'";
$stmtTotal = $pdo->prepare($totalSQL);
$stmtTotal->execute();
$total = $stmtTotal->fetchColumn();
$pages = ceil($total / $limit);

ob_start();
?>

<div class="container">
  <h2>Completed Orders</h2>

  <!-- Filters -->
  <form method="get" style="margin-bottom:1rem;">
    <input type="text" name="phone" placeholder="Search Phone" value="<?= htmlspecialchars($search_phone) ?>" class="input">

    <select name="supplier" class="input">
      <option value="">All Suppliers</option>
      <?php foreach($suppliers as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $search_supplier == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="button-primary">Filter</button>
  </form>

  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Product</th>
      <th>Supplier</th>
      <th>Buyer Name</th>
      <th>Phone</th>
      <th>Quantity</th>
      <th>Created At</th>
      <th>Status</th>
      <th>View</th>
    </tr>

    <?php if($orders): ?>
      <?php foreach($orders as $o): ?>
      <tr>
        <td><?= $o['id'] ?></td>
        <td><a href="product_details.php?id=<?= $o['product_id'] ?>"><?= htmlspecialchars($o['product_name']) ?></a></td>
        <td><?= htmlspecialchars($o['supplier_name']) ?></td>
        <td><?= htmlspecialchars($o['name']) ?></td>
        <td><?= htmlspecialchars($o['phone']) ?></td>
        <td><?= $o['quantity'] ?></td>
        <td><?= date("h:i A, d-m-Y", strtotime($o['created_at'])) ?></td>
        <td><?= ucfirst($o['status']) ?></td>
        <td><a href="order_details.php?id=<?= $o['id'] ?>">View</a></td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="9" style="text-align:center;color:red;">No orders found</td></tr>
    <?php endif; ?>
  </table>

  <!-- Pagination -->
  <div style="margin-top:1rem;">
    <?php for($p = 1; $p <= $pages; $p++): ?>
      <?php if($p == $page): ?>
        <strong><?= $p ?></strong>
      <?php else: ?>
        <a href="?page=<?= $p ?>&phone=<?= urlencode($search_phone) ?>&supplier=<?= $search_supplier ?>"><?= $p ?></a>
      <?php endif; ?>
      &nbsp;
    <?php endfor; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
