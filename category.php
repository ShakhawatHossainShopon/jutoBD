<?php
require_once __DIR__ . '/config/config.php';

$currentCategory = isset($_GET['name']) ? $_GET['name'] : 'Oxford'; // Default for the design

try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.images, p.created_at, p.colors, p.size, c.title as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_products = [];
}

// Extract unique filters from real data
$real_categories = [];
$real_colors = [];
$real_sizes = [];

foreach($all_products as $p) {
    if(!empty($p['category_name'])) {
        $real_categories[$p['category_name']] = true;
    }
    if(!empty($p['colors'])) {
        $cols = explode(',', $p['colors']);
        foreach($cols as $c) {
            $c = trim($c);
            if(strlen($c) > 0 && strlen($c) < 25) { 
                $real_colors[ucfirst(strtolower($c))] = true;
            }
        }
    }
    if(!empty($p['size'])) {
        $szs = preg_split('/[,.]+/', $p['size']); 
        foreach($szs as $s) {
            $s = trim(strtoupper($s));
            if(strlen($s) > 0 && strlen($s) < 6) { 
                $real_sizes[$s] = true;
            }
        }
    }
}

$real_categories = array_keys($real_categories);
$real_colors = array_keys($real_colors);
$real_sizes = array_keys($real_sizes);
sort($real_categories);
sort($real_colors);

usort($real_sizes, function($a, $b) {
    if(is_numeric($a) && is_numeric($b)) return $a - $b;
    if(is_numeric($a)) return -1;
    if(is_numeric($b)) return 1;
    return strcmp($a, $b);
});

