<?php
// Fetch all categories
$allCategories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Limit to 5 categories
$threeDayPeriod = floor(time() / (3 * 24 * 60 * 60)); // 3 days in seconds
srand($threeDayPeriod); // seed random number generator
shuffle($allCategories);
srand(); // reset seed to default

// Limit to 8 categories
$categories = array_slice($allCategories, 0, 15);
?>
    <?php foreach($categories as $cat): ?>
    <?php
    // Fetch latest 4 posts for this category
    $stmt = $pdo->prepare("
        SELECT * FROM products
        WHERE category_id=(SELECT id FROM categories WHERE title=?)
        ORDER BY created_at DESC
        LIMIT 6
    ");
    $stmt->execute([$cat['title']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Skip if no posts
    if(!$products) continue;
    ?>
    <div class="deals-section-container">

        <!-- Header Section -->
        <div class="header">
            <h2 class="title">
                <span class="title-highlight">
                  <?= htmlspecialchars($cat['title']) ?>
                </span>
            </h2>
            <a href="category_page.php?title=<?= urlencode($cat['title']) ?>" class="view-all-link">
                View All
                <!-- Simple Right Arrow SVG for the "View All" link -->
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </div>

        <!-- Product Grid Container -->
        <div id="product-container" class="product-carousel">
            <?php foreach($products as $prod): ?>
            <a href="product_details.php?id=<?= urlencode($prod['id']) ?>" class="product-card">
              <div class="product-image-container">
                <?php
                $images = json_decode($prod['images'], true);
                $first_image = $images[0] ?? 'https://placehold.co/250x250';
                 ?>
                  <img src="<?= htmlspecialchars($first_image) ?>"  alt="<?= htmlspecialchars($prod['name']) ?>">
              </div>
                <div class="product-info">
                    <p class="product-name"><?= $prod['name'] ?></p>
                    <div class="price-row">
                        <span class="current-price"><?= $prod['price'] ?> BDT</span>
                        <span class="old-price"><?= $prod['uid'] ?></span>
                    </div>
                    <p class="save-text"><?= htmlspecialchars($cat['title']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
