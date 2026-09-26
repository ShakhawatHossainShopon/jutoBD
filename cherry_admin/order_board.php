<?php
include "../config/config.php";
$title = "Order Board";

// Tabs logic
$tab = $_GET['tab'] ?? 'unpaid';

// Get counts for both tabs regardless of which is active
$unpaidSql = "SELECT COUNT(*) FROM orders o WHERE o.status IN ('pending', 'processing') AND (o.payment_method IS NULL OR (o.payment_method NOT LIKE '%bKash%' AND o.payment_method NOT LIKE '%Nagad%' AND o.payment_method NOT LIKE '%SLS%' AND o.payment_method NOT LIKE '%Paid%'))";
$totalUnpaid = $pdo->query($unpaidSql)->fetchColumn();

$paidSql = "SELECT COUNT(*) FROM orders o WHERE o.status IN ('pending', 'processing') AND (o.payment_method LIKE '%bKash%' OR o.payment_method LIKE '%Nagad%' OR o.payment_method LIKE '%SLS%' OR o.payment_method LIKE '%Paid%')";
$totalPaid = $pdo->query($paidSql)->fetchColumn();

// Determine which condition to use for the main table query
$paymentCondition = "";
if ($tab === 'paid') {
    $paymentCondition = "AND (o.payment_method LIKE '%bKash%' OR o.payment_method LIKE '%Nagad%' OR o.payment_method LIKE '%SLS%' OR o.payment_method LIKE '%Paid%')";
    $totalOrders = $totalPaid;
} else {
    $paymentCondition = "AND (o.payment_method IS NULL OR (o.payment_method NOT LIKE '%bKash%' AND o.payment_method NOT LIKE '%Nagad%' AND o.payment_method NOT LIKE '%SLS%' AND o.payment_method NOT LIKE '%Paid%'))";
    $totalOrders = $totalUnpaid;
}

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$totalPages = ceil($totalOrders / $perPage);

