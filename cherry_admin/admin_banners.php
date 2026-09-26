<?php
include "../config/config.php"; // DB connection
$title = "Website Banners";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit;
}

// Handle upload
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner'])) {
    $file = $_FILES['banner'];
    if($file['size'] > 102400) { // 100 KB limit
        $_SESSION['msg'] = "File size must be less than 100KB.";
        $_SESSION['msg_type'] = "error";
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'uploads/banners/' . uniqid() . '.' . $ext;
        
        // Ensure directory exists
        if (!is_dir('../uploads/banners')) {
            mkdir('../uploads/banners', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], '../' . $filename)) {
            $stmt = $pdo->prepare("INSERT INTO banners (image) VALUES (?)");
            $stmt->execute([$filename]);
            $_SESSION['msg'] = "Banner uploaded successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Failed to save file.";
            $_SESSION['msg_type'] = "error";
        }
    }
    header("Location: admin_banners");
    exit;
}

// Handle delete
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("SELECT image FROM banners WHERE id=?");
    $stmt->execute([$id]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    if($banner) {
        @unlink('../' . $banner['image']); // delete file
        $stmt = $pdo->prepare("DELETE FROM banners WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['msg'] = "Banner deleted successfully!";
        $_SESSION['msg_type'] = "success";
    }
    header("Location: admin_banners");
    exit;
}

// Fetch all banners
$banners = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Website Banners</h1>
        <p class="text-slate-500 font-medium mt-1">Manage the hero images displayed on your frontend</p>
    </div>
    
    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center">
        <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Banner
    </button>
</div>

<!-- Banners Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
    <?php if($banners): ?>
        <?php foreach($banners as $b): ?>
        <div class="bg-white rounded-[32px] shadow-card border border-slate-100 overflow-hidden group">
            <div class="relative aspect-[21/9] bg-slate-100 overflow-hidden">
                <img src="../<?= htmlspecialchars($b['image']) ?>" alt="Banner <?= $b['id'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                
                <!-- Hover Overlay -->
                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px]">
                    <form method="post" class="m-0" onsubmit="return confirm('Delete this banner from the website?');">
                        <input type="hidden" name="delete_id" value="<?= $b['id'] ?>">
                        <button type="submit" class="w-14 h-14 rounded-2xl bg-red-500 text-white flex items-center justify-center hover:bg-red-600 hover:scale-110 transition-all shadow-xl" title="Delete Banner">
                            <i class="fa-solid fa-trash text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-5 flex items-center justify-between border-t border-slate-100 bg-slate-50/50">
                <p class="text-[12px] font-bold text-slate-400 uppercase tracking-widest">Banner ID: #<?= $b['id'] ?></p>
                <div class="flex items-center text-[12px] font-bold text-emerald-500 bg-emerald-50 px-2.5 py-1 rounded-lg">
                    <i class="fa-solid fa-circle text-[8px] mr-1.5"></i> Live
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full py-24 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center text-slate-300 mb-6 border border-slate-100">
                    <i class="fa-solid fa-image text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-700 tracking-tight">No banners uploaded</h3>
                <p class="text-slate-400 mt-2 font-medium">Upload your first promotional banner to display on the store.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-xl font-extrabold text-slate-800 flex items-center"><i class="fa-solid fa-panorama text-brand-500 mr-3"></i> Upload Banner</h3>
            <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form method="post" enctype="multipart/form-data" class="p-6 space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Select Image (Max 100KB)</label>
                <div class="relative">
                    <input type="file" name="banner" accept="image/*" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100">
                </div>
            </div>
            
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3 text-amber-700 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <p>For best results, use a <strong>21:9 aspect ratio</strong> image under 100KB to ensure fast website loading.</p>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white font-extrabold py-4 rounded-2xl hover:bg-brand-600 transition-all shadow-xl shadow-slate-900/10 flex items-center justify-center">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload & Publish
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
