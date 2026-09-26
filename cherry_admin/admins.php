<?php
include "../config/config.php";
$title = "Admin Accounts";
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit;
}

// Add Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $check = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
        $_SESSION['msg'] = "Username already exists.";
        $_SESSION['msg_type'] = "error";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        if($stmt->execute([$username, $password])) {
            $_SESSION['msg'] = "Admin account created successfully!";
            $_SESSION['msg_type'] = "success";
        }
    }
    header("Location: admins");
    exit;
}

// Delete Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if ($_POST['delete_id'] == $_SESSION['admin_id']) {
        $_SESSION['msg'] = "You cannot delete your own active account.";
        $_SESSION['msg_type'] = "error";
    } else {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id=?");
        if($stmt->execute([$_POST['delete_id']])) {
            $_SESSION['msg'] = "Admin account deleted!";
            $_SESSION['msg_type'] = "success";
        }
    }
    header("Location: admins");
    exit;
}

// Fetch all admins
$admins = $pdo->query("SELECT * FROM admins ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight">Admin Accounts</h1>
        <p class="text-slate-500 font-medium mt-1">Manage system administrators and their access</p>
    </div>
    
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-brand-600 text-white px-6 py-3 rounded-2xl text-[15px] font-bold hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all flex items-center">
        <i class="fa-solid fa-user-plus mr-2"></i> Add Admin
    </button>
</div>

<!-- Admins Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach($admins as $a): 
        $isMe = ($a['id'] == $_SESSION['admin_id']);
    ?>
    <div class="bg-white rounded-[32px] shadow-card border <?= $isMe ? 'border-brand-200 shadow-brand-500/5' : 'border-slate-100' ?> overflow-hidden p-6 relative group hover:-translate-y-1 transition-all duration-300">
        <?php if($isMe): ?>
            <div class="absolute top-0 right-0 bg-brand-500 text-white text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-bl-2xl">You</div>
        <?php endif; ?>
        
        <div class="flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-[24px] <?= $isMe ? 'bg-brand-100 text-brand-600' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center font-extrabold text-3xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                <?= strtoupper(substr($a['username'], 0, 1)) ?>
            </div>
            
            <h3 class="text-xl font-extrabold text-slate-800 mb-1"><?= htmlspecialchars($a['username']) ?></h3>
            <p class="text-[12px] text-slate-400 font-bold uppercase tracking-widest mb-6">Admin ID: #<?= $a['id'] ?></p>
            
            <?php if(!$isMe): ?>
                <form method="post" class="w-full m-0" onsubmit="return confirm('Are you sure you want to revoke this admin\'s access?');">
                    <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                    <button type="submit" class="w-full bg-red-50 text-red-500 font-bold py-3 rounded-2xl hover:bg-red-500 hover:text-white transition-all text-sm flex items-center justify-center border border-red-100 hover:border-red-500">
                        <i class="fa-solid fa-trash mr-2"></i> Revoke Access
                    </button>
                </form>
            <?php else: ?>
                <div class="w-full bg-slate-50 text-slate-400 font-bold py-3 rounded-2xl text-sm border border-slate-100 flex items-center justify-center cursor-not-allowed">
                    <i class="fa-solid fa-shield-halved mr-2"></i> Active Session
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-xl font-extrabold text-slate-800 flex items-center"><i class="fa-solid fa-user-shield text-brand-500 mr-3"></i> Create Admin</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form method="post" class="p-6 space-y-6">
            <input type="hidden" name="add" value="1">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Username</label>
                <input type="text" name="username" placeholder="e.g. jsmith_admin" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Secure Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none text-slate-800 font-bold transition-all">
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white font-extrabold py-4 rounded-2xl hover:bg-brand-600 transition-all shadow-xl shadow-slate-900/10 flex items-center justify-center">
                <i class="fa-solid fa-check mr-2"></i> Create Account
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
