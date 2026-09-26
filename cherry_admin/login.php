<?php
include "../config/config.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        $_SESSION['msg'] = "Welcome back to JutoBD!";
        $_SESSION['msg_type'] = "success";
        
        header("Location: index");
        exit;
    } else {
        $error = "Authentication failed. Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JutoBD | Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .bg-image {
            background-image: url('https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=2012&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen bg-image flex items-center justify-center p-4 relative selection:bg-red-500 selection:text-white">

    <!-- Dark overlay to make the login box pop -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Login Box -->
    <div class="glass-card w-full max-w-[420px] rounded-3xl shadow-2xl relative z-10 overflow-hidden">
        
        <div class="p-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-red-600 mb-4 shadow-sm">
                    <i class="fa-solid fa-shoe-prints text-2xl -rotate-45"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">JutoBD</h1>
                <p class="text-slate-500 mt-2 font-medium">Admin Workspace Login</p>
            </div>

            <!-- Error Message -->
            <?php if($error): ?>
            <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl text-sm font-bold mb-6 flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= $error ?></span>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="post" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Username</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="username" placeholder="Enter username" required autofocus
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none text-slate-800 font-bold transition-all placeholder:font-medium placeholder:text-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="password" placeholder="••••••••" required 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none text-slate-800 font-bold transition-all placeholder:font-medium placeholder:text-slate-400">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-red-600 text-white font-extrabold py-4 rounded-xl hover:bg-red-700 transition-all shadow-lg shadow-red-500/30 flex items-center justify-center">
                        Secure Login <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer Stripe -->
        <div class="bg-slate-50 py-4 text-center border-t border-slate-100">
            <p class="text-xs font-bold text-slate-400">JutoBD &copy; <?= date('Y') ?>. All rights reserved.</p>
        </div>

    </div>

</body>
</html>
