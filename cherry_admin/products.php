<?php
include "../config/config.php";
$title = "Home Page";

// Get filter values
$filter_category = $_GET['category'] ?? '';
$filter_supplier = $_GET['supplier'] ?? '';
$search = $_GET['search'] ?? '';
$filter_uid = $_GET['uid'] ?? ''; // UID filter

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch categories and suppliers for dropdown
$categories = $pdo->query("SELECT id, title FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Build SQL with filters and search
$where = [];
$params = [];
if ($filter_category) {
    $where[] = "p.category_id = ?";
    $params[] = $filter_category;
}
if ($filter_supplier) {
    $where[] = "p.supplier_id = ?";
    $params[] = $filter_supplier;
}
if ($search) {
    $where[] = "p.name LIKE ?";
    $params[] = "%$search%";
}
if ($filter_uid) {
    $where[] = "p.uid LIKE ?";
    $params[] = "%$filter_uid%";
}
$whereSQL = $where ? "WHERE ".implode(" AND ", $where) : "";

// Fetch products
$sql = "SELECT p.*, c.title AS category_name, s.name AS supplier_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN suppliers s ON p.supplier_id = s.id
        $whereSQL
        ORDER BY p.created_at DESC
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total rows & pages
$totalSQL = "SELECT COUNT(*) FROM products p $whereSQL";
$stmtTotal = $pdo->prepare($totalSQL);
$stmtTotal->execute($params);
$total = $stmtTotal->fetchColumn();
$pages = ceil($total / $limit);
$range = 2;

// Start output buffering
ob_start();
?>

<div class="container">
  <div style="display:flex;justify-content:space-between; margin-bottom:1rem;">
    <h2>Products</h2>
    <a class="button-primary" href="add_product.php">Add Products</a>
  </div>

  <!-- Filter + Search -->
  <form method="get" style="margin-bottom:1rem;">
    <div style="display:flex;gap:1rem; align-items:center;">
      <select name="category" class="input">
        <option value="">All Categories</option>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $filter_category==$c['id']?'selected':'' ?>><?= $c['title'] ?></option>
        <?php endforeach; ?>
      </select>

      <select name="supplier" class="input">
        <option value="">All Suppliers</option>
        <?php foreach($suppliers as $s): ?>
          <option value="<?= $s['id'] ?>" <?= $filter_supplier==$s['id']?'selected':'' ?>><?= $s['name'] ?></option>
        <?php endforeach; ?>
      </select>

      <input type="text" name="search" placeholder="Search by name" class="input" value="<?= htmlspecialchars($search) ?>">
      <input type="text" name="uid" placeholder="Search by UID" class="input" value="<?= htmlspecialchars($filter_uid) ?>">

      <div>
        <button type="submit" style="padding:6px 12px;border:none; background-color:#65b741;color:white; border-radius:4px;cursor:pointer;">Filter</button>
      </div>
    </div>
  </form>

  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>id</th>
      <th>Uid</th>
      <th>images</th>
      <th>name</th>
      <th>category</th>
      <th>whole</th>
      <th>price</th>
      <th>supplier</th>
      <th>orders</th>
      <th>size</th>
      <th>colors</th>
      <th>profit</th>
      <th>action</th>
    </tr>

    <?php if(count($rows) > 0): ?>
      <?php foreach ($rows as $i => $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= $r['uid'] ?></td>
        <td>
          <?php if (!empty($r['images'])):
              $images = json_decode($r['images'], true);
              $firstImage = $images[0];
          ?>
              <a href="#" class="view-images" style="color:green;font-weight:600;" data-images='<?= $r['images'] ?>'>
                  <img src="../<?= $firstImage ?>" width="50" height="50" style="vertical-align:middle; margin-right:5px;">
              </a>
          <?php endif; ?>
        </td>
        <td>        <a href="product_details.php?id=<?= $r['id'] ?>">
            <?= htmlspecialchars($r['name']) ?>
      </a></td>
        <td><?= $r['category_name'] ?></td>
        <td ><span style="color:#FF2E2E;font-weight:600"> <?= $r['wholesale'] ?></span> TK</td>
        <td ><span style="color:#5C5CFF;font-weight:600"><?= $r['price'] ?></span> TK</td>
        <td><?= $r['supplier_name'] ?></td>
        <td style="color:#000080">
    <?php
    // Count completed orders for this product
    $stmtOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE product_id=? AND status='completed'");
    $stmtOrders->execute([$r['id']]);
    $totalOrders = $stmtOrders->fetchColumn();
    echo "(" . $totalOrders . ")";
    ?>
</td>

        <td><?= $r['size'] ?></td>
        <td><?= $r['colors'] ?></td>
        <td ><span style="color:green;font-weight:600"><?= $r['profit'] ?></span> TK</td>
        <td>
          <a href="edit_product.php?id=<?= $r['id'] ?>">Edit</a> |
          <a href="delete_product.php?id=<?= $r['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="14" style="text-align:center; color:red;">No products found</td>
      </tr>
    <?php endif; ?>
  </table>

  <!-- Pagination -->
  <div class="pagination" style="margin-top:1rem;">
    <?php if($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&category=<?= $filter_category ?>&supplier=<?= $filter_supplier ?>&search=<?= urlencode($search) ?>&uid=<?= urlencode($filter_uid) ?>" class="page-link">Prev</a>
    <?php endif; ?>

    <?php for($p = max(1, $page - $range); $p <= min($pages, $page + $range); $p++): ?>
        <a href="?page=<?= $p ?>&category=<?= $filter_category ?>&supplier=<?= $filter_supplier ?>&search=<?= urlencode($search) ?>&uid=<?= urlencode($filter_uid) ?>" class="page-link <?= $p==$page?'active':'' ?>"><?= $p ?></a>
    <?php endfor; ?>

    <?php if($page < $pages): ?>
        <a href="?page=<?= $page+1 ?>&category=<?= $filter_category ?>&supplier=<?= $filter_supplier ?>&search=<?= urlencode($search) ?>&uid=<?= urlencode($filter_uid) ?>" class="page-link">Next</a>
    <?php endif; ?>
  </div>
</div>

<!-- Image Modal -->
<div id="imageModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); justify-content:center; align-items:center;">
    <div style="background:#fff; padding:20px; position:relative; max-width:80%; max-height:80%; overflow:auto;">
        <span id="closeModal" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">&times;</span>
        <div id="modalImages" style="display:flex; flex-wrap:wrap; gap:10px;"></div>
    </div>
</div>

<script>
document.querySelectorAll('.view-images').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const images = JSON.parse(this.dataset.images);
        const container = document.getElementById('modalImages');
        container.innerHTML = '';
        images.forEach(img => {
            const image = document.createElement('img');
            image.src = '../' + img;
            image.style.width = '100px';
            image.style.height = '100px';
            container.appendChild(image);
        });
        document.getElementById('imageModal').style.display = 'flex';
    });
});

document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('imageModal').style.display = 'none';
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
