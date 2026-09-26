<?php
session_start();
include "../config/config.php";

$title = "Discounts Management";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM discounts WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: discounts.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $discount_percent = $_POST['discount_percent'];
    $end_date = $_POST['end_date'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE discounts SET product_id=?, discount_percent=?, end_date=? WHERE id=?");
        $stmt->execute([$product_id, $discount_percent, $end_date, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO discounts (product_id, discount_percent, end_date) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE discount_percent=VALUES(discount_percent), end_date=VALUES(end_date)");
        $stmt->execute([$product_id, $discount_percent, $end_date]);
    }
    header("Location: discounts.php");
    exit;
}

$stmt = $pdo->query("SELECT d.*, p.name as product_name, p.price, p.images FROM discounts d JOIN products p ON d.product_id = p.id ORDER BY d.end_date ASC");
$discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$products = $pdo->query("SELECT id, name, price FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Discounts</h1>
        <p class="text-slate-500 font-medium mt-1">Manage product sales and countdown timers</p>
    </div>
    <button onclick="openModal()" class="bg-brand-500 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-600 transition-colors flex items-center shadow-lg shadow-brand-500/30">
        <i class="fa-solid fa-plus mr-3"></i> Add Sale
    </button>
</div>

<!-- Discounts List -->
<div class="bg-white rounded-[32px] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 p-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px]">Product</th>
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px]">Base Price</th>
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px]">Discount</th>
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px]">Sale Price</th>
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px]">Ends At</th>
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px]">Status</th>
                    <th class="pb-4 font-bold text-slate-400 uppercase tracking-widest text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($discounts as $d): 
                    $salePrice = $d['price'] - ($d['price'] * ($d['discount_percent'] / 100));
                    $imgs = json_decode($d['images'], true);
                    $img = (!empty($imgs) && is_array($imgs)) ? str_replace('\/', '/', $imgs[0]) : "https://placehold.co/100x100";
                    $isExpired = strtotime($d['end_date']) < time();
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                                <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover">
                            </div>
                            <span class="font-bold text-slate-800 text-[14px]"><?= htmlspecialchars($d['product_name']) ?></span>
                        </div>
                    </td>
                    <td class="py-4 font-bold text-slate-500">?<?= number_format($d['price'], 2) ?></td>
                    <td class="py-4 font-extrabold text-brand-600">
                        <span class="bg-brand-50 px-2 py-1 rounded"><?= $d['discount_percent'] ?>%</span>
                    </td>
                    <td class="py-4 font-bold text-slate-800">?<?= number_format($salePrice, 2) ?></td>
                    <td class="py-4">
                        <span class="text-[13px] font-medium text-slate-600">
                            <?= date('d M Y, h:i A', strtotime($d['end_date'])) ?>
                        </span>
                    </td>
                    <td class="py-4">
                        <?php if ($isExpired): ?>
                            <span class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[11px] font-bold uppercase tracking-widest">Expired</span>
                        <?php else: ?>
                            <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-lg text-[11px] font-bold uppercase tracking-widest">Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 text-right">
                        <button onclick="editModal(<?= $d['id'] ?>, <?= $d['product_id'] ?>, <?= $d['discount_percent'] ?>, '<?= date('Y-m-d\TH:i', strtotime($d['end_date'])) ?>')" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-colors inline-flex items-center justify-center mr-1">
                            <i class="fa-solid fa-pen text-[13px]"></i>
                        </button>
                        <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Remove discount?')" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-500 transition-colors inline-flex items-center justify-center">
                            <i class="fa-solid fa-trash text-[13px]"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($discounts)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 font-medium">No active discounts found. Add one above!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="saleModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center">
    <div class="bg-white rounded-[32px] w-full max-w-md p-8 shadow-2xl scale-95 opacity-0 transition-all duration-200" id="modalContent">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-[24px] font-extrabold text-slate-900" id="modalTitle">Add Sale</h2>
            <button type="button" onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form method="POST" id="saleForm">
            <input type="hidden" name="id" id="form_id">
            
            <div class="space-y-5">
                <div>
                    <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Select Product</label>
                    <select name="product_id" id="form_product" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
                        <option value="">-- Choose Product --</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (?<?= $p['price'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Discount Percentage (%)</label>
                    <input type="number" step="1" min="1" max="99" name="discount_percent" id="form_percent" placeholder="e.g. 10" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sale End Date & Time</label>
                    <input type="datetime-local" name="end_date" id="form_end" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
                </div>
                
                <button type="submit" class="w-full bg-brand-500 text-white font-bold text-[15px] py-4 rounded-2xl hover:bg-brand-600 transition-colors shadow-lg shadow-brand-500/30 mt-4">
                    Save Discount
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('form_id').value = '';
        document.getElementById('saleForm').reset();
        document.getElementById('modalTitle').innerText = 'Add Sale';
        document.getElementById('saleModal').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('modalContent').classList.remove('scale-95', 'opacity-0');
            document.getElementById('modalContent').classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeModal() {
        document.getElementById('modalContent').classList.remove('scale-100', 'opacity-100');
        document.getElementById('modalContent').classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            document.getElementById('saleModal').classList.add('hidden');
        }, 200);
    }

    function editModal(id, product, percent, end) {
        openModal();
        document.getElementById('modalTitle').innerText = 'Edit Sale';
        document.getElementById('form_id').value = id;
        document.getElementById('form_product').value = product;
        document.getElementById('form_percent').value = percent;
        document.getElementById('form_end').value = end;
    }
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