// Fetch orders (Parent level only)
$sql = "SELECT o.*
        FROM orders o
        WHERE o.status IN ('pending', 'processing') $paymentCondition
        ORDER BY o.created_at DESC
        LIMIT :offset, :perPage";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Order Board</h1>
        <p class="text-slate-500 font-medium mt-1">Manage pending and processing orders</p>
    </div>
    
    <div class="flex gap-4">
        <div class="bg-white px-5 py-3 rounded-2xl shadow-card border border-slate-100 flex items-center text-[15px] font-bold text-slate-600">
            <i class="fa-solid fa-clock mr-2.5 text-amber-500"></i>
            <?= $totalPaid + $totalUnpaid ?> Total Active
        </div>
        <a href="add_order" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Add Order
        </a>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex gap-8 mb-8 border-b border-slate-200">
    <a href="?tab=unpaid" class="pb-4 px-2 font-bold text-[15px] transition-colors relative flex items-center <?= $tab === 'unpaid' ? 'text-brand-600' : 'text-slate-400 hover:text-slate-700' ?>">
        <i class="fa-solid fa-hand-holding-dollar mr-2"></i> Cash on Delivery (Unpaid)
        <span class="ml-2.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold <?= $tab === 'unpaid' ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-500' ?>"><?= $totalUnpaid ?></span>
        <?php if($tab === 'unpaid'): ?>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-brand-600 rounded-t-lg"></div>
        <?php endif; ?>
    </a>
    <a href="?tab=paid" class="pb-4 px-2 font-bold text-[15px] transition-colors relative flex items-center <?= $tab === 'paid' ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-700' ?>">
        <i class="fa-solid fa-money-check-dollar mr-2"></i> Pre-Paid (bKash / Nagad / SLS)
        <span class="ml-2.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold <?= $tab === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= $totalPaid ?></span>
        <?php if($tab === 'paid'): ?>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-600 rounded-t-lg"></div>
        <?php endif; ?>
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
            <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-extrabold tracking-widest">
                <tr>
                    <th class="px-6 py-5">Order Info</th>
                    <th class="px-6 py-5">Customer & Delivery</th>
                    <th class="px-6 py-5">Financials & Payment</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if($orders): ?>
                    <?php foreach($orders as $o):
                        // Fetch cart items for this order
                        $iStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.uid as product_uid FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $iStmt->execute([$o['id']]);
                        $items = $iStmt->fetchAll(PDO::FETCH_ASSOC);

                        $itemCount = 0;
                        $totalPrice = 0;
                        $totalWholesale = 0;
                        $firstProduct = null;

                        foreach($items as $idx => $item) {
                            $qty = $item['quantity'];
                            $itemCount += $qty;
                            $totalPrice += ($item['locked_price'] * $qty);
                            $totalWholesale += ($item['locked_wholesale'] * $qty);
                            if ($idx === 0) $firstProduct = $item;
                        }

                        $profit = $totalPrice - $totalWholesale;
                        $paymentMethod = $o['payment_method'] ?? 'Cash on Delivery';
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[14px] overflow-hidden shadow-sm border border-slate-100 <?= $tab === 'paid' ? 'bg-emerald-50 text-emerald-500' : 'bg-blue-50 text-blue-500' ?> flex items-center justify-center shrink-0">
                                    <i class="fa-solid <?= $tab === 'paid' ? 'fa-check-double' : 'fa-cart-flatbed' ?> text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-800 text-[15px]">#<?= $o['id'] ?> - <?= count($items) > 1 ? count($items) . ' Items' : htmlspecialchars($firstProduct['product_name'] ?? 'Unknown Item') ?></h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <?php if(count($items) === 1 && $firstProduct): ?>
                                            <span class="text-[11px] bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-md uppercase tracking-widest border border-slate-200">UID: <?= $firstProduct['product_uid'] ?></span>
                                        <?php else: ?>
                                            <span class="text-[11px] bg-brand-50 text-brand-600 font-bold px-2 py-0.5 rounded-md uppercase tracking-widest border border-brand-200">Multi-Item Cart</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[12px] text-slate-400 font-medium mt-1.5"><i class="fa-solid fa-calendar-day mr-1"></i> <?= date("d M, Y h:i A", strtotime($o['created_at'])) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <p class="font-extrabold text-slate-800 text-[14px]"><i class="fa-solid fa-user text-slate-400 w-4 text-center mr-1"></i> <?= htmlspecialchars($o['name']) ?></p>
                                <p class="text-slate-500 font-medium text-[13px]"><i class="fa-solid fa-phone text-slate-400 w-4 text-center mr-1"></i> <?= htmlspecialchars($o['phone']) ?></p>
                                <p class="text-slate-500 font-medium text-[13px] max-w-[200px] truncate" title="<?= htmlspecialchars($o['address']) ?>"><i class="fa-solid fa-location-dot text-slate-400 w-4 text-center mr-1"></i> <?= htmlspecialchars($o['city']) ?> - <?= htmlspecialchars($o['address']) ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2 min-w-[180px]">
                                <div class="flex items-center justify-between gap-6 text-[13px]">
                                    <span class="text-slate-400 font-bold">Total Price:</span>
                                    <span class="font-extrabold text-blue-600">৳<?= number_format($totalPrice, 2) ?></span>
                                </div>
                                <div class="flex items-center justify-between gap-6 text-[13px] mb-2">
                                    <span class="text-slate-400 font-bold">Profit:</span>
                                    <span class="font-extrabold text-brand-500">৳<?= number_format($profit, 2) ?></span>
                                </div>
                                
                                <div>
                                    <?php if(stripos($paymentMethod, 'bkash') !== false || stripos($paymentMethod, 'nagad') !== false || stripos($paymentMethod, 'paid') !== false || stripos($paymentMethod, 'sls') !== false): ?>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-200 tracking-wide">
                                            <i class="fa-solid fa-money-check-dollar mr-2 text-emerald-500"></i> <?= htmlspecialchars($paymentMethod) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-extrabold bg-orange-50 text-orange-600 border border-orange-200 tracking-wide">
                                            <i class="fa-solid fa-hand-holding-dollar mr-2 text-orange-500"></i> <?= htmlspecialchars($paymentMethod) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <form method="post" action="update_order_status" class="flex flex-col gap-2">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <div class="relative">
                                    <select name="status" class="w-36 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-bold text-slate-700 outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                                        <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>⏳ Pending</option>
                                        <option value="processing" <?= $o['status']=='processing'?'selected':'' ?>>⚙️ Processing</option>
                                        <option value="completed" <?= $o['status']=='completed'?'selected':'' ?>>✅ Completed</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                                <button type="submit" class="w-36 bg-slate-900 text-white text-[12px] font-bold py-2.5 rounded-xl hover:bg-brand-600 transition-colors shadow-sm">Update Status</button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="order_details?id=<?= $o['id'] ?>" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors border border-slate-200" title="View Details">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <form method="post" action="delete_order" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors border border-transparent" title="Delete">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center text-slate-300 mb-6 border border-slate-100">
                                    <i class="fa-solid <?= $tab === 'paid' ? 'fa-wallet' : 'fa-hand-holding-dollar' ?> text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-700 tracking-tight">No active orders here</h3>
                                <p class="text-slate-400 mt-2 font-medium">You currently have no pending or processing orders in this tab.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if($totalPages > 1): ?>
<div class="flex items-center justify-between px-4 mb-10">
    <p class="text-sm text-slate-500 font-medium">Showing <span class="font-extrabold text-slate-800"><?= min($totalOrders, $offset + 1) ?></span> to <span class="font-extrabold text-slate-800"><?= min($totalOrders, $offset + $perPage) ?></span> of <span class="font-extrabold text-slate-800"><?= $totalOrders ?></span></p>
    
    <div class="flex gap-2">
        <?php if($page > 1): ?>
            <a href="?tab=<?= $tab ?>&page=<?= $page-1 ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        <?php endif; ?>

        <?php for($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?tab=<?= $tab ?>&page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all <?= $i==$page ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-500 hover:text-brand-600 shadow-sm' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?tab=<?= $tab ?>&page=<?= $page+1 ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
