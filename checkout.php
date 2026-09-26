<?php
require_once __DIR__ . "/config/config.php";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($_SESSION['cart'])) {
        header("Location: index.php");
        exit;
    }
    
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $city = $_POST['city'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'Cash on Delivery';

    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $stmt = $pdo->prepare("SELECT p.id, p.name, p.price, p.wholesale, p.colors, p.size, d.discount_percent, d.end_date FROM products p LEFT JOIN discounts d ON p.id = d.product_id AND d.end_date > NOW() WHERE p.id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdo->beginTransaction();
    try {
        // Insert into orders table ONCE
        $order_stmt = $pdo->prepare("
            INSERT INTO orders 
            (name, email, phone, city, address, status, payment_method) 
            VALUES 
            (?, ?, ?, ?, ?, 'pending', ?)
        ");
        
        $order_stmt->execute([
            $name,
            $email,
            $phone,
            $city,
            $address,
            $payment_method
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Insert each product into order_items
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items
            (order_id, product_id, quantity, color, size, locked_price, locked_wholesale)
            VALUES
            (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $colors = !empty($p['colors']) ? array_map('trim', explode(',', $p['colors'])) : [];
            $color = !empty($colors) ? $colors[0] : null;
            $sizes = !empty($p['size']) ? array_map('trim', preg_split('/[,.]+/', $p['size'])) : [];
            $size = !empty($sizes) ? $sizes[0] : null;
            
            $locked_price = $p['price'] ?: 0;
            if (!empty($p['discount_percent'])) {
                $locked_price = $locked_price - ($locked_price * ($p['discount_percent'] / 100));
            }
            $locked_wholesale = $p['wholesale'] ?: 0;

            $item_stmt->execute([
                $order_id,
                $p['id'],
                $qty,
                $color,
                $size,
                $locked_price,
                $locked_wholesale
            ]);
        }
        
        $pdo->commit();
        $_SESSION['cart'] = [];
        header("Location: success.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error placing order: " . $e->getMessage());
    }
}

// Fetch cart items for display
$cart = [];
$subtotal = 0;
$count = 0;

if (!empty($_SESSION["cart"])) {
    $ids = array_keys($_SESSION["cart"]);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $stmt = $pdo->prepare("SELECT p.id, p.name, p.price, p.images, d.discount_percent, d.end_date FROM products p LEFT JOIN discounts d ON p.id = d.product_id AND d.end_date > NOW() WHERE p.id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $p) {
        $qty = $_SESSION["cart"][$p["id"]];
        $images = json_decode($p["images"], true);
        $img_url = (is_array($images) && count($images) > 0) ? $images[0] : "https://placehold.co/100x100/f8f8f8/cccccc";
        $img_url = str_replace("\\/", "/", $img_url);
        
        $active_price = $p["price"];
        if (!empty($p["discount_percent"])) {
            $active_price = $active_price - ($active_price * ($p["discount_percent"] / 100));
        }
        
        $item_total = $active_price * $qty;
        $subtotal += $item_total;
        $count += $qty;
        
        $cart[] = [
            "id" => $p["id"],
            "name" => $p["name"],
            "price" => $active_price,
            "qty" => $qty,
            "image" => $img_url
        ];
    }
}

// If cart is empty, redirect to index
if (empty($cart)) {
    header("Location: index.php");
    exit;
}

$shipping = 70; // Default inside Dhaka
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | JUTO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
        .checkout-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #333;
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .checkout-input:focus {
            outline: none;
            border-color: #4a362a;
            box-shadow: 0 0 0 1px #4a362a;
        }
        .checkout-input::placeholder {
            color: #737373;
        }
        .section-title {
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 16px;
        }
        .custom-radio {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 1px solid #d9d9d9;
            border-radius: 50%;
            background-color: #fff;
            display: inline-block;
            position: relative;
            cursor: pointer;
        }
        .custom-radio:checked {
            border-color: #4a362a;
            background-color: #4a362a;
        }
        .custom-radio:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #fff;
        }
    </style>
</head>
<body class="bg-white text-[#333] font-sans antialiased min-h-screen flex flex-col">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex-grow flex flex-col md:flex-row w-full">
        <!-- Left Column (Form) -->
        <div class="w-full md:w-[55%] bg-white flex justify-end">
            <div class="w-full max-w-[600px] px-4 md:px-10 lg:px-14 py-10">
                
                <!-- Logo -->
                <a href="index.php" class="block mb-8">
                    <h1 class="font-serif text-[28px] font-bold tracking-wider text-[#333]">JUTO</h1>
                </a>

                <form action="" method="POST" id="checkout-form">
                    
                    <div class="mb-10">
                        <h2 class="section-title">Contact Information</h2>
                        
                        <div class="mb-3">
                            <input type="text" name="name" class="checkout-input" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="checkout-input" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="phone" class="checkout-input" placeholder="Phone Number" required>
                        </div>
                    </div>

                    <!-- DELIVERY -->
                    <div class="mb-10">
                        <h2 class="section-title">Delivery Details</h2>
                        <div class="mb-3">
                            <input type="text" name="city" class="checkout-input" placeholder="City (e.g. Dhaka)" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="address" class="checkout-input" placeholder="Full Address" rows="3" required></textarea>
                        </div>
                    </div>

                    <!-- PAYMENT -->
                    <div class="mb-10">
                        <h2 class="section-title">Payment</h2>
                        <p class="text-[13px] text-[#737373] mb-4">All transactions are secure and encrypted.</p>
                        
                        <div class="border border-[#e5e5e5] rounded-md overflow-hidden bg-white">
                            <!-- Cash on Delivery -->
                            <div class="p-4 border-b border-[#e5e5e5] flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-3 w-full" onclick="document.getElementById('cod').click()">
                                    <input type="radio" id="cod" name="payment_method" value="Cash on Delivery" class="custom-radio" checked>
                                    <label for="cod" class="text-[13px] font-medium cursor-pointer w-full">Cash on Delivery (COD)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-8 flex justify-end">
                        <input type="hidden" name="place_order" value="1">
                        <button type="submit" class="bg-[#4a362a] text-white px-8 py-4 text-[13px] font-medium tracking-widest uppercase hover:bg-[#382b22] transition-colors rounded">Place order &nbsp; <i class="fa-solid fa-arrow-right"></i></button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Column (Order Summary) -->
        <div class="w-full md:w-[45%] bg-[#f7f7f7] border-l border-[#e5e5e5] flex justify-start relative">
            <div class="w-full max-w-[500px] px-4 md:px-10 py-10 sticky top-0 h-max">
                
                <!-- Items -->
                <div class="flex flex-col gap-4 mb-6 pb-6 border-b border-[#e5e5e5]">
                    <?php foreach ($cart as $item): ?>
                    <div class="flex gap-4 items-center">
                        <div class="relative w-16 h-16 rounded-lg bg-white border border-[#e5e5e5] flex items-center justify-center shrink-0">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-12 h-12 object-cover">
                            <div class="absolute -top-2 -right-2 w-5 h-5 bg-[#737373] text-white text-[11px] flex items-center justify-center rounded-full font-medium">
                                <?= $item['qty'] ?>
                            </div>
                        </div>
                        <div class="flex-grow flex flex-col justify-center">
                            <span class="text-[13px] font-medium text-[#333] leading-snug"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                        <div class="text-[13px] font-medium text-[#333]">
                            ৳<?= number_format($item['price'] * $item['qty'], 2) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals -->
                <div class="flex flex-col gap-3 mb-6 pb-6 border-b border-[#e5e5e5] text-[13px] text-[#333]">
                    <div class="flex justify-between items-center">
                        <span>Subtotal &middot; <?= $count ?> items</span>
                        <span class="font-medium">৳<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-1">Shipping</span>
                        <span class="font-medium" id="shipping-cost">৳70.00</span>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="flex justify-between items-center">
                    <span class="text-[16px] text-[#333] uppercase tracking-widest">Total</span>
                    <div class="flex items-end gap-2">
                        <span class="text-[11px] text-[#737373] mb-1">BDT</span>
                        <span class="text-[24px] font-bold text-[#333]" id="grand-total">৳<?= number_format($total, 2) ?></span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Mobile Order Summary Toggle (Hidden on Desktop) -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-[#e5e5e5] p-4 flex justify-between items-center z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
        <button id="toggle-summary" class="flex items-center gap-2 text-[13px] font-medium text-[#4a362a]">
            <i class="fa-solid fa-cart-shopping"></i> Show order summary <i class="fa-solid fa-chevron-up text-[10px]"></i>
        </button>
        <span class="text-[18px] font-bold text-[#333]">৳<?= number_format($total, 2) ?></span>
    </div>

</body>
</html>
