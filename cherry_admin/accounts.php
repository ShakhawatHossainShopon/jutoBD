<?php
include "../config/config.php";
$title = "Supplier Accounts & Report";

// Fetch report data grouped by supplier using the multi-cart order_items logic
$sql = "SELECT s.id AS supplier_id, s.name AS supplier_name,
               COUNT(DISTINCT o.id) AS total_orders,
               SUM(oi.locked_wholesale * oi.quantity) AS total_wholesale,
               SUM(oi.locked_price * oi.quantity) AS total_sold,
               SUM((oi.locked_price - oi.locked_wholesale) * oi.quantity) AS profit,
               SUM(oi.quantity) AS total_units_sold
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN suppliers s ON p.supplier_id = s.id
        WHERE o.status='completed'
        GROUP BY s.id
        ORDER BY profit DESC";
$report = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Supplier Accounts</h1>
        <p class="text-slate-500 font-medium mt-1">Track financial performance and profit by supplier</p>
    </div>
    
    <div class="flex gap-4">
        <button onclick="window.print()" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center print-hide">
            <i class="fa-solid fa-print mr-2"></i> Print Report
        </button>
    </div>
</div>

<div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
            <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-extrabold tracking-widest">
                <tr>
                    <th class="px-6 py-5">Supplier Partner</th>
                    <th class="px-6 py-5 text-center">Volume</th>
                    <th class="px-6 py-5 text-right">Wholesale Cost</th>
                    <th class="px-6 py-5 text-right">Gross Revenue</th>
                    <th class="px-6 py-5 text-right">Net Profit</th>
                    <th class="px-6 py-5 text-center print-hide">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if($report): ?>
                    <?php foreach($report as $r): 
                        // Determine profit color
                        $profitColor = $r['profit'] > 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50';
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[14px] bg-blue-50 text-blue-500 flex items-center justify-center font-extrabold text-lg shrink-0">
                                    <?= strtoupper(substr($r['supplier_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-800 text-[15px]"><?= htmlspecialchars($r['supplier_name']) ?></h3>
                                    <p class="text-xs text-slate-400 font-bold mt-1 tracking-wide">ID: SUP-<?= str_pad($r['supplier_id'], 4, '0', STR_PAD_LEFT) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-extrabold text-slate-800 text-[15px]"><?= $r['total_orders'] ?> <span class="text-xs text-slate-400 font-medium">Orders</span></span>
                                <span class="text-[11px] bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-md uppercase tracking-widest"><?= $r['total_units_sold'] ?> Units</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right font-bold text-orange-500">
                            ৳<?= number_format($r['total_wholesale'], 2) ?>
                        </td>
                        <td class="px-6 py-5 text-right font-extrabold text-blue-600">
                            ৳<?= number_format($r['total_sold'], 2) ?>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[14px] font-extrabold <?= $profitColor ?>">
                                ৳<?= number_format($r['profit'], 2) ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center print-hide">
                            <a href="all_orders?supplier=<?= $r['supplier_id'] ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-200 hover:border-slate-900 group-hover:scale-105" title="View Orders">
                                <i class="fa-solid fa-arrow-right -rotate-45"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center text-slate-300 mb-6 border border-slate-100">
                                    <i class="fa-solid fa-chart-pie text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-700 tracking-tight">No data available</h3>
                                <p class="text-slate-400 mt-2 font-medium">Complete some orders to see financial reports.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
