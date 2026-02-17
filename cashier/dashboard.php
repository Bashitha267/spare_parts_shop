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
        body { font-family: 'Inter', sans-serif; }
        .hub-card:hover { transform: translateY(-8px); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <!-- Hub Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-200">V</div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase">Cashier Hub</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Logged In as</p>
                    <p class="text-sm font-bold text-slate-800"><?php echo $_SESSION['full_name']; ?></p>
                </div>
                <a href="../logout.php" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-8 py-16">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-slate-800 tracking-tighter mb-4">Good Day, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!</h2>
            <p class="text-slate-500 font-medium">What would you like to do today?</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- POS Card -->
            <a href="pos.php" class="hub-card group relative bg-white p-8 rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-100 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-blue-100 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-xl shadow-blue-200 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">New Sale</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">Scan items and generate customer invoices instantly.</p>
                </div>
            </a>

            <!-- History Card -->
            <a href="history.php" class="hub-card group relative bg-white p-8 rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-100 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-indigo-100 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-xl shadow-indigo-200 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">Sales History</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">Review past transactions and reprint previous bills.</p>
                </div>
            </a>

            <!-- Add Batches Card -->
            <a href="../admin/addItems.php" class="hub-card group relative bg-white p-8 rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-100 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50/50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-100 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-xl shadow-emerald-200 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">Add Batches</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">Record incoming stock and update product availability.</p>
                </div>
            </a>
        </div>
    </main>

    <footer class="text-center py-8 opacity-30">
        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">© 2026 Vehicle Square • POS System</p>
    </footer>
</body>
</html>
