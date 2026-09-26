<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: ../index.php");
    exit;
}

$contact = $_POST['contact'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$address_line = $_POST['address'] ?? '';
$apartment = $_POST['apartment'] ?? '';
$city = $_POST['city'] ?? '';
$zip_code = $_POST['zip_code'] ?? '';
$phone = $_POST['phone'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'Cash on Delivery';

// Name mapping
$full_name = trim($first_name . ' ' . $last_name);

// Email/Phone mapping from contact
$email = '';
if (strpos($contact, '@') !== false) {
    $email = $contact;
} else {
    if (empty($phone)) $phone = $contact;
}

// Address mapping
$full_address = $address_line;
if (!empty($apartment)) {
    $full_address .= ', ' . $apartment;
}

// Insert into orders table for each item in cart
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(",", array_fill(0, count($ids), "?"));
$stmt = $pdo->prepare("SELECT id, name, price, colors, size FROM products WHERE id IN ($placeholders)");
$stmt->execute(array_values($ids));
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$insert_stmt = $pdo->prepare("
    INSERT INTO orders 
    (product_id, name, phone, city, address, quantity, color, size, zip_code, payment_method, email, status) 
    VALUES 
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");

$pdo->beginTransaction();
try {
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        
        // Pick first color and size as defaults if any
        $colors = !empty($p['colors']) ? array_map('trim', explode(',', $p['colors'])) : [];
        $color = !empty($colors) ? $colors[0] : null;
        
        $sizes = !empty($p['size']) ? array_map('trim', preg_split('/[,.]+/', $p['size'])) : [];
        $size = !empty($sizes) ? $sizes[0] : null;

        $insert_stmt->execute([
            $p['id'],
            $full_name,
            $phone,
            $city,
            $full_address,
            $qty,
            $color,
            $size,
            $zip_code,
            $payment_method,
            $email
        ]);
    }
    $pdo->commit();
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    // Redirect to success page
    header("Location: ../success.php");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error placing order: " . $e->getMessage());
}
