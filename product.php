<?php
require_once __DIR__ . '/config/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.title as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $product = false;
}

if (!$product) {
    header("Location: index.php");
    exit;
}

$images = json_decode($product['images'], true) ?: [];
foreach($images as &$img) {
    $img = str_replace('\\/', '/', $img);
}
$main_image = !empty($images) ? $images[0] : 'https://placehold.co/600x800/f8f8f8/cccccc?text=No+Image';

$colors = !empty($product['colors']) ? array_map('trim', explode(',', $product['colors'])) : ['Classic'];
$sizes = !empty($product['size']) ? array_map('trim', preg_split('/[,.]+/', $product['size'])) : [];

$price_fmt = "৳" . number_format($product['price'], 2);

function getApproxHex($colorName) {
    $c = strtolower($colorName);
    if(strpos($c, 'black') !== false) return '#1a1a1a';
    if(strpos($c, 'white') !== false) return '#ffffff';
    if(strpos($c, 'red') !== false) return '#8b0000';
    if(strpos($c, 'blue') !== false) return '#000080';
    if(strpos($c, 'green') !== false) return '#556b2f';
    if(strpos($c, 'yellow') !== false) return '#ffd700';
    if(strpos($c, 'brown') !== false) return '#8b4513';
    if(strpos($c, 'gray') !== false || strpos($c, 'grey') !== false) return '#808080';
    if(strpos($c, 'cream') !== false) return '#f5f5dc';
    if(strpos($c, 'tan') !== false) return '#d2b48c';
    return '#cccccc'; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> | JUTO</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

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

    <main class="flex-grow pt-8 pb-24">
        
        <!-- Breadcrumb -->
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12 mb-8">
            <div class="flex items-center text-[10px] font-sans font-bold tracking-widest text-[#8b8277] uppercase">
                <a href="index.php" class="hover:text-[#4a362a] transition-colors">Home</a>
                <span class="mx-2">/</span>
                <a href="category.php" class="hover:text-[#4a362a] transition-colors">Shop</a>
                <span class="mx-2">/</span>
                <?php if($product['category_name']): ?>
                    <a href="category.php?name=<?= urlencode($product['category_name']) ?>" class="hover:text-[#4a362a] transition-colors"><?= htmlspecialchars($product['category_name']) ?></a>
                    <span class="mx-2">/</span>
                <?php endif; ?>
                <span class="text-[#4a362a]"><?= htmlspecialchars($product['name']) ?></span>
            </div>
        </div>

        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
            
            <!-- Left: Images -->
            <div class="flex flex-col-reverse md:flex-row gap-4">
                <!-- Thumbnails (Desktop side, Mobile bottom) -->
                <?php if(count($images) > 1): ?>
                <div class="flex md:flex-col gap-4 overflow-x-auto md:overflow-visible w-full md:w-[100px] shrink-0">
                    <?php foreach($images as $idx => $img): ?>
                        <button class="w-[80px] md:w-full aspect-[4/5] bg-[#f8f8f8] border-2 <?= $idx === 0 ? 'border-[#4a362a]' : 'border-transparent' ?> hover:border-[#4a362a] transition-colors shrink-0" onclick="document.getElementById('main-image').src='<?= htmlspecialchars($img) ?>'; document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('border-[#4a362a]')); this.classList.add('border-[#4a362a]');">
                            <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover mix-blend-multiply thumb-btn" alt="Thumbnail">
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Main Image -->
                <div class="w-full bg-[#f8f8f8] aspect-[4/5] flex items-center justify-center relative">
                    <img id="main-image" src="<?= htmlspecialchars($main_image) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover mix-blend-multiply">
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="flex flex-col pt-4 lg:pt-10 lg:pr-10">
                <h1 class="font-serif text-[36px] lg:text-[48px] text-[#222] leading-tight mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center text-[#ffa41c] text-[15px]">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <span class="text-[#222] ml-2 font-bold text-[14px]">5.0</span>
                    </div>
                    <span class="text-[#dcd6cb]">|</span>
                    <span class="font-sans text-[12px] text-[#71685f] uppercase tracking-wider">Product ID: #<?= str_pad($product['id'], 5, '0', STR_PAD_LEFT) ?></span>
                </div>

                <div class="font-sans text-[24px] lg:text-[28px] font-bold text-[#222] mb-8">
                    <?= $price_fmt ?>
                </div>

                <p class="font-sans text-[#71685f] text-[15px] leading-relaxed mb-10">
                    <?= !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : 'Experience true craftsmanship with this premium leather piece. Designed to last a lifetime and age beautifully.' ?>
                </p>

                <!-- Colors -->
                <?php if(!empty($colors)): ?>
                <div class="mb-8">
                    <h3 class="font-sans text-[11px] font-bold text-[#4a362a] uppercase tracking-widest mb-3">Color</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach($colors as $idx => $color): ?>
                            <button class="color-btn w-8 h-8 rounded-full border border-[#dcd6cb] relative group focus:outline-none <?= $idx === 0 ? 'ring-2 ring-offset-2 ring-[#4a362a]' : '' ?>" style="background-color: <?= getApproxHex($color) ?>;" title="<?= htmlspecialchars($color) ?>" onclick="document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('ring-2', 'ring-offset-2', 'ring-[#4a362a]')); this.classList.add('ring-2', 'ring-offset-2', 'ring-[#4a362a]');">
                                <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#222] text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none"><?= htmlspecialchars($color) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Sizes -->
                <?php if(!empty($sizes)): ?>
                <div class="mb-10">
                    <div class="flex justify-between items-end mb-3">
                        <h3 class="font-sans text-[11px] font-bold text-[#4a362a] uppercase tracking-widest">Size</h3>
                        <a href="#" class="font-sans text-[11px] text-[#71685f] underline hover:text-[#4a362a]">Size Guide</a>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach($sizes as $idx => $size): ?>
                            <button class="size-btn border border-[#e2dcd0] text-[#71685f] font-sans text-[13px] font-bold min-w-[3rem] px-3 py-2 hover:border-[#4a362a] hover:text-[#4a362a] transition-colors focus:outline-none <?= $idx === 0 ? 'bg-[#4a362a] text-white border-[#4a362a] hover:text-white' : 'bg-white' ?>" onclick="document.querySelectorAll('.size-btn').forEach(b => { b.classList.remove('bg-[#4a362a]', 'text-white', 'border-[#4a362a]'); b.classList.add('bg-white', 'text-[#71685f]', 'border-[#e2dcd0]'); }); this.classList.remove('bg-white', 'text-[#71685f]', 'border-[#e2dcd0]'); this.classList.add('bg-[#4a362a]', 'text-white', 'border-[#4a362a]');">
                                <?= htmlspecialchars($size) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 mb-10">
                    <button onclick="addToCart(<?= $product['id'] ?>)" class="flex-grow bg-[#4a362a] text-white py-4 text-[12px] font-bold tracking-[0.2em] hover:bg-[#38281e] transition-colors shadow-lg hover:shadow-xl flex justify-center items-center gap-3">
                        <i class="fa-solid fa-bag-shopping"></i> ADD TO CART
                    </button>
                    <button class="w-full sm:w-auto bg-[#f4f2ec] border border-[#e5e0d4] text-[#4a362a] py-4 px-6 hover:bg-[#eae6de] transition-colors flex justify-center items-center" title="Add to Wishlist">
                        <i class="fa-regular fa-heart text-[18px]"></i>
                    </button>
                </div>
                
                <!-- Accodions for extra details -->
                <div class="border-t border-[#e2dcd0]">
                    <div class="py-4 border-b border-[#e2dcd0] cursor-pointer group">
                        <div class="flex justify-between items-center text-[#4a362a] font-serif text-[18px] font-bold">
                            Product Details
                            <i class="fa-solid fa-plus text-[12px] group-hover:rotate-90 transition-transform"></i>
                        </div>
                    </div>
                    <div class="py-4 border-b border-[#e2dcd0] cursor-pointer group">
                        <div class="flex justify-between items-center text-[#4a362a] font-serif text-[18px] font-bold">
                            Shipping & Returns
                            <i class="fa-solid fa-plus text-[12px] group-hover:rotate-90 transition-transform"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
