<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/config.php';
}
try {
    $stmt = $pdo->prepare("SELECT id, title, image FROM categories ORDER BY title ASC");
    $stmt->execute();
    $nav_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $nav_categories = [];
}

// Prepare random featured category for the image card
$featured_cat = !empty($nav_categories) ? $nav_categories[array_rand($nav_categories)] : null;
$featured_img = (!empty($featured_cat) && !empty($featured_cat['image'])) ? $featured_cat['image'] : 'https://images.unsplash.com/photo-1614252339460-e1d15c7cc68e?q=80&w=400&auto=format&fit=crop';
?>
<!-- Main Navigation Bar -->
<header id="main-header" class="w-full border-b border-gray-100 bg-white/95 backdrop-blur-md sticky top-0 z-40 transition-all duration-300">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12">
        <div id="nav-container" class="flex justify-between items-center h-[90px] transition-all duration-500 ease-in-out">
            
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="index.php" class="flex flex-col items-center">
                    <div class="flex items-end">
                        <span id="logo-text" class="text-[40px] font-serif text-slate-800 tracking-wider font-bold transition-all duration-500 ease-in-out leading-none">JUTO</span>
                    </div>
                    <div id="logo-sub-container" class="overflow-hidden transition-all duration-500 ease-in-out h-[15px] mt-1 opacity-100">
                        <span class="block text-[9px] text-slate-500 tracking-[0.25em] uppercase leading-none">Leather Goods</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center space-x-12 mt-1">
                <a href="index.php" class="text-slate-900 hover:text-juto-brown text-[13px] tracking-[0.15em] font-medium transition-colors hover:scale-105 transform duration-300">HOME</a>
                
                <div class="relative group cursor-pointer flex items-center h-full">
                    <div class="flex items-center gap-1.5 text-slate-900 hover:text-[#4d3c31] text-[13px] tracking-[0.15em] font-medium transition-colors hover:scale-105 transform duration-300 py-4">
                        CATEGORY 
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 mt-0.5 transition-transform duration-300 group-hover:-scale-y-100">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    
                    <!-- Desktop Mega Menu Dropdown Wrapper (pt-4 acts as invisible bridge to maintain hover) -->
                    <div class="absolute top-full pt-2 left-1/2 -translate-x-1/2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <!-- Mega Menu Box -->
                        <div class="w-[850px] lg:w-[950px] bg-[#fefdfc] shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 p-10 cursor-default border border-gray-100 flex gap-8 text-left">
                            
                            <!-- Dynamic Categories Grid (Takes up space of 3 columns) -->
                            <div class="flex-[3]">
                                <h3 class="font-serif text-[#4d3c31] text-[15px] uppercase tracking-widest mb-6 font-semibold border-b border-gray-100 pb-3">SHOP BY CATEGORY</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-2 text-[14.5px] text-[#71685f] font-sans">
                                    <?php if(empty($nav_categories)): ?>
                                        <p>No categories found.</p>
                                    <?php else: ?>
                                        <?php foreach($nav_categories as $cat): ?>
                                            <a href="category.php?name=<?= urlencode($cat['title']) ?>" class="py-2.5 hover:text-[#4d3c31] transition-colors flex justify-between items-center group/link">
                                                <?= htmlspecialchars($cat['title']) ?> 
                                                <span class="opacity-0 group-hover/link:opacity-100 transition-opacity font-light text-[18px] leading-none text-[#d4af37]">&rarr;</span>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Column 4 (Featured Image Card) -->
                            <?php if($featured_cat): ?>
                            <a href="category.php?name=<?= urlencode($featured_cat['title']) ?>" class="w-[280px] shrink-0 bg-[#f8f8f8] p-4 flex flex-col group/card transition-colors hover:bg-[#f0ece3]">
                                <div class="bg-[#f0f0f0] w-full aspect-square mb-5 overflow-hidden flex items-center justify-center">
                                    <img src="<?= htmlspecialchars($featured_img) ?>" alt="<?= htmlspecialchars($featured_cat['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover/card:scale-110 mix-blend-multiply">
                                </div>
                                <h4 class="font-serif text-[#4d3c31] text-[13px] tracking-widest uppercase font-semibold mb-1.5">FEATURED</h4>
                                <p class="font-serif text-[#71685f] text-[14px]"><?= htmlspecialchars($featured_cat['title']) ?></p>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <a href="discount.php" class="text-slate-900 hover:text-juto-brown text-[13px] tracking-[0.15em] font-medium transition-colors hover:scale-105 transform duration-300">DISCOUNT</a>
                
                <a href="best-seller.php" class="text-slate-900 hover:text-juto-brown text-[13px] tracking-[0.15em] font-medium transition-colors hover:scale-105 transform duration-300">BEST SELLER</a>
            </nav>

            <!-- Icons (Search & Cart & Hamburger) -->
            <div class="flex items-center space-x-4 sm:space-x-6 mt-1">
                <!-- Search Icon -->
                <button class="text-slate-800 hover:text-[#4d3c31] transition-all transform hover:scale-110 active:scale-95 duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </button>
                
                <!-- Cart Icon with Badge (Triggers Drawer) -->
                <button type="button" onclick="toggleCartDrawer()" class="relative text-slate-800 hover:text-[#4d3c31] transition-all transform hover:scale-110 active:scale-95 duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[24px] h-[24px]">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <span id="nav-cart-count" class="absolute -top-1.5 -right-2.5 bg-[#4d3c31] text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm transition-transform duration-300 group-hover:scale-110">
                        0
                    </span>
                </button>

                <!-- Hamburger Icon (Mobile Only) -->
                <button type="button" onclick="toggleMobileMenu()" class="md:hidden text-slate-800 hover:text-[#4d3c31] transition-all transform hover:scale-110 active:scale-95 duration-300 flex items-center ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[26px] h-[26px]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Slide-Out Mobile Menu Drawer -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-[60] opacity-0 invisible transition-all duration-500 backdrop-blur-sm md:hidden" onclick="toggleMobileMenu()"></div>

<div id="mobile-menu-drawer" class="fixed top-0 left-0 h-full w-[85%] max-w-[350px] bg-[#fefdfc] z-[60] transform -translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.25,1,0.5,1)] shadow-2xl flex flex-col md:hidden">
    
    <!-- Drawer Header -->
    <div class="px-6 py-6 border-b border-[#f4f2ec] flex justify-between items-center bg-white">
        <span class="font-serif text-[26px] text-[#4d3c31] tracking-wider font-bold leading-none">JUTO</span>
        <button onclick="toggleMobileMenu()" class="text-slate-400 hover:text-[#4d3c31] transition-colors transform hover:rotate-90 duration-300 flex items-center justify-center w-8 h-8 rounded-full bg-[#f8f8f8]">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="flex-grow overflow-y-auto px-6 py-8 flex flex-col gap-6">
        <a href="index.php" class="font-serif text-[17px] text-[#4d3c31] tracking-[0.2em] uppercase font-semibold border-b border-[#f4f2ec] pb-4">HOME</a>
        
        <!-- Mobile Category Accordion -->
        <div class="border-b border-[#f4f2ec] pb-4">
            <button onclick="toggleMobileCategory()" class="w-full flex justify-between items-center font-serif text-[17px] text-[#4d3c31] tracking-[0.2em] uppercase font-semibold">
                CATEGORY
                <i id="mobile-category-icon" class="fa-solid fa-angle-down text-[14px] transition-transform duration-300"></i>
            </button>
            
            <div id="mobile-category-content" class="hidden flex-col gap-6 pt-6 pl-2">
                <div>
                    <h4 class="font-sans text-[11px] font-bold text-[#a3998f] uppercase tracking-widest mb-4">SHOP BY CATEGORY</h4>
                    <div class="flex flex-col gap-4 font-serif text-[18px] text-[#71685f]">
                        <?php if(!empty($nav_categories)): ?>
                            <?php foreach($nav_categories as $cat): ?>
                                <a href="category.php?name=<?= urlencode($cat['title']) ?>" class="active:text-[#4d3c31] hover:text-[#4d3c31] transition-colors"><?= htmlspecialchars($cat['title']) ?></a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No categories found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="discount.php" class="font-serif text-[17px] text-[#4d3c31] tracking-[0.2em] uppercase font-semibold border-b border-[#f4f2ec] pb-4">DISCOUNT</a>
        <a href="best-seller.php" class="font-serif text-[17px] text-[#4d3c31] tracking-[0.2em] uppercase font-semibold border-b border-[#f4f2ec] pb-4">BEST SELLER</a>
    </div>
</div>

<!-- Slide-Out Cart Drawer -->
<div id="cart-drawer-overlay" class="fixed inset-0 bg-black/50 z-[60] opacity-0 invisible transition-all duration-500 backdrop-blur-sm" onclick="toggleCartDrawer()"></div>

<div id="cart-drawer" class="fixed top-0 right-0 h-full w-full sm:w-[420px] bg-white z-[60] transform translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.25,1,0.5,1)] shadow-2xl flex flex-col">
    
    <!-- Drawer Header -->
    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-[#f4f2eb]/40">
        <h3 class="font-serif text-xl text-slate-900 tracking-wide font-semibold">YOUR CART <span class="text-slate-500 text-sm ml-1 font-sans">(0 Items)</span></h3>
        <button onclick="toggleCartDrawer()" class="text-slate-400 hover:text-red-500 transition-colors transform hover:rotate-90 duration-300 flex items-center justify-center w-8 h-8 rounded-full hover:bg-white">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <div id="cart-drawer-body" class="flex-grow flex flex-col relative overflow-y-auto bg-[#fefdfc]"></div></div><script>
    // 1. Shrinking Navbar on Scroll
    window.addEventListener('scroll', () => {
        const header = document.getElementById('main-header');
        const navContainer = document.getElementById('nav-container');
        const logoText = document.getElementById('logo-text');
        const logoSubContainer = document.getElementById('logo-sub-container');
        
        if (window.scrollY > 50) {
            // Shrink Mode
            navContainer.classList.remove('h-[90px]');
            navContainer.classList.add('h-[65px]');
            
            logoText.classList.remove('text-[40px]');
            logoText.classList.add('text-[28px]');
            
            logoSubContainer.classList.remove('h-[15px]', 'opacity-100');
            logoSubContainer.classList.add('h-0', 'opacity-0');
            
            header.classList.add('shadow-md');
        } else {
            // Expanded Mode
            navContainer.classList.remove('h-[65px]');
            navContainer.classList.add('h-[90px]');
            
            logoText.classList.remove('text-[28px]');
            logoText.classList.add('text-[40px]');
            
            logoSubContainer.classList.remove('h-0', 'opacity-0');
            logoSubContainer.classList.add('h-[15px]', 'opacity-100');
            
            header.classList.remove('shadow-md');
        }
    });

    // 2. Slide-Out Cart Logic
    function toggleCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        
        if (drawer.classList.contains('translate-x-full')) {
            // Open Drawer
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('opacity-0', 'invisible');
            overlay.classList.add('opacity-100', 'visible');
            document.body.style.overflow = 'hidden'; // Lock background scroll
        } else {
            // Close Drawer
            drawer.classList.add('translate-x-full');
            overlay.classList.remove('opacity-100', 'visible');
            overlay.classList.add('opacity-0', 'invisible');
            document.body.style.overflow = ''; // Restore scroll
        }
    }

    // 3. Mobile Menu Logic
    function toggleMobileMenu() {
        const drawer = document.getElementById('mobile-menu-drawer');
        const overlay = document.getElementById('mobile-menu-overlay');
        
        if (drawer.classList.contains('-translate-x-full')) {
            // Open Menu
            drawer.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'invisible');
            overlay.classList.add('opacity-100', 'visible');
            document.body.style.overflow = 'hidden'; // Lock background scroll
        } else {
            // Close Menu
            drawer.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100', 'visible');
            overlay.classList.add('opacity-0', 'invisible');
            document.body.style.overflow = ''; // Restore scroll
        }
    }

    // 4. Mobile Category Accordion Logic
    function toggleMobileCategory() {
        const content = document.getElementById('mobile-category-content');
        const icon = document.getElementById('mobile-category-icon');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            content.classList.add('flex');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            content.classList.remove('flex');
            icon.classList.remove('rotate-180');
        }
    }
</script>

