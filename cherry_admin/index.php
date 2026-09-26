<?php
include "../config/config.php";
$title = "Account Page";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch total completed orders
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetchColumn();

// Fetch total wholesale (sum of wholesale prices of completed orders)
$totalWholesaleSql = "SELECT SUM(p.wholesale * o.quantity) AS total_wholesale
                      FROM orders o
                      JOIN products p ON o.product_id = p.id
                      WHERE o.status='completed'";
$totalWholesale = $pdo->query($totalWholesaleSql)->fetchColumn();

// Fetch total sold (sum of selling price of completed orders)
$totalSoldSql = "SELECT SUM(p.price * o.quantity) AS total_sold
                 FROM orders o
                 JOIN products p ON o.product_id = p.id
                 WHERE o.status='completed'";
$totalSold = $pdo->query($totalSoldSql)->fetchColumn();

// Calculate profit
$profit = $totalSold - $totalWholesale;

// Fetch pending orders count
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'processing')")->fetchColumn();

ob_start();
?>

<div class="container">
  <h2>Account Overview</h2>

  <div style="display:flex; gap:20px; flex-wrap:wrap;">
    <!-- Total Orders Card -->
    <div style="flex:1 1 200px; padding:20px; background:#f0f0f0; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
      <h3>Total Orders</h3>
      <p style="font-size:24px; font-weight:bold; color:#2e7d32;"><?= $totalOrders ?></p>
    </div>
    <div style="flex:1 1 200px; padding:20px; background:#e8eaf6; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
      <h3>Pending Orders</h3>
      <p style="font-size:24px; font-weight:bold; color:#3f51b5;"><?= $pendingOrders ?></p>
    </div>
    <!-- Total Wholesale Card -->
    <div style="flex:1 1 200px; padding:20px; background:#e0f7fa; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
      <h3>Total Wholesale</h3>
      <p style="font-size:24px; font-weight:bold; color:#00796b;"><?= number_format($totalWholesale, 2) ?> TK</p>
    </div>

    <!-- Total Sold Card -->
    <div style="flex:1 1 200px; padding:20px; background:#fff3e0; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
      <h3>Total Sold</h3>
      <p style="font-size:24px; font-weight:bold; color:#ef6c00;"><?= number_format($totalSold, 2) ?> TK</p>
    </div>

    <!-- Profit Card -->
    <div style="flex:1 1 200px; padding:20px; background:#fce4ec; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
      <h3>Profit</h3>
      <p style="font-size:24px; font-weight:bold; color:#c2185b;"><?= number_format($profit, 2) ?> TK</p>
    </div>

    <!-- Pending Orders Card -->

  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
