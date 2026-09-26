<?php
include "../config/config.php";
$title = "Order Details - Invoice";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: order_board");
    exit;
}

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: order_board");
    exit;
}

// Fetch items
$itemStmt = $pdo->prepare("SELECT oi.*, p.name AS product_name, p.uid AS product_uid, p.images AS product_images FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$grandTotal = 0;
$totalItems = 0;
foreach($items as $item) {
    $grandTotal += ($item['locked_price'] * $item['quantity']);
    $totalItems += $item['quantity'];
}

$paymentMethod = $order['payment_method'] ?? 'Cash on Delivery';

// Status formatting
$statusColors = [
    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
    'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
    'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
    'cancelled' => 'bg-red-100 text-red-700 border-red-200',
];
$statusIcons = [
    'pending' => 'fa-clock',
    'processing' => 'fa-gear fa-spin',
    'completed' => 'fa-check',
    'cancelled' => 'fa-xmark',
];
$statusColor = $statusColors[$order['status']] ?? 'bg-slate-100 text-slate-700 border-slate-200';
$statusIcon = $statusIcons[$order['status']] ?? 'fa-circle-info';

ob_start();
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Order #<?= $order['id'] ?></h1>
            <span class="px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-lg border <?= $statusColor ?>">
                <i class="fa-solid <?= $statusIcon ?> mr-1"></i> <?= $order['status'] ?>
            </span>
        </div>
        <p class="text-slate-500 font-medium"><i class="fa-solid fa-calendar-day mr-1"></i> Placed on <?= date("F j, Y, g:i a", strtotime($order['created_at'])) ?></p>
    </div>
    
    <div class="flex gap-3">
        <a href="order_board" class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl text-[14px] font-bold hover:bg-slate-50 transition-colors shadow-sm flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back
        </a>
        <a href="invoice_print?id=<?= $order['id'] ?>" target="_blank" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[14px] font-bold hover:bg-brand-700 hover:-translate-y-0.5 transition-all shadow-xl shadow-brand-500/20 flex items-center print-hide">
            <i class="fa-solid fa-print mr-2"></i> Print Invoice
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column (Invoice Items) -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-[19px] font-extrabold text-slate-800 flex items-center"><span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mr-3"><i class="fa-solid fa-cart-shopping text-sm"></i></span> Cart Items (<?= $totalItems ?>)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-extrabold tracking-widest">
                        <tr>
                            <th class="px-6 py-5">Product Details</th>
                            <th class="px-6 py-5 text-center">Attributes</th>
                            <th class="px-6 py-5 text-center">Price x Qty</th>
                            <th class="px-6 py-5 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach($items as $item): 
                            $lineTotal = $item['locked_price'] * $item['quantity'];
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php 
    $imgs = json_decode($item["product_images"], true);
    $img = (!empty($imgs) && is_array($imgs)) ? str_replace("\/", "/", $imgs[0]) : "https://placehold.co/100x100";
    ?>
    <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
        <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover mix-blend-multiply">
    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-[14px]"><?= htmlspecialchars($item['product_name']) ?></p>
                                        <p class="text-[11px] bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-md uppercase tracking-widest mt-1 inline-block">UID: <?= $item['product_uid'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col gap-1 items-center justify-center">
                                    <?php if($item['color']): ?>
                                        <span class="text-[12px] text-slate-600 font-medium bg-slate-100 px-2 py-1 rounded-md">Color: <b><?= htmlspecialchars($item['color']) ?></b></span>
                                    <?php endif; ?>
                                    <?php if($item['size']): ?>
                                        <span class="text-[12px] text-slate-600 font-medium bg-slate-100 px-2 py-1 rounded-md">Size: <b><?= htmlspecialchars($item['size']) ?></b></span>
                                    <?php endif; ?>
                                    <?php if(!$item['color'] && !$item['size']): ?>
                                        <span class="text-[12px] text-slate-400">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-slate-500">
                                ৳<?= number_format($item['locked_price'], 2) ?> × <?= $item['quantity'] ?>
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-slate-800 text-[15px]">
                                ৳<?= number_format($lineTotal, 2) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                <div class="w-64 space-y-3">
                    <div class="flex justify-between text-[14px] font-bold text-slate-500">
                        <span>Subtotal</span>
                        <span>৳<?= number_format($grandTotal, 2) ?></span>
                    </div>
                    <div class="flex justify-between text-[14px] font-bold text-slate-500">
                        <span>Delivery Fee</span>
                        <span>৳0.00</span> <!-- Placeholder for future -->
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex justify-between items-end">
                        <span class="text-[14px] font-bold text-slate-500 uppercase tracking-widest">Grand Total</span>
                        <span class="text-2xl font-extrabold text-brand-600">৳<?= number_format($grandTotal, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column (Customer & Status) -->
    <div class="space-y-8">
        
        <!-- Payment Info -->
        <div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden p-6 relative">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-100/50 to-transparent rounded-bl-full pointer-events-none"></div>
            <h3 class="text-[16px] font-extrabold text-slate-800 flex items-center mb-4"><i class="fa-solid fa-wallet text-emerald-500 mr-2 text-lg"></i> Payment Info</h3>
            
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-700 text-xl">
                    <i class="fa-solid <?= (stripos($paymentMethod, 'bKash') !== false || stripos($paymentMethod, 'Nagad') !== false || stripos($paymentMethod, 'Paid') !== false) ? 'fa-check-double text-emerald-500' : 'fa-hand-holding-dollar text-orange-500' ?>"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Method</p>
                    <p class="font-extrabold text-slate-800 text-[14px]"><?= htmlspecialchars($paymentMethod) ?></p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden p-6">
            <h3 class="text-[16px] font-extrabold text-slate-800 flex items-center mb-6"><i class="fa-solid fa-user-circle text-brand-500 mr-2 text-lg"></i> Customer Details</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Name</p>
                    <p class="font-bold text-slate-800"><?= htmlspecialchars($order['name']) ?></p>
                </div>
                <?php if(!empty($order['email'])): ?>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email</p>
                    <p class="font-medium text-slate-600"><a href="mailto:<?= htmlspecialchars($order['email']) ?>" class="hover:text-brand-600"><?= htmlspecialchars($order['email']) ?></a></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Phone</p>
                    <p class="font-medium text-slate-600"><a href="tel:<?= htmlspecialchars($order['phone']) ?>" class="hover:text-brand-600"><?= htmlspecialchars($order['phone']) ?></a></p>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2"><i class="fa-solid fa-location-dot mr-1"></i> Shipping Address</p>
                    <p class="font-bold text-slate-800 text-[14px]"><?= htmlspecialchars($order['city']) ?></p>
                    <p class="font-medium text-slate-500 text-[14px] mt-1 leading-relaxed"><?= nl2br(htmlspecialchars($order['address'])) ?></p>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    body { background-color: white !important; }
    .sidebar, .print-hide { display: none !important; }
    main { margin-left: 0 !important; padding: 0 !important; }
    .shadow-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
}
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

