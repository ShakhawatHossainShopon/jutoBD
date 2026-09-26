<?php
include "../config/config.php";
$title = "Add Order";

$products = $pdo->query("SELECT id, name, uid FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $status = 'pending';

    try {
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (name, email, phone, city, address, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $city, $address, $status, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // Insert items
        if(isset($_POST['items']) && is_array($_POST['items'])) {
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, color, size, locked_price, locked_wholesale) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $pStmt = $pdo->prepare("SELECT price, wholesale FROM products WHERE id = ?");
            
            foreach($_POST['items'] as $item) {
                if(empty($item['product_id'])) continue;
                
                $pStmt->execute([$item['product_id']]);
                $prod = $pStmt->fetch();
                
                $stmtItem->execute([
                    $order_id,
                    $item['product_id'],
                    $item['quantity'] ?? 1,
                    $item['color'] ?? '',
                    $item['size'] ?? '',
                    $prod['price'] ?? 0,
                    $prod['wholesale'] ?? 0
                ]);
            }
        }
        
        $pdo->commit();
        $_SESSION['msg'] = "Order created successfully!";
        $_SESSION['msg_type'] = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg'] = "Failed to create order: " . $e->getMessage();
        $_SESSION['msg_type'] = "error";
    }

    header("Location: order_board");
    exit;
}

ob_start();
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Create Order</h1>
        <p class="text-slate-500 font-medium mt-1">Manually enter a new customer order</p>
    </div>
    <a href="order_board" class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-slate-50 transition-colors flex items-center shadow-sm hover:shadow-md">
        <i class="fa-solid fa-arrow-left mr-3"></i> Back to Board
    </a>
</div>

<div class="bg-white rounded-[32px] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 p-8 max-w-4xl mx-auto">
    <form method="post" class="space-y-8" id="orderForm">
        
        <!-- Cart Items Section -->
        <div class="pb-8 border-b border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[19px] font-extrabold text-slate-800 flex items-center"><span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-500 flex items-center justify-center mr-3"><i class="fa-solid fa-cart-shopping text-sm"></i></span> Order Items</h3>
                <button type="button" onclick="addCartItem()" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-[13px] font-bold hover:bg-slate-200 transition-colors shadow-sm border border-slate-200"><i class="fa-solid fa-plus mr-1.5"></i> Add Product</button>
            </div>
            
            <div id="cartItemsContainer" class="space-y-4">
                <!-- Single Item Row -->
                <div class="cart-item bg-slate-50 border border-slate-100 rounded-2xl p-5 flex flex-wrap md:flex-nowrap gap-4 items-end relative group">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Select Product <span class="text-red-500">*</span></label>
                        <select name="items[0][product_id]" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all appearance-none cursor-pointer">
                            <option value="">Select a product...</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['uid'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-24 shrink-0">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Qty <span class="text-red-500">*</span></label>
                        <input type="number" name="items[0][quantity]" min="1" value="1" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all text-center">
                    </div>
                    <div class="w-32 shrink-0">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Color</label>
                        <input type="text" name="items[0][color]" placeholder="e.g. Black" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                    </div>
                    <div class="w-32 shrink-0">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Size</label>
                        <input type="text" name="items[0][size]" placeholder="e.g. XL" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="pb-8 border-b border-slate-100">
            <h3 class="text-[19px] font-extrabold text-slate-800 mb-6 flex items-center"><span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mr-3"><i class="fa-solid fa-money-check-dollar text-sm"></i></span> Payment Method</h3>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">How was this order paid? <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="payment_method" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all appearance-none cursor-pointer">
                        <option value="Cash on Delivery">💵 Cash on Delivery (COD)</option>
                        <option value="Paid by bKash">🟣 Paid by bKash</option>
                        <option value="Paid by Nagad">🟠 Paid by Nagad</option>
                        <option value="Paid by SLS E-commerce">🌐 Paid by SLS E-commerce</option>
                        <option value="Other Paid Gateway">💳 Other (Paid Gateway)</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
        </div>

        <!-- Customer Section -->
        <div>
            <h3 class="text-[19px] font-extrabold text-slate-800 mb-6 flex items-center"><span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mr-3"><i class="fa-solid fa-address-card text-sm"></i></span> Customer Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Customer Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="e.g. John Doe" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" placeholder="e.g. john@example.com" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" placeholder="e.g. 017..." required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">City <span class="text-red-500">*</span></label>
                    <input type="text" name="city" placeholder="e.g. Dhaka" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Full Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" placeholder="House #, Road #, Area" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all custom-scrollbar"></textarea>
                </div>
            </div>
        </div>
        
        <div class="pt-6 flex justify-end">
            <button type="submit" class="bg-brand-600 text-white px-12 py-5 rounded-2xl font-extrabold hover:bg-brand-700 shadow-xl shadow-brand-500/20 hover:-translate-y-0.5 transition-all text-[16px] tracking-wide w-full md:w-auto text-center flex items-center justify-center">
                <i class="fa-solid fa-check mr-2"></i> Submit Order
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = 1;

function addCartItem() {
    const container = document.getElementById('cartItemsContainer');
    
    // Copy options from first select
    const firstSelect = document.querySelector('select[name^="items[0][product_id]"]');
    const optionsHtml = firstSelect ? firstSelect.innerHTML : '';

    const newRow = document.createElement('div');
    newRow.className = 'cart-item bg-slate-50 border border-slate-100 rounded-2xl p-5 flex flex-wrap md:flex-nowrap gap-4 items-end relative group mt-4 animation-slide-up';
    newRow.innerHTML = `
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Select Product <span class="text-red-500">*</span></label>
            <select name="items[${itemIndex}][product_id]" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all appearance-none cursor-pointer">
                ${optionsHtml}
            </select>
        </div>
        <div class="w-24 shrink-0">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Qty <span class="text-red-500">*</span></label>
            <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all text-center">
        </div>
        <div class="w-32 shrink-0">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Color</label>
            <input type="text" name="items[${itemIndex}][color]" placeholder="e.g. Black" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
        </div>
        <div class="w-32 shrink-0">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Size</label>
            <input type="text" name="items[${itemIndex}][size]" placeholder="e.g. XL" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
        </div>
        <button type="button" onclick="removeCartItem(this)" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors absolute -right-4 md:-right-14 top-1/2 -translate-y-1/2 opacity-100 md:opacity-0 md:group-hover:opacity-100 shadow-sm border border-red-100" title="Remove Item">
            <i class="fa-solid fa-trash text-sm"></i>
        </button>
    `;
    
    container.appendChild(newRow);
    itemIndex++;
}

function removeCartItem(btn) {
    const container = document.getElementById('cartItemsContainer');
    if (container.children.length > 1) {
        btn.closest('.cart-item').remove();
    } else {
        alert("You must have at least one product in the order.");
    }
}
</script>

<style>
.animation-slide-up {
    animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
