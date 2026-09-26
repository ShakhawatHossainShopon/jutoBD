<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit;
}
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? "Admin Dashboard"; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '#16a34a',
                            900: '#14532d',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 0px 20px 0px rgba(0,0,0,0.03)',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F8FAFC; color: #334155; }
        .sidebar-link { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-link:hover { transform: translateX(5px); }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden font-sans selection:bg-brand-500 selection:text-white">

    <!-- Sidebar -->
    <aside class="w-[280px] bg-white border-r border-slate-100 flex flex-col transition-all duration-300 z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)] relative">
        <div class="h-20 flex items-center px-8 border-b border-slate-50">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-emerald-400 flex items-center justify-center shadow-lg shadow-brand-500/30 text-white">
                <i class="fa-solid fa-store text-lg"></i>
            </div>
            <h1 class="ml-3 text-xl font-bold tracking-tight text-slate-800">JUTO<span class="text-brand-500">BD</span></h1>
        </div>
        
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 custom-scrollbar">
            <div class="px-4 pb-2 text-xs font-bold text-slate-400 uppercase tracking-widest">Menu</div>
            <?php
            $navItems = [
                'index' => ['Dashboard', 'fa-chart-pie'],
                'products' => ['Products', 'fa-box-open'],
                'categories' => ['Categories', 'fa-layer-group'],
                'suppliers' => ['Suppliers', 'fa-truck-field'],
                'discounts' => ['Discounts', 'fa-percent'],
            ];
            $navItems2 = [
                'order_board' => ['Orders Board', 'fa-clipboard-list'],
                'all_orders' => ['All Orders', 'fa-list'],
                'add_order' => ['Create Orders', 'fa-cart-plus'],
            ];
            $navItems3 = [
                'accounts' => ['Reports', 'fa-file-invoice-dollar'],
                'admins' => ['Admin', 'fa-user-shield'],
                'admin_banners' => ['Banners', 'fa-image']
            ];

            function renderNav($items, $current) {
                foreach ($items as $link => $item) {
                    $name = $item[0];
                    $icon = $item[1];
                    $isActive = ($current === $link);
                    $activeClass = $isActive 
                        ? 'bg-brand-50 text-brand-600 font-bold shadow-sm ring-1 ring-brand-100' 
                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 font-medium';
                    $iconClass = $isActive ? 'text-brand-500' : 'text-slate-400 group-hover:text-slate-600';
                    
                    echo "<a href=\"{$link}\" class=\"group sidebar-link flex items-center px-4 py-3 rounded-xl {$activeClass} mb-1\">
                            <i class=\"fa-solid {$icon} w-6 text-[17px] mr-3 transition-colors {$iconClass}\"></i>
                            <span class=\"text-[14.5px]\">{$name}</span>
                          </a>";
                }
            }
            renderNav($navItems, $currentPage);
            echo '<div class="px-4 pt-8 pb-2 text-xs font-bold text-slate-400 uppercase tracking-widest">Sales</div>';
            renderNav($navItems2, $currentPage);
            echo '<div class="px-4 pt-8 pb-2 text-xs font-bold text-slate-400 uppercase tracking-widest">Settings</div>';
            renderNav($navItems3, $currentPage);
            ?>
        </div>
        
        <div class="p-4 border-t border-slate-50">
            <a href="logout" class="sidebar-link flex items-center justify-center px-4 py-3 text-slate-500 hover:bg-red-50 hover:text-red-600 rounded-xl font-bold transition-colors">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-[#F8FAFC]">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 left-0 w-full h-72 bg-gradient-to-b from-brand-500/5 to-transparent -z-10"></div>
        <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-brand-400/10 rounded-full blur-[80px] -z-10"></div>
        
        <!-- Top Navbar -->
        <header class="h-20 glass-panel flex items-center justify-between px-10 z-10 sticky top-0">
            <div class="flex items-center">
                <h2 class="text-[22px] font-bold text-slate-800 tracking-tight hidden sm:block"><?php echo $title ?? "Dashboard"; ?></h2>
            </div>
            
            <div class="flex items-center space-x-6">
                <!-- Search (Visual Only) -->
                <div class="hidden md:flex items-center bg-white px-4 py-2.5 rounded-2xl shadow-sm border border-slate-100 hover:border-brand-200 transition-all w-72 focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500">
                    <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Search anything..." class="bg-transparent border-none outline-none ml-3 text-sm w-full text-slate-700 placeholder-slate-400 font-medium">
                </div>
                
                <!-- Notification -->
                <button class="relative p-2 text-slate-400 hover:text-brand-500 transition-colors bg-white rounded-xl shadow-sm border border-slate-100 w-10 h-10 flex items-center justify-center">
                    <i class="fa-regular fa-bell text-lg"></i>
                    <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>
                
                <div class="h-8 w-px bg-slate-200"></div>
                
                <!-- Profile -->
                <button class="flex items-center gap-3 text-left group">
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=22c55e&color=fff&bold=true" alt="Admin" class="w-11 h-11 rounded-full ring-4 ring-white shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all">
                        <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full ring-2 ring-white"></span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-bold text-slate-800 leading-tight">Admin User</p>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Super Admin</p>
                    </div>
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-10 relative">
            <div class="max-w-[1400px] mx-auto animate-[fadeIn_0.5s_ease-out]">
                <!-- Legacy class overrides to prevent old pages from breaking -->
                <style>
                    .container { @apply bg-white p-8 rounded-[24px] shadow-card border border-slate-100 mt-0 mx-0 w-full mb-8 !important; }
                    table { @apply w-full text-sm text-left text-slate-600 rounded-2xl overflow-hidden shadow-sm border border-slate-100 !important; }
                    th { @apply bg-slate-50 text-slate-700 font-bold px-6 py-4 border-b border-slate-200 uppercase tracking-wider text-xs !important; }
                    td { @apply px-6 py-4 border-b border-slate-50 font-medium !important; }
                    tr:hover td { @apply bg-slate-50/50 !important; }
                    .button-primary, a.button-primary { @apply inline-flex items-center justify-center bg-slate-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-brand-600 hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all duration-300 !important; }
                    .input, .textarea { @apply w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-3.5 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none my-2 text-slate-800 font-medium !important; }
                    h2 { @apply text-[26px] font-extrabold text-slate-800 mb-8 tracking-tight !important; }
                </style>
                
                <?php echo $content; ?>
            </div>
        </main>
        
    </div>

    <!-- Toast Notification System -->
    <?php if (isset($_SESSION['msg'])): ?>
    <?php 
        $msgType = $_SESSION['msg_type'] ?? 'success';
        $iconClass = $msgType === 'success' ? 'fa-circle-check text-brand-500' : 'fa-circle-exclamation text-red-500';
        $bgClass = $msgType === 'success' ? 'bg-white border-brand-100' : 'bg-white border-red-100';
    ?>
    <div id="toastNotification" class="fixed bottom-8 right-8 z-[200] transform transition-all duration-500 translate-y-20 opacity-0 flex items-center gap-4 px-6 py-4 rounded-2xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.1)] border <?= $bgClass ?>">
        <i class="fa-solid <?= $iconClass ?> text-3xl"></i>
        <div class="pr-6">
            <h4 class="font-extrabold text-slate-800 text-[15px]"><?= $msgType === 'success' ? 'Success!' : 'Error' ?></h4>
            <p class="text-slate-500 text-[13px] font-bold mt-0.5"><?= htmlspecialchars($_SESSION['msg']) ?></p>
        </div>
        <button onclick="closeToast()" class="absolute top-2 right-2 w-8 h-8 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toastNotification');
            if(toast) {
                toast.classList.remove('translate-y-20', 'opacity-0');
            }
        }, 100);
        
        function closeToast() {
            const toast = document.getElementById('toastNotification');
            if(toast) {
                toast.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }
        
        setTimeout(closeToast, 4000);
    </script>
    <?php 
        unset($_SESSION['msg']);
        unset($_SESSION['msg_type']);
    endif; ?>

</body>
</html>
