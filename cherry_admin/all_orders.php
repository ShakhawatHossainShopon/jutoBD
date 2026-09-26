<?php
include "../config/config.php";
$title = "Completed Orders";

// Get filter values
$search_phone = $_GET['phone'] ?? '';

// We can no longer filter purely by 'supplier' directly on orders table since an order can have multiple suppliers.
// But we can filter by matching order_items.
$search_supplier = $_GET['supplier'] ?? '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build SQL with filters (only completed orders)
$where = ["o.status = 'completed'"];
$params = [];

if ($search_phone) {
    $where[] = "o.phone LIKE ?";
    $params[] = "%$search_phone%";
}

$joinSql = "";
if ($search_supplier) {
    // If supplier is filtered, we only want orders that contain at least one item from this supplier
    $joinSql = "JOIN order_items oi ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id";
    $where[] = "p.supplier_id = ?";
    $params[] = $search_supplier;
}

$whereSQL = "WHERE " . implode(" AND ", $where);

// Total rows & pages
if ($search_supplier) {
    $totalSQL = "SELECT COUNT(DISTINCT o.id) FROM orders o $joinSql $whereSQL";
} else {
    $totalSQL = "SELECT COUNT(*) FROM orders o $whereSQL";
}
$stmtTotal = $pdo->prepare($totalSQL);
$stmtTotal->execute($params);
$total = $stmtTotal->fetchColumn();
$pages = ceil($total / $limit);
$range = 2; // for pagination UI

