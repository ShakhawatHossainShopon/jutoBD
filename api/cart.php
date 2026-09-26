<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($action === 'add') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    $qty = isset($data['qty']) ? (int)$data['qty'] : 1;
    
    if ($id > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'remove') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'update') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    $qty = isset($data['qty']) ? (int)$data['qty'] : 1;
    
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'fetch') {
    $cart = [];
    $total = 0;
    $count = 0;
    
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT p.id, p.name, p.price, p.images, d.discount_percent, d.end_date FROM products p LEFT JOIN discounts d ON p.id = d.product_id AND d.end_date > NOW() WHERE p.id IN ($placeholders)");
        $stmt->execute(array_values($ids));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $images = json_decode($p['images'], true);
            $img_url = (is_array($images) && count($images) > 0) ? $images[0] : 'https://placehold.co/100x100/f8f8f8/cccccc';
            $img_url = str_replace('\\/', '/', $img_url);
            
            $active_price = $p['price'];
            if (!empty($p['discount_percent'])) {
                $active_price = $active_price - ($active_price * ($p['discount_percent'] / 100));
            }
            $subtotal = $active_price * $qty;
            $total += $subtotal;
            $count += $qty;
            
            $cart[] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'price' => $active_price,
                'price_fmt' => "?" . number_format($active_price, 2),
                'qty' => $qty,
                'image' => $img_url,
                'subtotal' => $subtotal,
                'subtotal_fmt' => "?" . number_format($subtotal, 2)
            ];
        }
    }
    
    echo json_encode([
        'items' => $cart,
        'total' => $total,
        'total_fmt' => "?" . number_format($total, 2),
        'count' => $count
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>