function getApproxHex($colorName) {
    $c = strtolower($colorName);
    if(strpos($c, 'black') !== false || strpos($c, 'balck') !== false) return '#1a1a1a';
    if(strpos($c, 'white') !== false) return '#ffffff';
    if(strpos($c, 'red') !== false || strpos($c, 'maroon') !== false) return '#8b0000';
    if(strpos($c, 'blue') !== false || strpos($c, 'navi') !== false) return '#000080';
    if(strpos($c, 'green') !== false || strpos($c, 'olive') !== false || strpos($c, 'teal') !== false) return '#556b2f';
    if(strpos($c, 'yellow') !== false) return '#ffd700';
    if(strpos($c, 'pink') !== false) return '#ffc0cb';
    if(strpos($c, 'brown') !== false || strpos($c, 'chocklate') !== false) return '#8b4513';
    if(strpos($c, 'gray') !== false || strpos($c, 'grey') !== false || strpos($c, 'silver') !== false) return '#808080';
    if(strpos($c, 'cream') !== false || strpos($c, 'biscuit') !== false) return '#f5f5dc';
    if(strpos($c, 'orange') !== false) return '#ffa500';
    if(strpos($c, 'paste') !== false) return '#afeeee';
    return '#cccccc'; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($currentCategory) ?> | JUTO</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['"Cormorant Garamond"', 'serif'],
                        sans: ['"Nunito Sans"', 'sans-serif'],
                    },
                    colors: {
                        'juto-brown': '#4a362a',
                        'juto-light': '#f4f2eb',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#fefdfc] selection:bg-[#4d3c31] selection:text-[#f4f2ec] flex flex-col min-h-screen">

    <?php include 'includes/header.php'; ?>

    <main class="flex-grow ">
        
        <!-- Hero Banner Section -->
        <section class="w-full bg-[#f4f2ec] relative overflow-hidden flex flex-col lg:flex-row items-center">
            <div class="w-full lg:w-[50%] flex justify-end">
                <div class="w-full max-w-[700px] px-4 sm:px-6 lg:pl-12 lg:pr-16 py-16 lg:py-0 z-10" data-aos="fade">
                    <span class="block text-[10px] font-bold tracking-[0.3em] text-[#8b8277] uppercase mb-4" id="hero-subtitle">THE <?= htmlspecialchars(strtoupper($currentCategory)) ?> COLLECTION</span>
                    <h1 class="font-serif text-[42px] lg:text-[52px] leading-[1.1] text-[#4d3c31] uppercase tracking-[0.05em] mb-6">TIMELESS FORM, REFINED CRAFTSMANSHIP.</h1>
                    <p class="font-sans text-[#71685f] text-[15px] max-w-md border-l-[3px] border-[#4d3c31] pl-5 leading-relaxed">
                        Made for the modern gentleman, our handcrafted leather goods feature a classic, streamlined look. Meticulously handcrafted from premium full-grain leather, they offer unmatched elegance for formal events or everyday wear.
                    </p>
                </div>
            </div>
            
            <div class="hidden lg:block w-full lg:w-[50%] h-[350px] lg:h-[550px] relative" data-aos="fade">
                <img src="assets/category_banner.png" class="w-full h-full object-cover" alt="Oxford Collection">
                <!-- Soft fade gradient on the left side to blend image with beige bg on desktop -->
                <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-[#f4f2ec] to-transparent hidden lg:block pointer-events-none"></div>
            </div>
        </section>

        <!-- Features Strip -->
        <div class="w-full bg-[#eeeae0] py-6 border-b border-[#e5e0d4]">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-[#4d3c31] text-[11px] font-bold tracking-[0.1em] uppercase">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-truck-fast text-[20px]"></i> 
                    <div>FREE SHIPPING<br><span class="text-[9px] text-[#71685f] font-normal tracking-normal normal-case">On all orders over $200</span></div>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-shield-halved text-[20px]"></i> 
                    <div>SECURE PAYMENT<br><span class="text-[9px] text-[#71685f] font-normal tracking-normal normal-case">100% secure checkout</span></div>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-headset text-[20px]"></i> 
                    <div>24/7 SUPPORT<br><span class="text-[9px] text-[#71685f] font-normal tracking-normal normal-case">Dedicated assistance</span></div>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-award text-[20px]"></i> 
                    <div>100% AUTHENTIC<br><span class="text-[9px] text-[#71685f] font-normal tracking-normal normal-case">Guaranteed quality</span></div>
                </div>
            </div>
        </div>

        <section class="w-full bg-white py-16 lg:py-24">
            <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-12">
                
                <!-- Section Title -->
                <div class="text-center mb-16" data-aos="fade-up">
                    <h2 class="font-serif text-[32px] lg:text-[40px] text-[#4d3c31] uppercase tracking-[0.05em] font-normal leading-[1.2]" id="section-title">
                        CLASSIC <?= htmlspecialchars(strtoupper($currentCategory)) ?> SHOES, JUTO STYLE.
                    </h2>
                </div>

                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
                    
                    <!-- Filter Sidebar -->
                    <aside class="w-full lg:w-[260px] shrink-0 bg-[#f6f5f0] p-6 lg:p-8 lg:sticky lg:top-[110px] max-h-none lg:max-h-[85vh] overflow-y-auto custom-scrollbar border border-[#e5e0d4]">
                        <style>
                            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                            .custom-scrollbar::-webkit-scrollbar-thumb { background: #dcd6cb; border-radius: 4px; }
                        </style>
                        <h3 class="font-serif text-[#4d3c31] text-[15px] tracking-[0.2em] uppercase border-b border-[#e2dcd0] pb-4 mb-6">FILTERS</h3>
                        
                        <!-- CATEGORY -->
                        <?php if(!empty($real_categories)): ?>
                        <div class="mb-8">
                            <h4 class="font-serif text-[#71685f] text-[12px] tracking-widest uppercase mb-4">CATEGORY</h4>
                            <div class="flex flex-col gap-1 font-sans text-[13px] text-[#71685f]" id="filter-category">
                                <?php foreach($real_categories as $cat): ?>
                                <label class="flex items-center cursor-pointer py-2 px-3 -mx-3 transition-colors filter-label hover:bg-[#e8e4db]" data-value="<?= htmlspecialchars($cat) ?>">
                                    <input type="checkbox" class="hidden filter-checkbox" value="<?= htmlspecialchars($cat) ?>">
                                    <div class="w-3.5 h-3.5 border border-[#b3ad9f] flex items-center justify-center mr-3 filter-box transition-colors">
                                        <i class="fa-solid fa-check text-[9px] text-white opacity-0 transition-opacity"></i>
                                    </div>
                                    <?= htmlspecialchars($cat) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- COLOR -->
                        <?php if(!empty($real_colors)): ?>
                        <div class="mb-8">
                            <h4 class="font-serif text-[#71685f] text-[12px] tracking-widest uppercase mb-4">COLOR</h4>
                            <div class="flex flex-col gap-3 font-sans text-[13px] text-[#71685f]" id="filter-color">
                                <?php foreach($real_colors as $colorName): ?>
                                <label class="flex items-center cursor-pointer group" data-value="<?= htmlspecialchars($colorName) ?>">
                                    <input type="checkbox" class="hidden filter-checkbox color-checkbox" value="<?= htmlspecialchars($colorName) ?>">
                                    <div class="w-4 h-4 mr-3 color-box transition-all group-hover:scale-110" style="background-color: <?= getApproxHex($colorName) ?>;"></div>
                                    <span class="truncate"><?= htmlspecialchars($colorName) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- SIZE -->
                        <?php if(!empty($real_sizes)): ?>
                        <div class="mb-8">
                            <h4 class="font-serif text-[#71685f] text-[12px] tracking-widest uppercase mb-4">SIZE</h4>
                            <div class="flex flex-wrap gap-2" id="filter-size">
                                <?php foreach($real_sizes as $sz): ?>
                                <button class="w-10 h-10 bg-white border border-[#e2dcd0] text-[#71685f] text-[12px] hover:border-[#4d3c31] transition-colors filter-size-btn" data-value="<?= htmlspecialchars($sz) ?>"><?= htmlspecialchars($sz) ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <button id="clear-filters" class="w-full bg-[#4d3c31] text-white py-4 text-[11px] font-bold tracking-[0.15em] flex justify-center items-center hover:bg-[#382b22] transition-colors mt-10 shadow-md">
                            CLEAR FILTER <i class="fa-solid fa-xmark ml-2 text-[13px]"></i>
                        </button>
                    </aside>

                    <!-- Product Grid Area -->
                    <div class="flex-grow">
                        <div class="mb-8 flex justify-between items-center text-[#71685f] text-[13px] font-sans border-b border-[#f4f2ec] pb-4">
                            <span id="product-count">Showing products</span>
                            <div class="flex items-center gap-2 cursor-pointer hover:text-[#4d3c31] transition-colors">
                                SORT BY: NEWEST <i class="fa-solid fa-angle-down ml-1 text-[10px]"></i>
                            </div>
                        </div>

                        <!-- Skeleton Loader -->
                        <div id="skeleton-grid" class="hidden grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-10 sm:gap-x-6 sm:gap-y-12">
                            <?php for($i=0; $i<6; $i++): ?>
                            <div class="flex flex-col bg-white border border-[#f0f0f0] p-3 animate-pulse shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                                <div class="w-full aspect-[4/5] bg-gray-200/60 mb-4"></div>
                                <div class="px-1 flex flex-col flex-grow">
                                    <div class="h-3 sm:h-4 bg-gray-200/80 w-3/4 mb-2"></div>
                                    <div class="h-2 sm:h-2.5 bg-gray-200/60 w-1/2 mb-4"></div>
                                    <div class="h-3 sm:h-4 bg-gray-200/80 w-1/4 mt-auto mb-3"></div>
                                    <div class="w-full bg-gray-100 h-5 sm:h-6 mt-auto"></div>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-10 sm:gap-x-6 sm:gap-y-12" id="product-grid">
                            <?php if(empty($all_products)): ?>
                                <div class="col-span-full text-center py-20 text-[#71685f]">
                                    No products found.
                                </div>
                            <?php else: ?>
                                <?php foreach($all_products as $index => $product): 
                                    $images = json_decode($product['images'], true);
                                    $img_url = (is_array($images) && count($images) > 0) ? $images[0] : 'https://placehold.co/400x500/f8f8f8/cccccc?text=No+Image';
                                    $img_url = str_replace('\\/', '/', $img_url);
                                    $price_formatted = "$" . number_format($product['price'], 2);
                                    
                                    $p_cat = $product['category_name'] ?? '';
                                    $p_cols = $product['colors'] ?? '';
                                    $p_szs = $product['size'] ?? '';
                                ?>
                                    <div class="product-card group cursor-pointer flex flex-col bg-white border border-[#f0f0f0] p-3 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.06)] transition-all duration-300" 
                                         data-category="<?= htmlspecialchars($p_cat) ?>" 
                                         data-colors="<?= htmlspecialchars(strtolower($p_cols)) ?>" 
                                         data-sizes="<?= htmlspecialchars(strtoupper($p_szs)) ?>">
                                         
                                        <!-- Image Box -->
                                        <div class="relative w-full aspect-[4/5] bg-[#f8f8f8] mb-4 overflow-hidden flex items-center justify-center">
                                            
                                            <!-- Tags -->
                                            <div class="absolute top-2 left-2 flex gap-1 z-10">
                                                <?php if($index % 3 == 0): ?>
                                                <span class="bg-[#4d3c31] text-white text-[8px] sm:text-[9px] font-bold tracking-widest uppercase px-2 py-1 shadow-sm">BESTSELLER</span>
                                                <?php endif; ?>
                                                <?php if($index % 2 == 0): ?>
                                                <span class="bg-white text-[#4d3c31] text-[8px] sm:text-[9px] font-bold tracking-widest uppercase px-2 py-1 shadow-sm border border-[#f0f0f0]">NEW IN</span>
                                                <?php endif; ?>
                                            </div>

                                            <button class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 bg-white rounded-full flex items-center justify-center text-[#71685f] hover:text-[#4d3c31] transition-all duration-300 shadow-sm z-10">
                                                <i class="fa-regular fa-heart text-[12px] sm:text-[14px]"></i>
                                            </button>

                                            <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 mix-blend-multiply">
                                            
                                            <!-- Hover Cart overlay -->
                                            <div class="absolute inset-0 bg-black/5 opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-6 z-10 pointer-events-none">
                                                <button class="bg-[#4d3c31]/90 lg:bg-[#4d3c31] text-white px-8 py-3 text-[10px] font-bold tracking-[0.15em] flex items-center shadow-lg pointer-events-auto transform translate-y-4 lg:group-hover:translate-y-0 transition-transform duration-300">
                                                    ADD TO CART <span class="ml-2">&rarr;</span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Text Content -->
                                        <div class="px-1 flex flex-col flex-grow">
                                            <div class="flex justify-between items-start mb-1">
                                                <h3 class="font-serif text-[#4d3c31] text-[14px] sm:text-[16px] font-bold truncate pr-2 leading-tight"><?= htmlspecialchars($product['name']) ?></h3>
                                                <div class="flex items-center text-[#d4af37] text-[10px] sm:text-[11px] shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-star"></i><span class="text-[#4d3c31] ml-1 font-bold">4.9</span>
                                                </div>
                                            </div>
                                            <p class="font-sans text-[#71685f] text-[10px] sm:text-[11px] mb-2 truncate">
                                                <?= !empty($p_cat) ? htmlspecialchars($p_cat) : 'Premium' ?> &bull; <?= htmlspecialchars(ucfirst(explode(',', $p_cols)[0] ?? 'Leather')) ?>
                                            </p>
                                            <div class="font-sans text-[#4d3c31] text-[12px] sm:text-[14px] font-bold mb-3 mt-auto"><?= $price_formatted ?></div>
                                            
                                            <!-- Promo Tag -->
                                            <div class="w-full bg-[#f6f5f0] text-[#71685f] text-[9px] sm:text-[10px] py-1.5 flex justify-center items-center gap-1 border border-[#e5e0d4] mt-auto">
                                                <i class="fa-solid fa-tag text-[8px]"></i> 32% off running this week
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <?php include 'includes/sections/cta_banner.php'; ?>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });

        document.addEventListener('DOMContentLoaded', () => {
            const products = document.querySelectorAll('.product-card');
            const productCount = document.getElementById('product-count');
            const heroSubtitle = document.getElementById('hero-subtitle');
            const sectionTitle = document.getElementById('section-title');
            
            let activeCategories = new Set();
            let activeColors = new Set();
            let activeSizes = new Set();

            function updateDynamicTitles() {
                if(activeCategories.size === 1) {
                    const cat = Array.from(activeCategories)[0].toUpperCase();
                    heroSubtitle.textContent = `THE ${cat} COLLECTION`;
                    sectionTitle.textContent = `CLASSIC ${cat} SHOES, JUTO STYLE.`;
                } else if(activeCategories.size > 1) {
                    heroSubtitle.textContent = "THE CURATED COLLECTION";
                    sectionTitle.textContent = "CLASSIC SHOES, JUTO STYLE.";
                } else {
                    heroSubtitle.textContent = "THE ALL CATEGORIES COLLECTION";
                    sectionTitle.textContent = "CLASSIC SHOES, JUTO STYLE.";
                }
            }

            function updateFilters() {
                const productGrid = document.getElementById('product-grid');
                const skeletonGrid = document.getElementById('skeleton-grid');
                
                // Show Skeleton, Hide Grid
                productGrid.style.display = 'none';
                skeletonGrid.style.display = 'grid';
                
                setTimeout(() => {
                    let visibleCount = 0;
                    products.forEach(p => {
                        const pCat = p.dataset.category || "";
                        const pCols = p.dataset.colors || "";
                        const pSzs = p.dataset.sizes || "";

                        const matchCat = activeCategories.size === 0 || Array.from(activeCategories).some(c => pCat === c);
                        const matchCol = activeColors.size === 0 || Array.from(activeColors).some(c => pCols.includes(c.toLowerCase()));
                        const matchSz = activeSizes.size === 0 || Array.from(activeSizes).some(s => pSzs.includes(s.toUpperCase()));

                        if (matchCat && matchCol && matchSz) {
                            p.style.display = 'flex';
                            visibleCount++;
                        } else {
                            p.style.display = 'none';
                        }
                    });
                    productCount.textContent = `Showing ${visibleCount} products`;
                    updateDynamicTitles();
                    
                    // Hide Skeleton, Show Grid
                    skeletonGrid.style.display = 'none';
                    productGrid.style.display = 'grid';
                }, 400); // 400ms loading transition
            }

            const urlParams = new URLSearchParams(window.location.search);
            const initialCat = urlParams.get('name');
            if(initialCat) {
                const label = document.querySelector(`.filter-label[data-value="${initialCat}"]`);
                if(label) {
                    activeCategories.add(initialCat);
                    label.classList.add('bg-[#4d3c31]', 'text-white');
                    label.classList.remove('hover:bg-[#e8e4db]', 'text-[#71685f]');
                    const box = label.querySelector('.filter-box');
                    box.classList.add('border-white', 'bg-transparent');
                    box.classList.remove('border-[#b3ad9f]', 'bg-white');
                    label.querySelector('.fa-check').classList.remove('opacity-0');
                }
            }
            
            updateFilters();

            document.querySelectorAll('.filter-label').forEach(label => {
                label.addEventListener('click', (e) => {
                    e.preventDefault();
                    const val = label.dataset.value;
                    const box = label.querySelector('.filter-box');
                    const icon = label.querySelector('.fa-check');
                    
                    if (activeCategories.has(val)) {
                        activeCategories.delete(val);
                        label.classList.remove('bg-[#4d3c31]', 'text-white');
                        label.classList.add('hover:bg-[#e8e4db]', 'text-[#71685f]');
                        box.classList.remove('border-white', 'bg-transparent');
                        box.classList.add('border-[#b3ad9f]', 'bg-white');
                        icon.classList.add('opacity-0');
                    } else {
                        activeCategories.add(val);
                        label.classList.add('bg-[#4d3c31]', 'text-white');
                        label.classList.remove('hover:bg-[#e8e4db]', 'text-[#71685f]');
                        box.classList.add('border-white', 'bg-transparent');
                        box.classList.remove('border-[#b3ad9f]', 'bg-white');
                        icon.classList.remove('opacity-0');
                    }
                    updateFilters();
                });
            });

            document.querySelectorAll('.group[data-value]').forEach(label => {
                if(label.classList.contains('filter-label')) return; 
                label.addEventListener('click', (e) => {
                    e.preventDefault();
                    const val = label.dataset.value;
                    const box = label.querySelector('.color-box');
                    
                    if (activeColors.has(val)) {
                        activeColors.delete(val);
                        box.classList.remove('outline', 'outline-1', 'outline-offset-2', 'outline-[#b3ad9f]');
                    } else {
                        activeColors.add(val);
                        box.classList.add('outline', 'outline-1', 'outline-offset-2', 'outline-[#b3ad9f]');
                    }
                    updateFilters();
                });
            });

            document.querySelectorAll('.filter-size-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const val = btn.dataset.value;
                    
                    if (activeSizes.has(val)) {
                        activeSizes.delete(val);
                        btn.classList.remove('bg-[#4d3c31]', 'text-white', 'border-[#4d3c31]');
                        btn.classList.add('bg-white', 'text-[#71685f]', 'border-[#e2dcd0]');
                    } else {
                        activeSizes.add(val);
                        btn.classList.add('bg-[#4d3c31]', 'text-white', 'border-[#4d3c31]');
                        btn.classList.remove('bg-white', 'text-[#71685f]', 'border-[#e2dcd0]');
                    }
                    updateFilters();
                });
            });

            document.getElementById('clear-filters').addEventListener('click', () => {
                activeCategories.clear();
                activeColors.clear();
                activeSizes.clear();
                
                document.querySelectorAll('.filter-label').forEach(label => {
                    label.classList.remove('bg-[#4d3c31]', 'text-white');
                    label.classList.add('hover:bg-[#e8e4db]', 'text-[#71685f]');
                    const box = label.querySelector('.filter-box');
                    box.classList.remove('border-white', 'bg-transparent');
                    box.classList.add('border-[#b3ad9f]', 'bg-white');
                    label.querySelector('.fa-check').classList.add('opacity-0');
                });
                
                document.querySelectorAll('.color-box').forEach(box => {
                    box.classList.remove('outline', 'outline-1', 'outline-offset-2', 'outline-[#b3ad9f]');
                });
                
                document.querySelectorAll('.filter-size-btn').forEach(btn => {
                    btn.classList.remove('bg-[#4d3c31]', 'text-white', 'border-[#4d3c31]');
                    btn.classList.add('bg-white', 'text-[#71685f]', 'border-[#e2dcd0]');
                });
                
                updateFilters();
            });
        });
    </script>
</body>
</html>