// Fetch orders (Parent level)
if ($search_supplier) {
    $sql = "SELECT DISTINCT o.* FROM orders o $joinSql $whereSQL ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";
} else {
    $sql = "SELECT o.* FROM orders o $whereSQL ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch suppliers for filter dropdown
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Completed Orders</h1>
        <p class="text-slate-500 font-medium mt-1">History of all successfully fulfilled orders</p>
    </div>
    
    <div class="bg-white px-5 py-3 rounded-2xl shadow-card border border-slate-100 flex items-center text-[15px] font-bold text-emerald-500">
        <i class="fa-solid fa-check-circle mr-2.5"></i>
        <?= $total ?> Completed Orders
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-6 rounded-[32px] shadow-card border border-slate-100 mb-8">
    <form method="get" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Customer Phone</label>
            <div class="relative">
                <i class="fa-solid fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="phone" placeholder="e.g. 017..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" value="<?= htmlspecialchars($search_phone) ?>">
            </div>
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Supplier</label>
            <div class="relative">
                <select name="supplier" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all appearance-none cursor-pointer">
                    <option value="">All Suppliers</option>
                    <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $search_supplier == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
            </div>
        </div>
        
        <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-extrabold hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 h-[58px] flex items-center justify-center">
            Filter Results
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-[32px] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
            <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-extrabold tracking-widest">
                <tr>
                    <th class="px-6 py-5">Order Info</th>
                    <th class="px-6 py-5">Items Summary</th>
                    <th class="px-6 py-5">Customer Details</th>
                    <th class="px-6 py-5">Financials & Payment</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if($orders): ?>
                    <?php foreach($orders as $o): 
                        // Fetch cart items for this order
                        $iStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.uid as product_uid, s.name as supplier_name FROM order_items oi JOIN products p ON oi.product_id = p.id LEFT JOIN suppliers s ON p.supplier_id = s.id WHERE oi.order_id = ?");
                        $iStmt->execute([$o['id']]);
                        $items = $iStmt->fetchAll(PDO::FETCH_ASSOC);

                        $itemCount = 0;
                        $totalPrice = 0;
                        $firstProduct = null;

                        foreach($items as $idx => $item) {
                            $qty = $item['quantity'];
                            $itemCount += $qty;
                            $totalPrice += ($item['locked_price'] * $qty);
                            if ($idx === 0) $firstProduct = $item;
                        }
                        
                        $paymentMethod = $o['payment_method'] ?? 'Cash on Delivery';
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[14px] overflow-hidden shadow-sm border border-slate-100 bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-check text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-800 text-[15px]">#<?= $o['id'] ?></h3>
                                    <p class="text-xs text-slate-400 font-bold mt-1 tracking-wide"><i class="fa-solid fa-calendar-check mr-1"></i> <?= date("d M, Y h:i A", strtotime($o['created_at'])) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <?php if(count($items) === 1 && $firstProduct): ?>
                                    <p class="font-extrabold text-slate-800 text-[14px]"><i class="fa-solid fa-box text-slate-400 w-4 text-center mr-1.5"></i> <?= htmlspecialchars($firstProduct['product_name']) ?></p>
                                    <p class="text-[11px] bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-md uppercase tracking-widest border border-slate-200 inline-block w-max mb-1">UID: <?= $firstProduct['product_uid'] ?></p>
                                    <p class="text-slate-500 font-medium text-[13px]"><i class="fa-solid fa-truck-field text-slate-400 w-4 text-center mr-1.5"></i> <?= htmlspecialchars($firstProduct['supplier_name']) ?></p>
                                <?php else: ?>
                                    <p class="font-extrabold text-slate-800 text-[14px]"><i class="fa-solid fa-boxes-stacked text-slate-400 w-4 text-center mr-1.5"></i> <?= count($items) ?> Different Products</p>
                                    <p class="text-[11px] bg-brand-50 text-brand-600 font-bold px-2 py-0.5 rounded-md uppercase tracking-widest border border-brand-200 inline-block w-max mb-1">Multi-Item Cart</p>
                                    <p class="text-slate-500 font-medium text-[13px]"><i class="fa-solid fa-hashtag text-slate-400 w-4 text-center mr-1.5"></i> Total <?= $itemCount ?> Units</p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <p class="font-extrabold text-slate-800 text-[14px]"><i class="fa-solid fa-user text-slate-400 w-4 text-center mr-1"></i> <?= htmlspecialchars($o['name']) ?></p>
                                <p class="text-slate-500 font-medium text-[13px]"><i class="fa-solid fa-phone text-slate-400 w-4 text-center mr-1"></i> <?= htmlspecialchars($o['phone']) ?></p>
                                <p class="text-slate-500 font-medium text-[13px]"><i class="fa-solid fa-hashtag text-slate-400 w-4 text-center mr-1"></i> Total Qty: <span class="font-bold"><?= $itemCount ?></span></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2 min-w-[150px]">
                                <div class="flex items-center justify-between gap-6 text-[13px]">
                                    <span class="text-slate-400 font-bold">Total:</span>
                                    <span class="font-extrabold text-slate-800 text-lg">৳<?= number_format($totalPrice, 2) ?></span>
                                </div>
                                <div class="mt-1">
                                    <?php if(stripos($paymentMethod, 'bkash') !== false || stripos($paymentMethod, 'nagad') !== false || stripos($paymentMethod, 'paid') !== false || stripos($paymentMethod, 'sls') !== false): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-green-50 text-green-600 border border-green-200 tracking-wide uppercase">
                                            <i class="fa-solid fa-check-double mr-1.5 text-green-500"></i> <?= htmlspecialchars($paymentMethod) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200 tracking-wide uppercase">
                                            <i class="fa-solid fa-handshake mr-1.5"></i> <?= htmlspecialchars($paymentMethod) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="post" action="update_order_status" class="inline m-0" onsubmit="return confirm('Are you sure you want to undo this completion and move the order back to Processing?');">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <input type="hidden" name="status" value="processing">
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-500 hover:bg-orange-500 hover:text-white transition-all shadow-sm border border-orange-100 hover:scale-110" title="Undo Completion">
                                        <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                    </button>
                                </form>
                                <a href="order_details?id=<?= $o['id'] ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-200 hover:border-slate-900 hover:scale-105" title="View Details">
                                    <i class="fa-solid fa-arrow-right -rotate-45"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center text-slate-300 mb-6 border border-slate-100">
                                    <i class="fa-solid fa-list-check text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-700 tracking-tight">No completed orders found</h3>
                                <p class="text-slate-400 mt-2 font-medium">Try adjusting your filters or checking the active order board.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if($pages > 1): ?>
<div class="flex items-center justify-between px-4 mb-10">
    <p class="text-sm text-slate-500 font-medium">Showing <span class="font-extrabold text-slate-800"><?= min($total, $offset + 1) ?></span> to <span class="font-extrabold text-slate-800"><?= min($total, $offset + $limit) ?></span> of <span class="font-extrabold text-slate-800"><?= $total ?></span></p>
    
    <div class="flex gap-2">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&phone=<?= urlencode($search_phone) ?>&supplier=<?= $search_supplier ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        <?php endif; ?>

        <?php for($p = max(1, $page - $range); $p <= min($pages, $page + $range); $p++): ?>
            <a href="?page=<?= $p ?>&phone=<?= urlencode($search_phone) ?>&supplier=<?= $search_supplier ?>" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all <?= $p==$page ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-500 hover:text-brand-600 shadow-sm' ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $pages): ?>
            <a href="?page=<?= $page+1 ?>&phone=<?= urlencode($search_phone) ?>&supplier=<?= $search_supplier ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
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
