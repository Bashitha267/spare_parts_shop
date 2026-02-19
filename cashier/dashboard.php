<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('cashier');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Hub - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #0d1117;
            color: #e6edf3;
        }
        .bg-main {
            background: url('../admin/public/admin_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .blue-gradient-card {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(30, 64, 175, 0.8) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        .glass-nav {
            background: rgba(13, 17, 23, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .hub-card:hover { transform: translateY(-8px) scale(1.02); }
    </style>
</head>
<body class="bg-main min-h-screen relative overflow-x-hidden">
    <div class="colorful-overlay"></div>
    
    <!-- Hub Header -->
    <header class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 py-5 flex justify-between items-center">
            <div class="flex items-center gap-5">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-blue-500/20">V</div>
                <h1 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Cashier Hub</h1>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-[10px] text-blue-300/40 font-black uppercase tracking-[0.2em]">Session Active</p>
                    <p class="text-sm font-bold text-blue-100"><?php echo $_SESSION['full_name']; ?></p>
                </div>
                <a href="../logout.php" class="p-3 bg-red-500/10 text-red-400 hover:text-white hover:bg-red-500 rounded-2xl transition-all border border-red-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-8 py-20 relative z-10">
        <div class="text-center mb-20">
            <h2 class="text-5xl font-black text-white tracking-tighter mb-6">Good Day, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- POS Card -->
            <a href="pos.php" class="hub-card group relative blue-gradient-card p-10 rounded-[3rem] transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-blue-400/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:bg-blue-400/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center text-white mb-8 shadow-2xl shadow-blue-500/20 group-hover:rotate-12 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-3"> POS</h3>
                    <p class="text-sm text-blue-200/60 font-medium leading-relaxed">Scan items and generate customer invoices instantly.</p>
                </div>
            </a>

            <!-- History Card -->
            <a href="history.php" class="hub-card group relative blue-gradient-card p-10 rounded-[3rem] transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-400/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:bg-indigo-400/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-indigo-500 rounded-2xl flex items-center justify-center text-white mb-8 shadow-2xl shadow-indigo-500/20 group-hover:rotate-12 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-3">Sales History</h3>
                    <p class="text-sm text-blue-200/60 font-medium leading-relaxed">Review past sales</p>
                </div>
            </a>

            <!-- Add Batches Card -->
            <a href="../admin/addItems.php" class="hub-card group relative blue-gradient-card p-10 rounded-[3rem] transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-400/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:bg-emerald-400/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mb-8 shadow-2xl shadow-emerald-500/20 group-hover:rotate-12 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-3">Add Stock </h3>
                    <p class="text-sm text-blue-200/60 font-medium leading-relaxed">Add items to inventory</p>
                </div>
            </a>
        </div>
    </main>

    <footer class="text-center py-12">
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-blue-300/20">© 2026 VEHICLE SQUARE • PREMIUM OPS SYSTEM</p>
    </footer>
</body>
</html>
