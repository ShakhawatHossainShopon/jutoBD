<?php
include "./config/config.php";

$search = trim($_GET['q'] ?? '');
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$products = [];
$total_products = 0;
$total_pages = 0;

if ($search !== '') {
    // total products matching search
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE ?
           OR p.description LIKE ?
           OR p.colors LIKE ?
           OR p.size LIKE ?
           OR p.price LIKE ?
           OR c.title LIKE ?
           OR p.uid LIKE ?
    ");
    $count_stmt->execute(array_fill(0, 7, "%$search%"));
    $total_products = $count_stmt->fetchColumn();
    $total_pages = ceil($total_products / $limit);

    // fetch paginated products
    $stmt = $pdo->prepare("
        SELECT p.*, c.title AS category_title FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE ?
           OR p.description LIKE ?
           OR p.colors LIKE ?
           OR p.size LIKE ?
           OR p.price LIKE ?
           OR c.title LIKE ?
           OR p.uid LIKE ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    for ($i = 1; $i <= 7; $i++) $stmt->bindValue($i, "%$search%");
    $stmt->bindValue(8, $limit, PDO::PARAM_INT);
    $stmt->bindValue(9, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

ob_start();
?>

<div class="deals-section-container main">
  <div class="header">
      <h2 class="title">
          <span class="title-highlight">
              Search Results <?= $search !== '' ? 'for "'.htmlspecialchars($search).'"' : '' ?>
          </span>
      </h2>

      <?php if ($search === ''): ?>
          <p style="font-size:14px;color:#f57224;">Please enter a search term.</p>
      <?php elseif (count($products) > 0): ?>
          <p style="font-size:14px;color:#65b741">
              <?= $total_products ?> product<?= $total_products !== 1 ? 's' : '' ?> found
          </p>
      <?php else: ?>
          <p style="color:#f57224;">No products found for "<?= htmlspecialchars($search) ?>"</p>
      <?php endif; ?>
  </div>

  <div id="product-container" class="product-carousel">
      <?php foreach ($products as $prod): ?>
      <a href="product_details.php?id=<?= urlencode($prod['id']) ?>" class="product-card">
          <div class="product-image-container">
            <?php
            $images = json_decode($prod['images'], true);
            $first_image = $images[0] ?? 'https://placehold.co/250x250';
            ?>
              <img src="<?= htmlspecialchars($first_image) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
          </div>
          <div class="product-info">
              <p class="product-name"><?= htmlspecialchars($prod['name']) ?></p>
              <div class="price-row">
                  <span class="current-price"><?= htmlspecialchars($prod['price']) ?> BDT</span>
                  <span class="old-price"><?= htmlspecialchars($prod['uid']) ?></span>
              </div>
              <p class="save-text"><?= htmlspecialchars($prod['category_title'] ?? '') ?></p>
          </div>
      </a>
      <?php endforeach; ?>
  </div>

  <?php if ($total_pages > 1 && $search !== ''): ?>
  <div class="pagination" style="margin-top:20px;text-align:center;">
      <?php if ($page > 1): ?>
          <a href="?q=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">&laquo; Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"
             style="<?= $i == $page ? 'font-weight:bold;color:#65b741;' : '' ?>">
             <?= $i ?>
          </a>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
          <a href="?q=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
      <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
