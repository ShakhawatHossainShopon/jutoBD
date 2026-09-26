<?php
include "../config/config.php";
$title = "Suppliers Management";

// Pagination setup
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch suppliers
$rows = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

// Total rows & pages
$total = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$pages = ceil($total / $limit);
$range = 2; // pages before/after current

// Start output buffering
ob_start();
?>

<!-- Header & Actions -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Suppliers</h1>
        <p class="text-slate-500 font-medium mt-1">Manage your vendors and supply chain</p>
    </div>
    
    <button onclick="openAddModal()" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center shrink-0">
        <i class="fa-solid fa-plus mr-2"></i> Add Supplier
    </button>
</div>

<!-- Table -->
<div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
            <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-extrabold tracking-widest">
                <tr>
                    <th class="px-6 py-5 w-16">No.</th>
                    <th class="px-6 py-5">Supplier Info</th>
                    <th class="px-6 py-5">Contact Details</th>
                    <th class="px-6 py-5 w-1/3">Description</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(count($rows) > 0): ?>
                    <?php foreach ($rows as $i => $r): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 font-bold text-slate-400">
                            <?= str_pad($offset + $i + 1, 2, '0', STR_PAD_LEFT) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[14px] overflow-hidden shadow-sm border border-slate-100 bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-truck-field text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-800 text-[15px] group-hover:text-brand-600 transition-colors"><?= htmlspecialchars($r['name']) ?></h3>
                                    <span class="text-xs text-slate-400 font-bold font-mono tracking-widest mt-0.5 block">ID: <?= $r['id'] ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 text-center text-slate-400"><i class="fa-solid fa-phone"></i></span>
                                    <span class="font-bold text-slate-700"><?= htmlspecialchars($r['phone'] ?: 'N/A') ?></span>
                                </div>
                                <?php if(!empty($r['website'])): ?>
                                <div class="flex items-center gap-2">
                                    <span class="w-5 text-center text-slate-400"><i class="fa-solid fa-globe"></i></span>
                                    <a href="<?= (strpos($r['website'], 'http') !== 0 ? 'http://' : '') . htmlspecialchars($r['website']) ?>" target="_blank" class="font-bold text-blue-500 hover:underline"><?= htmlspecialchars($r['website']) ?></a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-slate-500 font-medium truncate max-w-sm" title="<?= htmlspecialchars($r['des']) ?>">
                                <?= htmlspecialchars($r['des'] ?: 'No description provided.') ?>
                            </p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="edit_supplier?id=<?= $r['id'] ?>" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-500 hover:text-white hover:shadow-lg hover:shadow-blue-500/20 transition-all" title="Edit">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <a href="delete_supplier?id=<?= $r['id'] ?>" onclick="return confirm('Are you sure you want to delete this supplier?')" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-500/20 transition-all" title="Delete">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-[24px] flex items-center justify-center text-slate-300 mb-5 border border-slate-100">
                                    <i class="fa-solid fa-truck-field text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-700">No suppliers found</h3>
                                <p class="text-slate-400 mt-2 font-medium">Add a supplier to start sourcing products.</p>
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
            <a href="?page=<?= $page-1 ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        <?php endif; ?>

        <?php for($p = max(1, $page - $range); $p <= min($pages, $page + $range); $p++): ?>
            <a href="?page=<?= $p ?>" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all <?= $p==$page ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-500 hover:text-brand-600 shadow-sm' ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $pages): ?>
            <a href="?page=<?= $page+1 ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Slide-over Add Supplier Modal -->
<div id="addModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0" id="addModalBackdrop" onclick="closeAddModal()"></div>
    
    <!-- Side Panel -->
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1) flex flex-col" id="addModalContent">
        <div class="flex items-center justify-between p-8 border-b border-slate-100">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">New Supplier</h3>
                <p class="text-[13px] text-slate-500 font-medium mt-1">Add a new vendor to your list</p>
            </div>
            <button onclick="closeAddModal()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <form action="add_suppliers" method="post" class="space-y-6" id="addSupplierForm">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Company / Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="e.g. Acme Corp" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" placeholder="e.g. +880 1642..." required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Website</label>
                    <input type="text" name="website" placeholder="e.g. www.supplier.com" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Description / Notes</label>
                    <textarea name="des" rows="4" placeholder="Important details about this supplier..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-medium transition-all custom-scrollbar"></textarea>
                </div>
            </form>
        </div>
        
        <div class="p-6 border-t border-slate-100 bg-white">
            <div class="flex gap-4">
                <button type="button" onclick="closeAddModal()" class="flex-1 bg-slate-100 text-slate-600 py-4 rounded-2xl font-bold hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="submit" form="addSupplierForm" class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-brand-600 shadow-xl shadow-slate-900/10 hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all">Save Supplier</button>
            </div>
        </div>
    </div>
</div>

<script>
// Slide-over Modal logic
function openAddModal() {
    const modal = document.getElementById('addModal');
    const backdrop = document.getElementById('addModalBackdrop');
    const content = document.getElementById('addModalContent');
    
    modal.classList.remove('hidden');
    
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        content.classList.remove('translate-x-full');
    }, 10);
}

function closeAddModal() {
    const backdrop = document.getElementById('addModalBackdrop');
    const content = document.getElementById('addModalContent');
    
    backdrop.classList.add('opacity-0');
    content.classList.add('translate-x-full');
    
    setTimeout(() => {
        document.getElementById('addModal').classList.add('hidden');
    }, 500); 
}
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
