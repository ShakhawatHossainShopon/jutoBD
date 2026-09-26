<?php
require_once __DIR__ . '/config/config.php';

try {
    // Fetch latest 20 products only
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.images, p.created_at, p.colors, p.size, c.title as category_name,
               COALESCE(SUM(o.quantity), 0) as total_sold
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN orders o ON p.id = o.product_id
        GROUP BY p.id
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
    <title>Discounted Items | JUTO</title>
    
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
        <section class="w-full relative bg-[#dfd5c5] lg:h-[600px] flex items-center overflow-hidden" data-aos="fade">
            <!-- Full Width Background Image for Desktop -->
            <img src="assets/discount_banner.png" class="hidden lg:block absolute inset-0 w-full h-full object-cover object-right" alt="Discounted Items">
            
            <!-- Gradient Overlay -->
            <div class="hidden lg:block absolute inset-0 z-0" style="background: linear-gradient(90deg, #F3EFE5 46.93%, rgba(243, 239, 229, 0) 60.31%); pointer-events: none;"></div>
            
            <!-- Content Wrapper -->
            <div class="relative z-10 w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12 py-16 lg:py-0 flex flex-col lg:flex-row items-center justify-between">
                
                <!-- Left Text Container -->
                <div class="w-full lg:w-1/2 lg:pr-10 mb-12 lg:mb-0">
                    <span class="block text-[11px] font-bold tracking-[0.3em] text-[#4d3c31] uppercase mb-4" id="hero-subtitle">SPECIAL OFFERS</span>
                    <h1 class="font-serif text-[48px] lg:text-[56px] leading-[1.1] text-[#4d3c31] uppercase tracking-[0.05em] mb-6">PREMIUM LEATHER.</h1>
                    <p class="font-sans text-[#71685f] text-[14px] leading-relaxed border-l-[2px] border-[#4d3c31] pl-5 max-w-[500px] mb-8 bg-white/40 lg:bg-transparent p-4 lg:p-0 rounded-lg lg:rounded-none backdrop-blur-sm lg:backdrop-blur-none">
                        Discover selected JUTO leather goods at exclusive discounted prices. Explore timeless footwear and everyday essentials crafted with quality materials, thoughtful design, and the character you expect from JUTO.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-[#4a362a] text-white px-6 py-3.5 text-[10px] sm:text-[11px] tracking-[0.15em] font-bold flex items-center gap-2 hover:bg-[#38281e] transition-colors">
                            SHOP DISCOUNTS &rarr;
                        </button>
                        <button class="border border-[#4a362a] text-[#4a362a] px-6 py-3.5 text-[10px] sm:text-[11px] tracking-[0.15em] font-bold flex items-center gap-2 hover:bg-[#4a362a] hover:text-white transition-colors">
                            EXPLORE ALL PRODUCTS &rarr;
                        </button>
                    </div>
                </div>

                <!-- Right Timer Card -->
                <div class="w-full lg:w-[45%] flex lg:justify-end items-center">
                    <div class="bg-white p-6 sm:p-8 shadow-2xl max-w-[480px] w-full">
                        <h3 class="text-center font-serif text-[24px] text-[#4a362a] uppercase tracking-wide mb-6 font-normal">SALE ENDS IN</h3>
                        <div class="bg-[#f5f3ef] p-4 sm:p-6 flex justify-between gap-2 sm:gap-4 border border-[#e5e0d4]">
                            
                            <!-- Day -->
                            <div class="flex flex-col items-center">
                                <div class="relative bg-[#4a362a] rounded-[2px] w-[55px] sm:w-[70px] h-[55px] sm:h-[65px] flex items-center justify-center mb-2 overflow-hidden shadow-md">
                                    <span class="text-white font-sans font-light text-[28px] sm:text-[34px] relative z-10 leading-none" id="cd-days">01</span>
                                    <!-- Horizontal line for flip clock effect -->
                                    <div class="absolute inset-x-0 top-1/2 h-[2px] bg-[#3a2a20] z-20 shadow-[0_1px_0_rgba(255,255,255,0.1)]"></div>
                                    <!-- tiny circles on the sides of the line -->
                                    <div class="absolute left-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                    <div class="absolute right-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                </div>
                                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-[#71685f]">DAYS</span>
                            </div>
                            
                            <!-- Hour -->
                            <div class="flex flex-col items-center">
                                <div class="relative bg-[#4a362a] rounded-[2px] w-[55px] sm:w-[70px] h-[55px] sm:h-[65px] flex items-center justify-center mb-2 overflow-hidden shadow-md">
                                    <span class="text-white font-sans font-light text-[28px] sm:text-[34px] relative z-10 leading-none" id="cd-hours">08</span>
                                    <div class="absolute inset-x-0 top-1/2 h-[2px] bg-[#3a2a20] z-20 shadow-[0_1px_0_rgba(255,255,255,0.1)]"></div>
                                    <div class="absolute left-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                    <div class="absolute right-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                </div>
                                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-[#71685f]">HOURS</span>
                            </div>
                            
                            <!-- Min -->
                            <div class="flex flex-col items-center">
                                <div class="relative bg-[#4a362a] rounded-[2px] w-[55px] sm:w-[70px] h-[55px] sm:h-[65px] flex items-center justify-center mb-2 overflow-hidden shadow-md">
                                    <span class="text-white font-sans font-light text-[28px] sm:text-[34px] relative z-10 leading-none" id="cd-mins">36</span>
                                    <div class="absolute inset-x-0 top-1/2 h-[2px] bg-[#3a2a20] z-20 shadow-[0_1px_0_rgba(255,255,255,0.1)]"></div>
                                    <div class="absolute left-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                    <div class="absolute right-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                </div>
                                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-[#71685f]">MINUTES</span>
                            </div>
                            
                            <!-- Sec -->
                            <div class="flex flex-col items-center">
                                <div class="relative bg-[#4a362a] rounded-[2px] w-[55px] sm:w-[70px] h-[55px] sm:h-[65px] flex items-center justify-center mb-2 overflow-hidden shadow-md">
                                    <span class="text-white font-sans font-light text-[28px] sm:text-[34px] relative z-10 leading-none" id="cd-secs">01</span>
                                    <div class="absolute inset-x-0 top-1/2 h-[2px] bg-[#3a2a20] z-20 shadow-[0_1px_0_rgba(255,255,255,0.1)]"></div>
                                    <div class="absolute left-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                    <div class="absolute right-[-2px] top-[calc(50%-2px)] w-1.5 h-1.5 rounded-full bg-[#f5f3ef] z-30"></div>
                                </div>
                                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-[#71685f]">SECONDS</span>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
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
                        SPECIAL OFFERS, JUTO STYLE.
                    </h2>
                </div>

                <!-- Product Grid Area (Full Width, No Sidebar) -->
                <div class="w-full">
                    <div class="mb-8 flex justify-between items-center text-[#71685f] text-[13px] font-sans border-b border-[#f4f2ec] pb-4">
                        <span id="product-count">Showing <?= count($all_products) ?> discounted products</span>
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
                                
                                // Discount Math
                                $discount_pct = 30; // 30% discount
                                $old_price = $product['price'];
                                $save_amount = $old_price * ($discount_pct / 100);
                                $new_price = $old_price - $save_amount;
                                
                                $old_price_fmt = "$" . number_format($old_price, 2);
                                $new_price_fmt = "$" . number_format($new_price, 2);
                                $save_fmt = "$" . floor($save_amount);
                                
                                // Dynamic stats
                                $bought = (int)$product['total_sold'];
                                if($bought < 3) $bought = rand(5, 24); // Fake it if too low for the "last 24 hrs" effect
                                $left = rand(4, 18); // Fake stock left
                            ?>
                                <div class="group flex flex-col bg-white">
                                     
                                    <!-- Image Box -->
                                    <div class="relative w-full aspect-[4/5] bg-[#f8f8f8] overflow-hidden flex items-center justify-center">
                                        
                                        <!-- Top Left Tags -->
                                        <div class="absolute top-3 left-3 flex font-sans text-[9px] sm:text-[10px] font-medium tracking-wide z-10 shadow-sm">
                                            <div class="bg-[#4d3c31] text-white px-2 py-1"><?= $discount_pct ?>% DISCOUNT</div>
                                            <div class="bg-white text-[#4d3c31] px-2 py-1">SAVE <?= $save_fmt ?></div>
                                        </div>

                                        <!-- Top Right Tag (Speech Bubble) -->
                                        <div class="absolute top-3 right-3 bg-white text-[#4d3c31] text-[10px] sm:text-[11px] font-medium px-2 py-1 shadow-sm z-10">
                                            <?= $left ?> Left
                                            <!-- Little triangle pointing down -->
                                            <div class="absolute -bottom-[5px] left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[5px] border-r-[5px] border-t-[5px] border-l-transparent border-r-transparent border-t-white"></div>
                                        </div>

                                        <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover mix-blend-multiply">
                                    </div>
                                    
                                    <!-- Text Content -->
                                    <div class="pt-3 flex flex-col flex-grow bg-white">
                                        <div class="flex justify-between items-start mb-1 px-1">
                                            <h3 class="font-serif text-[#111] text-[16px] sm:text-[19px] font-medium truncate pr-2"><?= htmlspecialchars($product['name']) ?></h3>
                                            <div class="flex items-center text-[#ffa41c] text-[13px] sm:text-[15px] shrink-0 mt-0.5">
                                                <i class="fa-solid fa-star"></i><span class="text-[#111] ml-1 font-bold text-[12px] sm:text-[14px]">4.9</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 mb-3 px-1 mt-1">
                                            <span class="font-sans text-[#111] text-[14px] sm:text-[16px] font-bold"><?= $new_price_fmt ?></span>
                                            <span class="font-sans text-[#999] text-[12px] sm:text-[14px] line-through"><?= $old_price_fmt ?></span>
                                        </div>
                                        
                                        <!-- Megaphone Banner -->
                                        <div class="w-full bg-[#f4f2ec] text-[#4d3c31] text-[11px] sm:text-[12px] py-2 flex justify-center items-center gap-2 mt-auto">
                                            <i class="fa-solid fa-bullhorn text-[11px]"></i> <?= $bought ?> people bought... last 24 hrs
                                        </div>
                                    </div>
                                </div>
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

        // Real-time Countdown Timer Logic
        document.addEventListener('DOMContentLoaded', () => {
            // Set end date to 3 days from now for demo purposes
            const countDownDate = new Date();
            countDownDate.setDate(countDownDate.getDate() + 3); 

            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update DOM elements, padding with zero if needed
                const elDays = document.getElementById("cd-days");
                if (elDays) {
                    elDays.innerText = days.toString().padStart(2, '0');
                    document.getElementById("cd-hours").innerText = hours.toString().padStart(2, '0');
                    document.getElementById("cd-mins").innerText = minutes.toString().padStart(2, '0');
                    document.getElementById("cd-secs").innerText = seconds.toString().padStart(2, '0');
                }

                // Stop if expired
                if (distance < 0) {
                    clearInterval(timer);
                    if (elDays) {
                        elDays.innerText = "00";
                        document.getElementById("cd-hours").innerText = "00";
                        document.getElementById("cd-mins").innerText = "00";
                        document.getElementById("cd-secs").innerText = "00";
                    }
                }
            }, 1000);
        });
    </script>
</body>
</html>
