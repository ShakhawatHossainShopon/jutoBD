<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid Product ID");

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header("Location: products.php");
    exit;
}

// Fetch categories & suppliers for dropdown
$categories = $pdo->query("SELECT id, title FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $price = $_POST['price'];
    $wholesale = $_POST['wholesale'];
    $size = $_POST['size'];
    $description = $_POST['description'];
    $colors = $_POST['colors'];

    // calculate profit automatically
    $profit = $price - $wholesale;

    // Update product
    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, supplier_id=?, price=?, wholesale=?, size=?, description=?, colors=?, profit=? WHERE id=?");
    $stmt->execute([$name, $category_id, $supplier_id, $price, $wholesale, $size, $description, $colors, $profit, $id]);

    header("Location: products.php");
    exit;
}

$title = "Edit Product - " . htmlspecialchars($product['name']);
ob_start();
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="products.php" class="text-slate-400 hover:text-brand-500 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
            <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Edit Product</h1>
        </div>
        <p class="text-slate-500 font-medium">Update details for <?= htmlspecialchars($product['name']) ?></p>
    </div>
</div>

<div class="bg-white rounded-[32px] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 p-8 max-w-4xl">
    <form method="post" class="space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="md:col-span-2">
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Product Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Category</label>
                <div class="relative">
                    <select name="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 appearance-none focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Supplier</label>
                <div class="relative">
                    <select name="supplier_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 appearance-none focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $product['supplier_id']==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <!-- Pricing -->
            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Wholesale Cost (?)</label>
                <input type="number" step="0.01" name="wholesale" value="<?= $product['wholesale'] ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Retail Price (?)</label>
                <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
            </div>

            <!-- Variants -->
            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Available Sizes</label>
                <input type="text" name="size" value="<?= htmlspecialchars($product['size']) ?>" placeholder="e.g. 40, 41, 42, 43" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                <p class="text-[11px] text-slate-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i>Comma separated sizes</p>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Available Colors</label>
                <input type="text" name="colors" value="<?= htmlspecialchars($product['colors']) ?>" placeholder="e.g. Black, Brown, Tan" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                <p class="text-[11px] text-slate-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i>Comma separated colors</p>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Product Description</label>
                <textarea name="description" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-medium transition-all resize-y"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
        </div>
        
        <div class="pt-6 border-t border-slate-100 flex justify-end gap-4">
            <a href="products.php" class="px-8 py-4 rounded-2xl font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 transition-colors">Cancel</a>
            <button type="submit" class="px-8 py-4 rounded-2xl font-bold text-white bg-brand-500 hover:bg-brand-600 transition-colors shadow-lg shadow-brand-500/30 flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
