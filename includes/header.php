<?php
$stmt = $pdo->query("SELECT id, title,image FROM categories ORDER BY title ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(isset($_GET['order']) && $_GET['order'] === 'processing'){
    $_SESSION['show_order_message'] = true;
}

// Display message once
if(isset($_SESSION['show_order_message']) && $_SESSION['show_order_message']){
    echo '<div style="padding:8px; background:#FFF3CD; color:#856404; border-radius:8px; margin-bottom:15px;font-size:14px">
            Your order is processing!
          </div>';
    $_SESSION['show_order_message'] = false; // reset
}

?>
<header>
  <div class="upper-header">
    <p style="font-size:12px;color:#666666">Welcome to worldwide Zellomarket!</p>
    <div>
      <p style="font-size:12px;color:#666666">
        <i class="fa-solid fa-phone" style="color:#008ECC;"></i> +880 1642956206 &nbsp;&nbsp;
        <i class="fa-solid fa-envelope" style="color:#008ECC;"></i> info@Zellomarket.com &nbsp;&nbsp;
        <i class="fa-solid fa-location-dot" style="color:#008ECC;"></i> Dhaka, Bangladesh
      </p>
    </div>
  </div>

<div class="middle-nav">
  <div  style="display:flex; align-items:center;gap:10px;">
    <i id="hamburger" class="fa-solid fa-bars" style="font-size:20px; color:#008ECC; cursor:pointer;"></i>
      <a href="index.php" style="width:150px">
      <img src="./includes/logo/logo.png"
        alt="logo"
         />
      </a>

  </div>

  <div class="nav-search" style="position:relative; width:100%;">
      <form action="search_results.php" method="get">
          <input type="text" name="q" placeholder="Search product, Catrgory, uid and more..." required
                 style="width:100%; padding:8px 40px 8px 15px; border-radius:5px; border:none; outline:none;background-color:#F3F9FB;font-size:12px">
          <button type="submit" style="position:absolute; right:15px; top:50%; transform:translateY(-50%); border:none; background:none; cursor:pointer;">
              <i class="fa-solid fa-magnifying-glass" style="color:#008ECC;"></i>
          </button>
      </form>
  </div>

<div  style="display:flex; gap:1rem;">
    <a href="about-us.php" style="font-size:14px; font-weight:600;" class="nav-item">About us</a>
    <a href="faq.php" style="font-size:14px;font-weight:600;" class="nav-item">FAQ</a>
  </div>
</div>

<!-- Mobile Menu -->
<div id="mobile-menu" class="mobile-menu">
    <div class="mobile-menu-header">
        <span style="font-weight:bold; font-size:16px;">Categories</span>
        <span id="close-mobile-menu" style="cursor:pointer; font-size:32px;">&times;</span>
    </div>
    <ul style="list-style:none; padding:10px;">
        <?php foreach($categories as $cat): ?>
            <li style="padding:8px 0; border-bottom:1px solid #eee; text-transform:capitalize">
                <a href="category_page.php?title=<?= urlencode($cat['title']) ?>" style="text-decoration:none; color:#008ECC;">
                    <?= htmlspecialchars($cat['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<style>
/* Mobile Menu Styling */
.mobile-menu {
    position: fixed;
    top: 0;
    left: -250px; /* hidden by default */
    width: 250px;
    height: 100%;
    background: #fff;
    box-shadow: 2px 0 5px rgba(0,0,0,0.3);
    z-index: 9999;
    transition: left 0.3s ease;
    overflow-y: auto;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

/* Show menu when active */
.mobile-menu.active {
    left: 0;
}

/* Only show hamburger in mobile */
@media(min-width: 768px){
    #mobile-menu, #hamburger {
        display: none;
    }
}
</style>

<script>
// Open menu
document.getElementById('hamburger').addEventListener('click', function(){
    document.getElementById('mobile-menu').classList.add('active');
});

// Close menu
document.getElementById('close-mobile-menu').addEventListener('click', function(){
    document.getElementById('mobile-menu').classList.remove('active');
});
</script>

<!-- Mobile Bottom Menu -->
<div class="bottom-nav">
  <a href="index.php" class="nav-item"><i class="fas fa-home"></i></a>
  <a href="category_list.php"  class="nav-item"><i class="fa-solid fa-list"></i></a>
  <a href="mobile_search.php" class="nav-item"><i class="fa-solid fa-magnifying-glass"></i></a>
  <a href="aboutus.php" class="nav-item"><i class="fa-solid fa-people-line"></i></a>
  <a href="faq.php" class="nav-item"><i class="fa-solid fa-comment-dots"></i></a>
</div>
</header>
