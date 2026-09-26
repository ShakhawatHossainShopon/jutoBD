<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config/config.php';
}

try {
    $stmt = $pdo->prepare("SELECT id, title, image FROM categories LIMIT 5");
    $stmt->execute();
    $db_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_categories = [];
}

$fallbacks = [
    'https://images.unsplash.com/photo-1614252235316-f3eb54dc0616?q=80&w=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1614252339460-e1d15c7cc68e?q=80&w=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1624222247344-550fb60583dc?q=80&w=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=500&auto=format&fit=crop'
];
?>

<!-- Categories Section -->
<section class="w-full bg-white py-20 lg:py-28" data-aos="fade">
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-14">
            <span class="block text-[10px] font-semibold tracking-[0.3em] text-[#71685f] uppercase mb-4">
                FIND YOUR EVERYDAY ESSENTIAL
            </span>
            <h2 class="font-serif text-[32px] lg:text-[40px] text-[#4d3c31] uppercase tracking-[0.05em] font-normal">
                MADE FOR THE WAY YOU LIVE.
            </h2>
        </div>
        
        <!-- 5-Column Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-5 mb-12 lg:mb-16">
            <?php foreach($db_categories as $index => $cat): 
                $img_url = !empty($cat['image']) ? $cat['image'] : $fallbacks[$index % count($fallbacks)];
            ?>
                <a href="category.php?name=<?= urlencode($cat['title']) ?>" class="group relative block w-full aspect-[4/5] lg:aspect-[3/4] overflow-hidden bg-[#f4f4f4]">
                    
                    <!-- Image -->
                    <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($cat['title']) ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-[1.03]">
                    
                    <!-- Label Overlay -->
                    <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 bg-[#eeeae0] group-hover:bg-[#1a1a1a] transition-colors duration-300 py-3 sm:py-4 flex justify-center items-center shadow-sm">
                        <h4 class="font-serif text-[#4d3c31] group-hover:text-white transition-colors duration-300 text-[11px] sm:text-[14px] tracking-[0.1em] font-semibold uppercase">
                            <?= htmlspecialchars($cat['title']) ?>
                        </h4>
                    </div>
                    
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Bottom CTA -->
        <div class="flex justify-center">
            <a href="categories.php" class="font-serif bg-[#4d3c31] text-white px-10 py-[15px] text-[11px] font-semibold tracking-[0.2em] transition-colors hover:bg-[#382b22] flex items-center justify-center shadow-md hover:shadow-lg hover:-translate-y-0.5 duration-300">
                EXPLORE ALL CATEGORIES <span class="ml-4 font-sans font-light text-[14px] leading-none">&rarr;</span>
            </a>
        </div>
        
    </div>
</section>
