<?php
require_once __DIR__ . '/config/config.php';

try {
    // Fetch latest 20 products only
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.images, p.created_at, p.colors, p.size, c.title as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Arrivals | JUTO</title>
    
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
            <!-- Left Text Container aligned to 1400px max-width grid -->
            <div class="w-full lg:w-[50%] flex justify-end">
                <div class="w-full max-w-[700px] px-4 sm:px-6 lg:pl-12 lg:pr-16 py-16 lg:py-0 z-10" data-aos="fade">
                    <span class="block text-[10px] font-bold tracking-[0.3em] text-[#8b8277] uppercase mb-4" id="hero-subtitle">THE NEW ARRIVALS</span>
                    <h1 class="font-serif text-[42px] lg:text-[52px] leading-[1.1] text-[#4d3c31] uppercase tracking-[0.05em] mb-6">LATEST RELEASES, REFINED CRAFTSMANSHIP.</h1>
                    <p class="font-sans text-[#71685f] text-[15px] max-w-md border-l-[3px] border-[#4d3c31] pl-5 leading-relaxed">
                        Discover our newest handcrafted leather goods featuring a classic, streamlined look. Meticulously crafted from premium full-grain leather, offering unmatched elegance.
                    </p>
                </div>
            </div>
            
            <!-- Hide image on mobile -->
            <div class="hidden lg:block w-full lg:w-[50%] h-[350px] lg:h-[550px] relative" data-aos="fade">
                <img src="assets/category_banner.png" class="w-full h-full object-cover" alt="New Arrivals">
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
                    <h2 class="font-serif text-[32px] lg:text-[40px] text-[#4d3c31] uppercase tracking-[0.05em] font-normal leading-[1.2]">
                        LATEST ARRIVALS, JUTO STYLE.
                    </h2>
                </div>

                <!-- Product Grid Area (Full Width, No Sidebar) -->
                <div class="w-full">
                    <div class="mb-8 flex justify-between items-center text-[#71685f] text-[13px] font-sans border-b border-[#f4f2ec] pb-4">
                        <span id="product-count">Showing <?= count($all_products) ?> latest products</span>
                        <div class="flex items-center gap-2 cursor-pointer hover:text-[#4d3c31] transition-colors">
                            SORT BY: NEWEST <i class="fa-solid fa-angle-down ml-1 text-[10px]"></i>
                        </div>
                    </div>

                    <!-- 4 Columns on Desktop because there's no sidebar -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-10 sm:gap-x-6 sm:gap-y-12">
                        <?php if(empty($all_products)): ?>
                            <div class="col-span-full text-center py-20 text-[#71685f]">
                                No products found.
                            </div>
                        <?php else: ?>
                            <?php foreach($all_products as $index => $product): 
                                $images = json_decode($product['images'], true);
                                $img_url = (is_array($images) && count($images) > 0) ? $images[0] : 'https://placehold.co/400x500/f8f8f8/cccccc?text=No+Image';
                                $img_url = str_replace('\\/', '/', $img_url);
                                $price_formatted = "৳" . number_format($product['price'], 2);
                                
                                $p_cat = $product['category_name'] ?? '';
                                $p_cols = $product['colors'] ?? '';
                            ?>
                                <a href="product.php?id=<?= $product['id'] ?>" class="product-card block group cursor-pointer flex flex-col bg-white border border-[#f0f0f0] p-3 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.06)] transition-all duration-300">
                                     
                                    <!-- Image Box -->
                                    <div class="relative w-full aspect-[4/5] bg-[#f8f8f8] mb-4 overflow-hidden flex items-center justify-center">
                                        
                                        <!-- Tags -->
                                        <div class="absolute top-2 left-2 flex gap-1 z-10">
                                            <span class="bg-white text-[#4d3c31] text-[8px] sm:text-[9px] font-bold tracking-widest uppercase px-2 py-1 shadow-sm border border-[#f0f0f0]">NEW IN</span>
                                        </div>

                                        <button onclick="event.preventDefault();" class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 bg-white rounded-full flex items-center justify-center text-[#71685f] hover:text-[#4d3c31] transition-all duration-300 shadow-sm z-10">
                                            <i class="fa-regular fa-heart text-[12px] sm:text-[14px]"></i>
                                        </button>

                                        <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 mix-blend-multiply">
                                        
                                        <!-- Hover Cart overlay -->
                                        <div class="absolute inset-0 bg-black/5 opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-6 z-10 pointer-events-none">
                                            <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?= $product['id'] ?>);" class="bg-[#4d3c31]/90 lg:bg-[#4d3c31] text-white px-8 py-3 text-[10px] font-bold tracking-[0.15em] flex items-center shadow-lg pointer-events-auto transform translate-y-4 lg:group-hover:translate-y-0 transition-transform duration-300">
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
                                        </div></div></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <?php include 'includes/sections/cta_banner.php'; ?>

    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
</body>
</html>








