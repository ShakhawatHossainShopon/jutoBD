<?php
require_once __DIR__ . '/config/config.php';

try {
    $stmt = $pdo->prepare("
        SELECT c.*, COUNT(p.id) as product_count 
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id
        ORDER BY c.title ASC
    ");
    $stmt->execute();
    $all_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_categories = [];
}

$fallbacks = [
    'https://images.unsplash.com/photo-1614252235316-f3eb54dc0616?q=80&w=600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1614252339460-e1d15c7cc68e?q=80&w=600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1624222247344-550fb60583dc?q=80&w=600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=600&auto=format&fit=crop'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories | JUTO</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Tailwind Config -->
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

    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-grow ">
        
        <!-- Page Title Banner -->
        <section class="w-full bg-[#f4f2ec] py-16 lg:py-24 border-b border-[#e5e0d4] text-center px-4" data-aos="fade">
            <span class="block text-[11px] font-semibold tracking-[0.3em] text-[#71685f] uppercase mb-4">COLLECTIONS</span>
            <h1 class="font-serif text-[42px] lg:text-[52px] text-[#4d3c31] uppercase tracking-[0.1em] mb-4">All Categories</h1>
            <p class="font-sans text-[#71685f] text-[15px] max-w-xl mx-auto">Discover our full range of handcrafted leather goods, meticulously designed for every aspect of your life.</p>
        </section>

        <!-- Categories Grid Section -->
        <section class="w-full bg-white py-12 lg:py-24">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12">
                
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6 lg:gap-10">
                    <?php if(empty($all_categories)): ?>
                        <div class="col-span-full text-center py-20 text-[#71685f]">
                            No categories found in the database.
                        </div>
                    <?php else: ?>
                        <?php foreach($all_categories as $index => $cat): 
                            $img_url = !empty($cat['image']) ? $cat['image'] : $fallbacks[$index % count($fallbacks)];
                        ?>
                            <a href="category.php?name=<?= urlencode($cat['title']) ?>" class="group cursor-pointer flex flex-col" data-aos="fade-up">
                                
                                <!-- Image Box -->
                                <div class="relative w-full aspect-[4/5] bg-[#f8f8f8] mb-4 sm:mb-6 overflow-hidden flex items-center justify-center">
                                    <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($cat['title']) ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                    
                                    <!-- Overlay -->
                                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors duration-300"></div>
                                </div>
                                
                                <!-- Text Content -->
                                <div class="text-center px-2">
                                    <h3 class="font-serif text-[#4d3c31] text-[16px] sm:text-[22px] mb-1 sm:mb-2 font-semibold tracking-wide uppercase truncate"><?= htmlspecialchars($cat['title']) ?></h3>
                                    <p class="font-sans text-[#71685f] text-[11px] sm:text-[13px] mb-2 sm:mb-4">
                                        <?= $cat['product_count'] ?> <?= $cat['product_count'] == 1 ? 'Product' : 'Products' ?>
                                    </p>
                                    <span class="inline-block border-b border-[#4d3c31] pb-1 font-sans text-[9px] sm:text-[11px] font-bold tracking-[0.2em] text-[#4d3c31] group-hover:text-[#8b5a2b] group-hover:border-[#8b5a2b] transition-colors">
                                        SHOP NOW
                                    </span>
                                </div>
                                
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </section>

    </main>

    <!-- Include Footer -->
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
