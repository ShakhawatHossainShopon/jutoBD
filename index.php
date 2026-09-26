<?php
include "./config/config.php";
ob_start();
$page_title = "Zellomarket - Home";
$stmt = $pdo->query("SELECT id, title,image FROM categories ORDER BY title ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="category-slider-main-div">

  <button id="prev-nav" class="category-arrow-btn">&#10094;</button>
  <button id="next-nav" class="category-arrow-btn">&#10095;</button>

  <!-- outer wrapper hides scrollbar -->
  <div class="category-slider-div">
    <div id="slider-wrapper-nav" style="display:flex; gap:10px; overflow-x:scroll; scroll-behavior:smooth; -ms-overflow-style:none; scrollbar-width:none;">
      <a class="category-btn active">All <i class="fa-solid fa-chevron-down"></i></a>
      <?php foreach ($categories as $cat): ?>
        <a href="category_page.php?title=<?= urlencode($cat['title']) ?>" class="category-btn">
          <?= htmlspecialchars($cat['title']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
const slider = document.getElementById('slider-wrapper-nav'); // scrollable element
document.getElementById('prev-nav').onclick = () => { slider.scrollBy({ left: -150, behavior: 'smooth' }); };
document.getElementById('next-nav').onclick = () => { slider.scrollBy({ left: 150, behavior: 'smooth' }); };
</script>




<div class="main">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
            <?php
            $banners = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach($banners as $banner):
            ?>
            <div class="swiper-slide">
                <img src="<?= $banner['image'] ?>" alt="<?= htmlspecialchars($banner['title'] ?? '') ?>">
            </div>
            <?php endforeach; ?>
        </div>
    <!-- Pagination -->
    <div class="swiper-pagination"></div>

    <!-- Navigation buttons -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>
  <?php include "./includes/category_section.php" ?>
  <?php include "./includes/card_section.php" ?>
</div>
    <?php include "./includes/footer.php" ?>
<?php
$content = ob_get_clean();
include 'layout.php';
?>
