<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config/config.php';
}

try {
    $stmt = $pdo->prepare("SELECT id, name, price, images, created_at FROM products ORDER BY created_at DESC LIMIT 8");
    $stmt->execute();
    $new_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $new_products = [];
}
?>

<section class="w-full bg-white py-20 lg:py-28" data-aos="fade">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12">
        
        <!-- Heading -->
        <div class="text-center mb-16">
            <h2 class="font-serif text-[34px] lg:text-[40px] text-[#4d3c31] uppercase tracking-[0.1em] font-normal">
                NEW ARRIVAL
            </h2>
        </div>
        
        <!-- Product Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-10 sm:gap-x-6 sm:gap-y-16">
            <?php foreach($new_products as $product): 
                // Decode JSON images
                $images = json_decode($product['images'], true);
                $img_url = (is_array($images) && count($images) > 0) ? $images[0] : 'https://placehold.co/400x500/f8f8f8/cccccc?text=No+Image';
                $img_url = str_replace('\\/', '/', $img_url); // Clean slashes
                
                $price_formatted = "৳" . number_format($product['price'], 2);
            ?>
                <a href="product.php?id=<?= $product['id'] ?>" class="group cursor-pointer flex flex-col">
                    
                    <!-- Image Box -->
                    <div class="relative w-full aspect-[4/5] bg-[#f8f8f8] mb-4 sm:mb-5 overflow-hidden flex items-center justify-center">
                        <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover transition-transform duration-1000 lg:group-hover:scale-105 mix-blend-multiply">
                        
                        <!-- Wishlist Icon (Always visible on mobile, hover on desktop) -->
                        <button class="absolute top-2 right-2 sm:top-4 sm:right-4 w-7 h-7 sm:w-9 sm:h-9 bg-white rounded-full flex items-center justify-center text-[#71685f] hover:text-[#4d3c31] transition-all duration-300 shadow-sm z-10 opacity-100 translate-y-0 lg:opacity-0 lg:group-hover:opacity-100 lg:-translate-y-2 lg:group-hover:translate-y-0" title="Add to Wishlist">
                            <i class="fa-regular fa-heart text-[12px] sm:text-[15px]"></i>
                        </button>
                        
                        <!-- Add to Cart Button (Always visible on mobile, hover on desktop) -->
                        <div class="absolute bottom-3 left-3 right-3 sm:bottom-5 sm:left-5 sm:right-5 z-10 opacity-100 translate-y-0 lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-300 lg:translate-y-2 lg:group-hover:translate-y-0">
                            <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?= $product['id'] ?>);" class="w-full font-serif bg-[#4d3c31]/90 lg:bg-[#4d3c31] text-white py-2.5 sm:py-3.5 text-[9px] sm:text-[11px] font-semibold tracking-[0.1em] sm:tracking-[0.15em] transition-colors hover:bg-[#382b22] flex items-center justify-center shadow-md hover:shadow-lg">
                                <span class="hidden sm:inline">ADD TO CART</span>
                                <span class="sm:hidden">ADD</span>
                                <span class="ml-1.5 sm:ml-3 font-sans font-light text-[12px] sm:text-[14px] leading-none">&rarr;</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Text Content -->
                    <div class="text-center mt-auto px-1">
                        <h3 class="font-serif text-[#4d3c31] text-[13px] sm:text-[17px] mb-1 font-bold truncate"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="font-sans text-[#71685f] text-[9px] sm:text-[11px] mb-1.5 sm:mb-2.5 truncate">Premium Leather</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-0.5 sm:gap-2.5">
                            <span class="font-sans text-[#4d3c31] text-[11px] sm:text-[12px] font-bold"><?= $price_formatted ?></span>
                        </div>
                    </div>
                    
                </a>
            <?php endforeach; ?>
        </div>

        <!-- View All Button -->
        <div class="mt-14 lg:mt-20 flex justify-center">
            <a href="new-arrivals.php" class="font-serif bg-[#4d3c31] border border-[#4d3c31] text-white px-10 py-[15px] text-[11px] font-semibold tracking-[0.2em] transition-all hover:bg-[#382b22] flex items-center justify-center shadow-md hover:shadow-lg hover:-translate-y-0.5 duration-300">
                VIEW NEW ARRIVALS <span class="ml-4 font-sans font-light text-[14px] leading-none">&rarr;</span>
            </a>
        </div>
        
    </div>
</section>








