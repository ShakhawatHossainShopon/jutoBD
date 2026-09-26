<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: suppliers.php");
    exit;
}

// Fetch supplier
$supplier = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
$supplier->execute([$id]);
$row = $supplier->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    header("Location: suppliers.php");
    exit;
}

// If form submitted, update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $des = $_POST['des'];
    $phone = $_POST['phone'];
    $website = $_POST['website'];

    $stmt = $pdo->prepare("UPDATE suppliers SET name=?, des=?, phone=?, website=? WHERE id=?");
    $stmt->execute([$name, $des, $phone, $website, $id]);

    header("Location: suppliers.php");
    exit;
}
$title = "Edit Supplier - " . htmlspecialchars($row['name']);
ob_start();
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="suppliers.php" class="text-slate-400 hover:text-brand-500 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
            <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Edit Supplier</h1>
        </div>
        <p class="text-slate-500 font-medium">Update details for <?= htmlspecialchars($row['name']) ?></p>
    </div>
</div>

<div class="bg-white rounded-[32px] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 p-8 max-w-2xl">
    <form method="post" class="space-y-6">
        
        <div class="space-y-6">
            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Supplier Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Website</label>
                    <input type="text" name="website" value="<?= htmlspecialchars($row['website']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
                </div>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Description</label>
                <textarea name="des" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-medium transition-all resize-y"><?= htmlspecialchars($row['des']) ?></textarea>
            </div>
        </div>
        
        <div class="pt-6 border-t border-slate-100 flex justify-end gap-4">
            <a href="suppliers.php" class="px-8 py-4 rounded-2xl font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 transition-colors">Cancel</a>
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
