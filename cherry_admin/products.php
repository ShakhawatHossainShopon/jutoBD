<?php
include "../config/config.php";
$title = "Products Management";

// Get filter values
$filter_category = $_GET['category'] ?? '';
$filter_supplier = $_GET['supplier'] ?? '';
$search = $_GET['search'] ?? '';
$filter_uid = $_GET['uid'] ?? '';

// Pagination setup
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch categories and suppliers
$categories = $pdo->query("SELECT id, title FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Build SQL with filters
$where = [];
$params = [];
if ($filter_category) { $where[] = "p.category_id = ?"; $params[] = $filter_category; }
if ($filter_supplier) { $where[] = "p.supplier_id = ?"; $params[] = $filter_supplier; }
if ($search) { $where[] = "p.name LIKE ?"; $params[] = "%$search%"; }
if ($filter_uid) { $where[] = "p.uid LIKE ?"; $params[] = "%$filter_uid%"; }
$whereSQL = $where ? "WHERE ".implode(" AND ", $where) : "";

// Fetch products
$sql = "SELECT p.*, c.title AS category_name, s.name AS supplier_name,
        (SELECT COUNT(*) FROM orders WHERE product_id=p.id AND status='completed') as completed_orders
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN suppliers s ON p.supplier_id = s.id
        $whereSQL
        ORDER BY p.created_at DESC
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total rows & pages
$totalSQL = "SELECT COUNT(*) FROM products p $whereSQL";
$stmtTotal = $pdo->prepare($totalSQL);
$stmtTotal->execute($params);
$total = $stmtTotal->fetchColumn();
$pages = ceil($total / $limit);
$range = 2;

ob_start();
?>

<!-- Header & Actions -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Products</h1>
        <p class="text-slate-500 font-medium mt-1">Manage your store inventory</p>
    </div>
    
    <button onclick="openAddModal()" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center shrink-0">
        <i class="fa-solid fa-plus mr-2"></i> Add Product
    </button>
</div>

<!-- Filters -->
<div class="bg-white p-6 rounded-[32px] shadow-card border border-slate-100 mb-8">
    <form method="get" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Category</label>
            <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none text-slate-700 font-medium">
                <option value="">All Categories</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filter_category==$c['id']?'selected':'' ?>><?= $c['title'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Supplier</label>
            <select name="supplier" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none text-slate-700 font-medium">
                <option value="">All Suppliers</option>
                <?php foreach($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filter_supplier==$s['id']?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Search Name</label>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" placeholder="E.g. Nike Air" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none text-slate-700 font-medium" value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        
        <div class="w-32">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">UID</label>
            <input type="text" name="uid" placeholder="UID" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none text-slate-700 font-bold uppercase tracking-wider" value="<?= htmlspecialchars($filter_uid) ?>">
        </div>
        
        <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-slate-800 transition-colors h-[48px]">
            Filter
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
            <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-extrabold tracking-widest">
                <tr>
                    <th class="px-6 py-5">Product</th>
                    <th class="px-6 py-5">UID</th>
                    <th class="px-6 py-5">Category</th>
                    <th class="px-6 py-5">Pricing</th>
                    <th class="px-6 py-5 text-center">Orders</th>
                    <th class="px-6 py-5">Specs</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(count($rows) > 0): ?>
                    <?php foreach ($rows as $r): 
                        $images = !empty($r['images']) ? json_decode($r['images'], true) : [];
                        $firstImage = !empty($images) ? $images[0] : '';
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <a href="#" onclick="viewImages(this)" data-images='<?= htmlspecialchars(json_encode($images)) ?>' class="relative w-14 h-14 rounded-[16px] overflow-hidden shadow-sm border border-slate-100 block shrink-0 cursor-pointer group-hover:shadow-md transition-shadow bg-slate-50 flex items-center justify-center">
                                    <?php if($firstImage): ?>
                                        <img src="../<?= $firstImage ?>" class="w-full h-full object-cover">
                                        <?php if(count($images) > 1): ?>
                                        <div class="absolute bottom-0 right-0 bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-tl-lg">
                                            +<?= count($images)-1 ?>
                                        </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    <?php endif; ?>
                                </a>
                                <div>
                                    <a href="product_details?id=<?= $r['id'] ?>" class="font-extrabold text-slate-800 hover:text-brand-600 transition-colors text-[15px]"><?= htmlspecialchars($r['name']) ?></a>
                                    <p class="text-xs text-slate-400 font-bold mt-0.5 uppercase tracking-widest"><?= $r['supplier_name'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold font-mono tracking-widest border border-slate-200"><?= $r['uid'] ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-600 font-bold bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"><?= $r['category_name'] ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between gap-6 text-xs">
                                    <span class="text-slate-400 font-semibold uppercase tracking-wider">Whole</span>
                                    <span class="font-bold text-orange-500">৳<?= number_format($r['wholesale'], 2) ?></span>
                                </div>
                                <div class="flex items-center justify-between gap-6 text-xs">
                                    <span class="text-slate-400 font-semibold uppercase tracking-wider">Retail</span>
                                    <span class="font-bold text-blue-500">৳<?= number_format($r['price'], 2) ?></span>
                                </div>
                                <div class="flex items-center justify-between gap-6 text-xs mt-1 pt-1 border-t border-slate-100">
                                    <span class="text-slate-400 font-semibold uppercase tracking-wider">Profit</span>
                                    <span class="font-bold text-emerald-500">৳<?= number_format($r['profit'], 2) ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-[16px] <?= $r['completed_orders'] > 0 ? 'bg-brand-50 text-brand-600' : 'bg-slate-50 text-slate-400' ?> font-extrabold text-lg shadow-inner">
                                <?= $r['completed_orders'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-slate-500">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-4 text-slate-400"><i class="fa-solid fa-ruler"></i></span>
                                <?= htmlspecialchars($r['size'] ?: '-') ?>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 text-slate-400"><i class="fa-solid fa-palette"></i></span>
                                <?= htmlspecialchars($r['colors'] ?: '-') ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="edit_product?id=<?= $r['id'] ?>" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-500 hover:text-white hover:shadow-lg hover:shadow-blue-500/20 transition-all" title="Edit">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <a href="delete_product?id=<?= $r['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-500/20 transition-all" title="Delete">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-[24px] flex items-center justify-center text-slate-300 mb-5 border border-slate-100">
                                    <i class="fa-solid fa-box-open text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-700">No products found</h3>
                                <p class="text-slate-400 mt-2 font-medium">Adjust your filters or add a new product to your inventory.</p>
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
            <a href="?page=<?= $page-1 ?>&category=<?= $filter_category ?>&supplier=<?= $filter_supplier ?>&search=<?= urlencode($search) ?>&uid=<?= urlencode($filter_uid) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        <?php endif; ?>

        <?php for($p = max(1, $page - $range); $p <= min($pages, $page + $range); $p++): ?>
            <a href="?page=<?= $p ?>&category=<?= $filter_category ?>&supplier=<?= $filter_supplier ?>&search=<?= urlencode($search) ?>&uid=<?= urlencode($filter_uid) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all <?= $p==$page ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-500 hover:text-brand-600 shadow-sm' ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $pages): ?>
            <a href="?page=<?= $page+1 ?>&category=<?= $filter_category ?>&supplier=<?= $filter_supplier ?>&search=<?= urlencode($search) ?>&uid=<?= urlencode($filter_uid) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-colors shadow-sm">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Slide-over Add Product Modal -->
<div id="addModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0" id="addModalBackdrop" onclick="closeAddModal()"></div>
    
    <!-- Side Panel -->
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform translate-x-full transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1) flex flex-col" id="addModalContent">
        <div class="flex items-center justify-between p-8 border-b border-slate-100">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">New Product</h3>
                <p class="text-[13px] text-slate-500 font-medium mt-1">Add a new item to your inventory</p>
            </div>
            <button onclick="closeAddModal()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <form action="add_product" method="post" enctype="multipart/form-data" class="space-y-6" id="addProductForm">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="e.g. Premium Cotton T-Shirt" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
                
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all appearance-none">
                            <option value="">Select...</option>
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all appearance-none">
                            <option value="">Select...</option>
                            <?php foreach($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Wholesale <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">৳</span>
                            <input type="number" step="0.01" name="wholesale" placeholder="0.00" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Retail Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">৳</span>
                            <input type="number" step="0.01" name="price" placeholder="0.00" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Sizes <span class="text-red-500">*</span></label>
                        <input type="text" name="size" placeholder="S, M, L..." required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Colors <span class="text-red-500">*</span></label>
                        <input type="text" name="colors" placeholder="Red, Blue..." required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" rows="3" placeholder="Enter product details..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-medium transition-all custom-scrollbar"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Images <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:bg-brand-50 hover:border-brand-200 transition-colors relative group">
                        <input type="file" name="image[]" accept=".jpg,.jpeg,.png" multiple required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                        <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 text-brand-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </div>
                        <p class="text-[15px] font-bold text-slate-700">Click or drag images to upload</p>
                        <p class="text-[13px] text-slate-400 font-medium mt-1">JPG, PNG (Max 100KB each)</p>
                        <p id="fileNames" class="text-sm text-brand-600 font-bold mt-3 truncate"></p>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="p-6 border-t border-slate-100 bg-white">
            <div class="flex gap-4">
                <button type="button" onclick="closeAddModal()" class="flex-1 bg-slate-100 text-slate-600 py-4 rounded-2xl font-bold hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="submit" form="addProductForm" class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-brand-600 shadow-xl shadow-slate-900/10 hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all">Save Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" onclick="closeImageModal()"></div>
    <div class="relative z-10 bg-white p-8 rounded-[32px] shadow-2xl max-w-4xl w-[90%] max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800">Gallery</h3>
                <p class="text-slate-500 font-medium text-sm mt-1">Product Images</p>
            </div>
            <button onclick="closeImageModal()" class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div id="modalImages" class="flex-1 overflow-y-auto custom-scrollbar flex flex-wrap gap-6 justify-center">
            <!-- Images injected via JS -->
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
    
    // slight delay for transition
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
    }, 500); // Wait for transition
}

function updateFileName(input) {
    const nameEl = document.getElementById('fileNames');
    if(input.files.length > 0) {
        nameEl.innerHTML = `<span class="bg-brand-50 text-brand-600 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-check mr-2"></i> ${input.files.length} file(s) selected</span>`;
    } else {
        nameEl.innerHTML = '';
    }
}

// Image viewer logic
function viewImages(element) {
    let images = [];
    try {
        images = JSON.parse(element.dataset.images);
    } catch(e) {}
    
    const container = document.getElementById('modalImages');
    container.innerHTML = '';
    
    if(images && images.length > 0) {
        images.forEach(img => {
            const wrapper = document.createElement('div');
            wrapper.className = 'rounded-[24px] overflow-hidden shadow-card border border-slate-100 bg-slate-50 flex items-center justify-center p-2';
            
            const image = document.createElement('img');
            image.src = '../' + img;
            image.className = 'max-w-[250px] max-h-[250px] object-contain rounded-[16px]';
            image.onerror = function() { this.src='https://placehold.co/250x250?text=Error'; };
            
            wrapper.appendChild(image);
            container.appendChild(wrapper);
        });
    } else {
        container.innerHTML = '<div class="w-full text-center py-10"><i class="fa-solid fa-image text-4xl text-slate-300 mb-3"></i><p class="text-slate-400 font-bold">No images available</p></div>';
    }
    
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
