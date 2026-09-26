<style>
/* Pagination Container */
.pagination a {
    display: inline-block;
    margin: 0px 5px;
    padding: 6px 12px;
    font-size: 12px;
    color: #1D4ED8; /* Blue links */
    text-decoration: none;
    border: 1px solid #ddd;
    border-radius: 6px;
    transition: all 0.2s ease;
}

/* Current Page */
.pagination a[style*="font-weight:bold"] {
    background: #1D4ED8;
    color: #fff !important;
    border-color: #1D4ED8;
}

/* Hover Effect */
.pagination a:hover {
    background: #2563EB;
    color: #fff;
    border-color: #2563EB;
}

/* Disabled / Invisible */
.pagination a.disabled {
    pointer-events: none;
    opacity: 0.5;
}

/* Pagination Container Centering */
.pagination {
    display: flex;
    justify-content: end;
    align-items: center;
    flex-wrap: wrap;
    gap: 0px;
    padding-bottom: 40px;
}
</style>

<?php
include "./config/config.php";
$title = 'Browse By Category';
$category_title = $_GET['title'] ?? '';

$limit = 6; // products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// total count
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) FROM products
    WHERE category_id = (SELECT id FROM categories WHERE title = ?)
");
$count_stmt->execute([$category_title]);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// fetch paginated products
$stmt = $pdo->prepare("
    SELECT * FROM products
    WHERE category_id = (SELECT id FROM categories WHERE title = ?)
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $category_title);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$product_count = count($products);

ob_start();
?>
<div class="deals-section-container main">
  <div class="header">
      <h2 class="title">
          <span class="title-highlight"><?= htmlspecialchars($category_title) ?></span>
      </h2>
      <?php if ($product_count > 0): ?>
          <p style="font-size:14px;color:#65b741">
              <?= $total_products ?> product<?= $total_products !== 1 ? 's' : '' ?> found
          </p>
      <?php else: ?>
          <p class="product-count-info">No products found in this category.</p>
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
              <img src="<?= htmlspecialchars($first_image) ?>"  alt="<?= htmlspecialchars($prod['name']) ?>">
          </div>
          <div class="product-info">
              <p class="product-name"><?= htmlspecialchars($prod['name']) ?></p>
              <div class="price-row">
                  <span class="current-price"><?= htmlspecialchars($prod['price']) ?> BDT</span>
                  <span class="old-price"><?= htmlspecialchars($prod['uid']) ?></span>
              </div>
              <p class="save-text"><?= htmlspecialchars($category_title) ?></p>
          </div>
      </a>
      <?php endforeach; ?>
  </div>

  <?php if ($total_pages > 1): ?>
  <div class="pagination" style="margin-top:20px;text-align:center;">
      <?php if ($page > 1): ?>
          <a href="?title=<?= urlencode($category_title) ?>&page=<?= $page - 1 ?>">&laquo; Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?title=<?= urlencode($category_title) ?>&page=<?= $i ?>"
             style="<?= $i == $page ? 'font-weight:bold;color:#65b741;' : '' ?>">
             <?= $i ?>
          </a>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
          <a href="?title=<?= urlencode($category_title) ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
      <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
<div class="mm-footer-bottom">
     &copy; 2025 All rights reserved. <a href="#" class="mm-copyright-link">Zellomarket</a>
 </div>
<?php

$content = ob_get_clean();
include 'layout.php';
?>
