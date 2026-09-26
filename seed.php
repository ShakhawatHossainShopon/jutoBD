<?php
require_once __DIR__ . '/config/config.php';

try {
    // Clean everything just in case
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE products; TRUNCATE TABLE categories; SET FOREIGN_KEY_CHECKS=1;");

    $categories = ['Oxford', 'Cap Toe', 'Wholecut', 'Wingtip', 'Brogue'];
    $catIds = [];

    foreach($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categories (title, des, image, added_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$cat, "$cat premium leather shoes", '']);
        $catIds[$cat] = $pdo->lastInsertId();
    }

    $pdo->exec("INSERT INTO suppliers (name) VALUES ('Juto Leather Goods')");
    $supplierId = $pdo->lastInsertId();

    $shoeImages = [
        'https://images.unsplash.com/photo-1614252339460-e1d15c7cc68e?q=80&w=600&auto=format&fit=crop', // classic black oxford
        'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=600&auto=format&fit=crop', // white sneaker
        'https://images.unsplash.com/photo-1614252235316-f3eb54dc0616?q=80&w=600&auto=format&fit=crop', // tan oxford
        'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=600&auto=format&fit=crop', // tote bag
        'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=600&auto=format&fit=crop', // brown shoe
        'https://images.unsplash.com/photo-1560769629-975ec94e6a86?q=80&w=600&auto=format&fit=crop', // sneaker
        'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?q=80&w=600&auto=format&fit=crop' // formal shoes
    ];

    $colors = ['Black', 'Brown', 'Dark Brown', 'Tan', 'Burgundy'];
    $sizes = ['39', '40', '41', '42', '43', '44', '45'];

    for($i = 1; $i <= 24; $i++) {
        $catName = $categories[array_rand($categories)];
        $catId = $catIds[$catName];
        
        // Pick 1-2 random colors
        $randColors = array_rand(array_flip($colors), rand(1, 2));
        if(!is_array($randColors)) $randColors = [$randColors];
        $colorStr = implode(',', $randColors);
        
        // Pick 3-6 random sizes
        $randSizes = array_rand(array_flip($sizes), rand(3, 6));
        if(!is_array($randSizes)) $randSizes = [$randSizes];
        sort($randSizes);
        $sizeStr = implode(',', $randSizes);
        
        $name = "The " . $catName . " Signature";
        $uid = "SHOE-" . str_pad($i, 4, '0', STR_PAD_LEFT);
        $price = rand(150, 450) + 0.00;
        
        // Pick 1 random image
        $img1 = $shoeImages[array_rand($shoeImages)];
        $imagesJson = json_encode([$img1]);
        
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (uid, name, category_id, supplier_id, wholesale, price, size, description, colors, profit, link, images, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $uid, 
            $name, 
            $catId, 
            $supplierId, 
            $price * 0.5, 
            $price, 
            $sizeStr, 
            "Handcrafted premium leather shoe.", 
            $colorStr, 
            $price * 0.5, 
            "", 
            $imagesJson
        ]);
    }
    
    // Create Dummy Orders for Best Sellers
    $allProductIds = $pdo->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);
    
    // Insert random sales for products
    $orderStmt = $pdo->prepare("INSERT INTO orders (product_id, name, phone, city, address, quantity, status) VALUES (?, 'John Doe', '123456789', 'New York', '123 Main St', ?, 'completed')");
    foreach($allProductIds as $pid) {
        $qty = rand(0, 150);
        if($qty > 10) {
            $orderStmt->execute([$pid, $qty]);
        }
    }

    echo "Database successfully seeded with 24 premium leather shoes and dummy sales data!";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
