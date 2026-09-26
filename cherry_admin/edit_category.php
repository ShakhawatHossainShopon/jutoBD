<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: categories.php");
    exit;
}

// Fetch category
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    header("Location: categories.php");
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $des = $_POST['des'];

    // Handle image upload if new file provided
    $imagePath = $row['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid() . '.' . $ext;
        if (!is_dir('../uploads')) mkdir('../uploads', 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], "../" . $imagePath);
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE categories SET title=?, des=?, image=? WHERE id=?");
    $stmt->execute([$title, $des, $imagePath, $id]);

    header("Location: categories.php");
    exit;
}
$title = "Edit Category - " . htmlspecialchars($row['title']);
ob_start();
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="categories.php" class="text-slate-400 hover:text-brand-500 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
            <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Edit Category</h1>
        </div>
        <p class="text-slate-500 font-medium">Update details for <?= htmlspecialchars($row['title']) ?></p>
    </div>
</div>

<div class="bg-white rounded-[32px] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 p-8 max-w-2xl">
    <form method="post" enctype="multipart/form-data" class="space-y-6">
        
        <div class="space-y-6">
            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Category Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all" required>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Description</label>
                <textarea name="des" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-medium transition-all resize-y" required><?= htmlspecialchars($row['des']) ?></textarea>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-2">Category Image</label>
                
                <?php if(!empty($row['image'])): ?>
                <div class="mb-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase mb-2">Current Image:</p>
                    <div class="w-32 h-32 rounded-2xl overflow-hidden border border-slate-200">
                        <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Current Image" class="w-full h-full object-cover">
                    </div>
                </div>
                <?php endif; ?>

                <div class="relative group">
                    <input type="file" name="image" id="imageInput" class="hidden" accept="image/*">
                    <label for="imageInput" class="w-full flex items-center justify-center gap-3 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl px-5 py-8 cursor-pointer group-hover:bg-brand-50 group-hover:border-brand-200 group-hover:text-brand-600 transition-colors text-slate-500 font-medium">
                        <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                        <span>Click to upload a new image (optional)</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="pt-6 border-t border-slate-100 flex justify-end gap-4">
            <a href="categories.php" class="px-8 py-4 rounded-2xl font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 transition-colors">Cancel</a>
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
