<?php

$product_id = $_GET['id'] ?? 0;
include "./config/config.php";

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) die("Product not found");
// Initialize error message
$order_error = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name     = $_POST['name'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    $city     = $_POST['city'];
    $zip_code = $_POST['zip_code'];
    $color    = $_POST['color'];
    $size     = $_POST['size'];
    $quantity = (int)$_POST['quantity'];
    $shipping_cost = (float)$_POST['shipping_method'];
    $status = 'pending';
    $created_at = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO orders (product_id,name,phone,city,address,quantity,color,size,status,created_at)
                               VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $product_id, $name, $phone, $city, $address, $quantity, $color, $size, $status, $created_at
        ]);

        $grand_total = $product['price'] * $quantity + $shipping_cost;

        $_SESSION['order_success'] = [
            'name' => $name,
            'quantity' => $quantity,
            'product_name' => $product['name'],
            'grand_total' => $grand_total
        ];

        header("Location: thankyou.php");
        exit;

    } catch (PDOException $e) {
        $order_error = "Order failed! Please try again. Error: " . $e->getMessage();
    }
}

ob_start();
?>

<main class="pdp-container">
    <?php if($order_error): ?>
        <div style="padding:20px; background:#F8D7DA; color:#721C24; border-radius:8px; margin-bottom:20px;">
            <?= htmlspecialchars($order_error) ?>
        </div>
    <?php endif; ?>

    <form id="order-form" method="POST">
        <div class="order-form-grid">

            <!-- 1. Shipping Info -->
            <div class="form-column">
                <div class="form-section">
                    <h2>Shipping Information</h2>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" placeholder="Enter your full name" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" placeholder="Enter quantity" name="quantity" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="number" placeholder="Enter phone number" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" placeholder="Enter your address" id="address" name="address" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input placeholder="Enter city" type="text" id="city" name="city" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <select id="color" name="color" required>
                                <?php foreach(explode(',', $product['colors']) as $c): ?>
                                    <option value="<?= htmlspecialchars(trim($c)) ?>"><?= htmlspecialchars(trim($c)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="size">Size</label>
                            <select id="size" name="size" required>
                                <?php foreach(explode(',', $product['size']) as $s): ?>
                                    <option value="<?= htmlspecialchars(trim($s)) ?>"><?= htmlspecialchars(trim($s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="form-section">
                    <h2>Shipping Location</h2>
                    <div class="shipping-options">
                        <div class="shipping-option">
                            <input type="radio" id="inside-dhaka" name="shipping_method" value="70" checked>
                            <label for="inside-dhaka">Inside Dhaka</label>
                        </div>
                        <div class="shipping-option">
                            <input type="radio" id="outside-dhaka" name="shipping_method" value="180">
                            <label for="outside-dhaka">Outside Dhaka</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Order Summary -->
            <div class="summary-column">
                <div class="order-summary">
                    <h2 style="color:#008ECC;">Your Order</h2>

                    <div class="summary-item">
                      <span>Quantity</span> <span style="color:#16A34A;" id="summary-quantity">1</span>
                    </div>
                    <div class="summary-item">
                        <span>Subtotal</span>
                      <span id="summary-subtotal" style="color:#16A34A;"><?= number_format($product['price'],2) ?> BDT</span>
                    </div>
                    <div class="summary-item">
                        <span>Shipping (<span id="summary-shipping-text">Inside Dhaka</span>)</span>
                        <span id="summary-shipping-amount" style="color:#16A34A;">70.00 BDT</span>
                    </div>
                    <div class="summary-total">
                        <span>Grand Total : </span>
                        <span style="color:#16A34A;font-weight:600" id="summary-grand"><?= number_format($product['price']+70,2) ?> BDT</span>
                    </div>
                </div>
                <button type="submit" id="place-order-btn">Place Order</button>
            </div>

        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const qtyInput = document.querySelector('input[name="quantity"]');
    const shippingOptions = document.querySelectorAll('input[name="shipping_method"]');
    const pricePerUnit = <?= $product['price'] ?>;

    const summaryQty = document.getElementById('summary-quantity');
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryShippingText = document.getElementById('summary-shipping-text');
    const summaryShippingAmount = document.getElementById('summary-shipping-amount');
    const summaryGrand = document.getElementById('summary-grand');

    const updateSummary = () => {
        const quantity = parseInt(qtyInput.value) || 1;
        const selectedShipping = Array.from(shippingOptions).find(r => r.checked);
        const shippingCost = parseFloat(selectedShipping.value);
        const shippingLabel = selectedShipping.nextElementSibling.textContent;
        const subtotal = pricePerUnit * quantity;
        const grandTotal = subtotal + shippingCost;

        summaryQty.textContent = quantity;
        summarySubtotal.textContent = subtotal.toFixed(2) + ' BDT';
        summaryShippingText.textContent = shippingLabel;
        summaryShippingAmount.textContent = shippingCost.toFixed(2) + ' BDT';
        summaryGrand.textContent = grandTotal.toFixed(2) + ' BDT';
    };

    qtyInput.addEventListener('input', updateSummary);
    shippingOptions.forEach(r => r.addEventListener('change', updateSummary));

    updateSummary();
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
