  <?php
  include "./config/config.php";

  // Get product ID from query string
  $product_id = $_GET['id'] ?? 0;

  // Fetch product details from DB
  $stmt = $pdo->prepare("SELECT p.*, c.title AS category_title FROM products p
                         LEFT JOIN categories c ON p.category_id = c.id
                         WHERE p.id = ?");
  $stmt->execute([$product_id]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$product) {
      die("Product not found");
  }

  // Decode images JSON
  $images = json_decode($product['images'], true);
  $main_image = $images[0] ?? 'https://placehold.co/400x400?text=No+Image';

  $colors = $product['colors'] ? explode(',', $product['colors']) : [];
  $sizes = $product['size'] ? explode(',', $product['size']) : [];

  ob_start();
  ?>

  <main class="pdp-container">
      <div class="pdp-grid">

          <!-- 1. Image Gallery -->
          <div class="image-column">
              <div class="pdp-main-image-area">
                  <img id="main-product-image"
                       src="<?= htmlspecialchars($main_image) ?>"
                       alt="<?= htmlspecialchars($product['name']) ?>">
              </div>

              <div id="thumbnail-gallery" class="pdp-thumbnail-gallery">
                  <?php foreach ($images as $index => $img): ?>
                      <img src="<?= htmlspecialchars($img) ?>"
                           data-image-url="<?= htmlspecialchars($img) ?>"
                           alt="Thumbnail <?= $index+1 ?>"
                           class="pdp-thumbnail <?= $index === 0 ? 'pdp-thumbnail-active' : '' ?>">
                  <?php endforeach; ?>
              </div>
          </div>

          <!-- 2. Product Details & Buy Box -->
          <div class="details-column">
              <h1 id="product-title"><?= htmlspecialchars($product['name']) ?></h1>

              <div class="pdp-rating-price">
                  <span style="background-color:#D1ECF1; color:#0C5460; font-weight:bold; padding:4px 8px; border-radius:5px; font-size:12px;">
                      In Stock
                  </span>
              </div>



              <p class="pdp-price"><?= htmlspecialchars($product['price']) ?> BDT</p>

              <!-- Variants: Color as text -->
              <?php if ($colors): ?>
                  <div class="pdp-variant-group">
                      <p>Color: <span style="font-weight: bold;">
                          <?= htmlspecialchars(implode(', ', $colors)) ?>
                      </span></p>
                  </div>
              <?php endif; ?>

              <!-- Variants: Size as text -->
              <?php if ($sizes): ?>
                  <div class="pdp-variant-group">
                      <p>Size: <span style="font-weight: bold;">
                          <?= htmlspecialchars(implode(', ', $sizes)) ?>
                      </span></p>
                  </div>
              <?php endif; ?>

              <div class="pdp-qty-cta-row">
                <div class="pdp-quantity-alternative" style="
                background-color:#D4EDDA;
                color:#155724;
                font-weight:bold;
                padding:6px 12px;
                border-radius:6px;
                font-size:14px;
                border: 1px solid #C3E6CB;">
                    Available now
                </div>

              <a href="add_order.php?id=<?= $product['id'] ?>" id="add-to-cart-btn">
              <i class="fa-solid fa-cart-shopping" style="margin-right: 8px;"></i>
              Order Now
              </a>

              </div>

              <div class="pdp-trust-points">
                  <div class="pdp-trust-item"><strong>Shipping:</strong>Fast Delivery.</div>
                  <div class="pdp-trust-item"><strong>Returns:</strong> Returns Guaranted.</div>
              </div>
          </div>
      </div>

      <!-- 3. Product Description -->
      <div class="pdp-description-section">
          <h2>Product Details</h2>
          <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px;"><?= htmlspecialchars($product['description']) ?></p>

          <ul class="pdp-features-list">
              <?php if (!empty($product['features'])): ?>
                  <?php $features = explode(',', $product['features']); ?>
                  <?php foreach ($features as $f): ?>
                      <li><?= htmlspecialchars($f) ?></li>
                  <?php endforeach; ?>
              <?php endif; ?>
          </ul>
      </div>
  </main>

  <?php
  $content = ob_get_clean();
  include 'layout.php';
  ?>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const thumbnails = document.querySelectorAll('.pdp-thumbnail');
      const mainImage = document.getElementById('main-product-image');

      thumbnails.forEach(thumbnail => {
          thumbnail.addEventListener('click', function() {
              // Change main image src
              mainImage.src = this.dataset.imageUrl;

              // Update active thumbnail class
              thumbnails.forEach(t => t.classList.remove('pdp-thumbnail-active'));
              this.classList.add('pdp-thumbnail-active');
          });
      });
  });
  </script>
