<?php
include "../config/config.php";
$title = "Dashboard";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit;
}

// -------------------------------------------------------------
// 1. Filter Logic
// -------------------------------------------------------------
$filter = $_GET['filter'] ?? 'all';
$dateCondition = "";

if ($filter === 'today') {
    $dateCondition = "AND DATE(o.created_at) = CURDATE()";
} elseif ($filter === 'week') {
    $dateCondition = "AND YEARWEEK(o.created_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $dateCondition = "AND MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
} elseif ($filter === 'year') {
    $dateCondition = "AND YEAR(o.created_at) = YEAR(CURDATE())";
}

// -------------------------------------------------------------
// 2. Metrics Calculation
// -------------------------------------------------------------
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders o WHERE o.status='completed' $dateCondition")->fetchColumn();

$financials = $pdo->query("
    SELECT SUM(oi.locked_wholesale * oi.quantity) AS total_wholesale,
           SUM(oi.locked_price * oi.quantity) AS total_sold
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.status='completed' $dateCondition
")->fetch(PDO::FETCH_ASSOC);

$totalWholesale = $financials['total_wholesale'] ?: 0;
$totalSold = $financials['total_sold'] ?: 0;
$profit = $totalSold - $totalWholesale;

$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders o WHERE o.status IN ('pending', 'processing') $dateCondition")->fetchColumn();

// -------------------------------------------------------------
// 3. Best Selling Products Table
// -------------------------------------------------------------
$bestSelling = $pdo->query("
    SELECT p.name, p.uid, SUM(oi.quantity) as total_sold_qty, SUM(oi.locked_price * oi.quantity) as total_revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    WHERE o.status = 'completed' $dateCondition
    GROUP BY p.id
    ORDER BY total_sold_qty DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// -------------------------------------------------------------
// 4. Recent Completed Orders
// -------------------------------------------------------------
$recentOrders = $pdo->query("
    SELECT o.id, o.name, o.created_at, o.payment_method, SUM(oi.locked_price * oi.quantity) as total_amount
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.status = 'completed' $dateCondition
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// -------------------------------------------------------------
// 5. Chart Data (Analytics)
// -------------------------------------------------------------
// Revenue Last 7 Days
$chartDates = [];
$chartRevenues = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartDates[] = date('M d', strtotime($date));
    $rev = $pdo->query("SELECT SUM(oi.locked_price * oi.quantity) FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.status='completed' AND DATE(o.created_at) = '$date'")->fetchColumn();
    $chartRevenues[] = $rev ?: 0;
}

// Supplier Market Share (Pie Chart)
$supplierSales = $pdo->query("
    SELECT s.name, SUM(oi.locked_price * oi.quantity) as total
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    JOIN suppliers s ON p.supplier_id = s.id
    WHERE o.status = 'completed'
    GROUP BY s.id
    ORDER BY total DESC
    LIMIT 4
")->fetchAll(PDO::FETCH_ASSOC);

$pieLabels = [];
$pieData = [];
foreach($supplierSales as $s) {
    $pieLabels[] = $s['name'];
    $pieData[] = $s['total'];
}

ob_start();
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Header & Filter Section -->
<div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-10 gap-6">
    <div>
        <p class="text-brand-500 font-bold mb-1 text-sm tracking-widest uppercase">Analytics Overview</p>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Store Dashboard</h1>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200 flex text-sm font-bold">
            <a href="?filter=today" class="px-5 py-2.5 rounded-xl transition-all <?= $filter==='today' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' ?>">Today</a>
            <a href="?filter=week" class="px-5 py-2.5 rounded-xl transition-all <?= $filter==='week' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' ?>">This Week</a>
            <a href="?filter=month" class="px-5 py-2.5 rounded-xl transition-all <?= $filter==='month' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' ?>">This Month</a>
            <a href="?filter=year" class="px-5 py-2.5 rounded-xl transition-all <?= $filter==='year' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' ?>">This Year</a>
            <a href="?filter=all" class="px-5 py-2.5 rounded-xl transition-all <?= $filter==='all' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' ?>">All Time</a>
        </div>
        <a href="add_order" class="bg-brand-600 text-white px-5 py-3 rounded-2xl text-[14px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center h-[46px]">
            <i class="fa-solid fa-plus mr-2"></i> Create Order
        </a>
    </div>
</div>

<!-- Financial Metrics Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Profit -->
    <div class="bg-slate-900 rounded-[32px] p-8 relative overflow-hidden shadow-card group">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-brand-500 rounded-full mix-blend-screen filter blur-[50px] opacity-40 group-hover:opacity-60 transition-opacity"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center border border-white/10 mb-6 text-white text-xl">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <p class="text-slate-400 text-[12px] font-bold mb-1 uppercase tracking-widest">Net Profit</p>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">৳<?= number_format($profit) ?></h2>
        </div>
    </div>

    <!-- Revenue -->
    <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl mb-6">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <p class="text-slate-400 text-[12px] font-bold mb-1 uppercase tracking-widest">Gross Revenue</p>
        <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight">৳<?= number_format($totalSold) ?></h3>
    </div>

    <!-- Wholesale Cost -->
    <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 text-xl mb-6">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <p class="text-slate-400 text-[12px] font-bold mb-1 uppercase tracking-widest">Wholesale Cost</p>
        <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight">৳<?= number_format($totalWholesale) ?></h3>
    </div>

    <!-- Volume Stats (Orders) -->
    <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50 mb-3 border border-emerald-100">
            <div>
                <p class="text-emerald-600/70 text-[11px] font-bold uppercase tracking-widest">Completed</p>
                <h4 class="text-xl font-extrabold text-emerald-700"><?= number_format($totalOrders) ?></h4>
            </div>
            <i class="fa-solid fa-circle-check text-2xl text-emerald-400"></i>
        </div>
        <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50 border border-amber-100">
            <div>
                <p class="text-amber-600/70 text-[11px] font-bold uppercase tracking-widest">Pending</p>
                <h4 class="text-xl font-extrabold text-amber-700"><?= number_format($pendingOrders) ?></h4>
            </div>
            <i class="fa-solid fa-clock text-2xl text-amber-400"></i>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Line Chart: Revenue -->
    <div class="lg:col-span-2 bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-extrabold text-slate-800"><i class="fa-solid fa-chart-line text-brand-500 mr-2"></i> Revenue Trend (Last 7 Days)</h3>
        </div>
        <div class="relative h-72 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Pie Chart: Supplier Share -->
    <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-extrabold text-slate-800"><i class="fa-solid fa-chart-pie text-purple-500 mr-2"></i> Supplier Market Share</h3>
        </div>
        <div class="relative h-64 w-full flex items-center justify-center">
            <?php if(!empty($pieData)): ?>
                <canvas id="supplierChart"></canvas>
            <?php else: ?>
                <p class="text-slate-400 font-medium">No sales data available</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <!-- Best Selling Products -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-[17px] font-extrabold text-slate-800"><i class="fa-solid fa-fire text-orange-500 mr-2"></i> Best-Selling Products</h3>
        </div>
        <div class="p-4 flex-1">
            <?php if($bestSelling): ?>
                <div class="space-y-3">
                    <?php foreach($bestSelling as $idx => $p): 
                        $badgeColors = ['bg-amber-100 text-amber-600', 'bg-slate-200 text-slate-500', 'bg-orange-100 text-orange-700', 'bg-blue-50 text-blue-500', 'bg-slate-100 text-slate-400'];
                        $badge = $badgeColors[$idx] ?? 'bg-slate-100 text-slate-400';
                    ?>
                    <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full <?= $badge ?> flex items-center justify-center font-extrabold text-sm shrink-0">
                                #<?= $idx + 1 ?>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-800 text-[14px] leading-tight mb-0.5"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">UID: <?= $p['uid'] ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-extrabold text-brand-600 text-[15px]">৳<?= number_format($p['total_revenue']) ?></p>
                            <p class="text-[12px] font-bold text-slate-500"><?= $p['total_sold_qty'] ?> Units</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="h-full flex flex-col items-center justify-center py-10 text-slate-400">
                    <i class="fa-solid fa-box-open text-3xl mb-3"></i>
                    <p class="font-medium">No sales data for this period.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-[17px] font-extrabold text-slate-800"><i class="fa-solid fa-bolt text-yellow-500 mr-2"></i> Recent Completed Orders</h3>
            <a href="all_orders" class="text-[12px] font-bold text-brand-600 hover:text-brand-700">View All</a>
        </div>
        <div class="p-4 flex-1">
            <?php if($recentOrders): ?>
                <div class="space-y-3">
                    <?php foreach($recentOrders as $o): ?>
                    <a href="order_details?id=<?= $o['id'] ?>" class="block">
                        <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-800 text-[14px] leading-tight mb-0.5"><?= htmlspecialchars($o['name']) ?></p>
                                    <p class="text-[11px] font-bold text-slate-400">Order #<?= $o['id'] ?> &bull; <?= date("M d, h:i A", strtotime($o['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-extrabold text-slate-700 text-[14px]">৳<?= number_format($o['total_amount']) ?></p>
                                <?php if(stripos($o['payment_method'], 'cash') !== false): ?>
                                    <span class="text-[10px] font-bold text-orange-500 uppercase tracking-widest">COD</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Paid</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="h-full flex flex-col items-center justify-center py-10 text-slate-400">
                    <i class="fa-solid fa-receipt text-3xl mb-3"></i>
                    <p class="font-medium">No completed orders for this period.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Render Charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared styling variables
    const brandColor = '#4f46e5';
    const brandColorLight = 'rgba(79, 70, 229, 0.1)';
    const gridColor = '#f1f5f9';
    
    // 1. Revenue Line Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartDates) ?>,
            datasets: [{
                label: 'Gross Revenue (৳)',
                data: <?= json_encode($chartRevenues) ?>,
                borderColor: brandColor,
                backgroundColor: brandColorLight,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: brandColor,
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    titleFont: { family: 'Plus Jakarta Sans', size: 13 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
                    displayColors: false,
                    callbacks: {
                        label: function(context) { return '৳' + context.parsed.y.toLocaleString(); }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 12 }, color: '#64748b' }
                },
                y: {
                    grid: { color: gridColor, drawBorder: false },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 12 },
                        color: '#64748b',
                        callback: function(value) { return '৳' + (value >= 1000 ? (value/1000) + 'k' : value); },
                        maxTicksLimit: 6
                    },
                    beginAtZero: true
                }
            }
        }
    });

    <?php if(!empty($pieData)): ?>
    // 2. Supplier Pie Chart
    const pieCtx = document.getElementById('supplierChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($pieLabels) ?>,
            datasets: [{
                data: <?= json_encode($pieData) ?>,
                backgroundColor: [
                    '#4f46e5', // Brand
                    '#0ea5e9', // Light Blue
                    '#10b981', // Emerald
                    '#f59e0b', // Amber
                    '#8b5cf6'  // Purple
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        color: '#475569',
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    bodyFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
                    callbacks: {
                        label: function(context) { return ' ৳' + context.parsed.toLocaleString(); }
                    }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
