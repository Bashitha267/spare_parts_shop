<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('cashier');

// Fetch Metrics for Header
$inv_stmt = $pdo->query("SELECT SUM(current_qty * buying_price) FROM batches");
$total_inventory = $inv_stmt->fetchColumn() ?: 0;

$sales_stmt = $pdo->query("SELECT SUM(final_amount) FROM sales WHERE DATE(created_at) = CURDATE()");
$today_sales = $sales_stmt->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Hub - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
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
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: 3px solid #ffffff;
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .blue-gradient-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.5);
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        .icon-svg {
            color: white;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-main min-h-screen relative overflow-x-hidden">
    <div class="colorful-overlay"></div>
    
    <!-- Hub Header -->
    <nav class="glass-nav fixed w-full z-30 top-0 text-slate-900 font-black">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-500/20">V</div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Cashier Hub</h1>
            </div>
            
            <div class="flex items-center gap-4 md:gap-8">
                <!-- Metrics Badges (Aligned Right) -->
                <div class="hidden lg:flex items-center gap-4">
                    <div class="px-5 py-2 bg-indigo-50 border-2 border-indigo-200 rounded-2xl flex flex-col items-end shadow-sm">
                        <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest leading-none mb-1">Total Inventory</span>
                        <span class="text-xs font-black text-indigo-800 leading-none">Rs. <?php echo number_format($total_inventory, 2); ?></span>
                    </div>
                    <div class="px-5 py-2 bg-emerald-50 border-2 border-emerald-200 rounded-2xl flex flex-col items-end shadow-sm">
                        <span class="text-[8px] font-black text-emerald-400 uppercase tracking-widest leading-none mb-1">Today's Sales</span>
                        <span class="text-xs font-black text-emerald-800 leading-none">Rs. <?php echo number_format($today_sales, 2); ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-l border-slate-200 pl-8">
                    <div class="hidden sm:block text-right">
                        <p class="text-[10px] text-slate-500 uppercase font-black text-white tracking-widest leading-none mb-1">Operator</p>
                        <p class="text-xs font-bold text-slate-900"><?php echo $_SESSION['full_name']; ?></p>
                    </div>
                    <a href="../logout.php" class="text-xs font-black text-white bg-red-600 border-2 border-white px-5 py-2.5 rounded-xl hover:bg-red-700 hover:shadow-lg transition-all uppercase tracking-wider">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 px-8 max-w-7xl mx-auto relative z-10 animate-fade">
        <div class="mb-16">
            <h2 class="text-4xl font-white text-white tracking-tighter mb-2 font-bold">Good Day, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <!-- POS Card -->
            <a href="pos.php" class="p-10 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden text-center">
                <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                    <svg class="w-14 h-14 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-sm font-black text-white uppercase tracking-[0.2em] relative z-10">Point of Sale</h3>
            </a>

            <!-- History Card -->
            <a href="history.php" class="p-10 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden text-center">
                <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                    <svg class="w-14 h-14 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-sm font-black text-white uppercase tracking-[0.2em] relative z-10">Sales History</h3>
            </a>

            <!-- Oil Registry Card -->
            <a href="../admin/manage.php" class="p-10 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden text-center">
                <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                    <svg class="w-14 h-14 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.675.337a4 4 0 01-2.574.345l-3.113-.623a4 4 0 01-2.574-.345l-.675-.337a6 6 0 00-3.86-.517l-2.387.477a2 2 0 00-1.022.547V21a1 1 0 001 1h12a1 1 0 001-1v-5.572zM12 11V5.05a.5.5 0 01.09-.3l3.29-4.41a.5.5 0 01.82 0l3.29 4.41c.06.08.09.18.09.3V11h-7.5z"/></svg>
                </div>
                <h3 class="text-sm font-black text-white uppercase tracking-[0.2em] relative z-10">Oil Registry</h3>
            </a>

            <!-- Spare Parts Card -->
            <a href="../admin/manage_spare.php" class="p-10 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden text-center">
                <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                    <svg class="w-14 h-14 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </div>
                <h3 class="text-sm font-black text-white uppercase tracking-[0.2em] relative z-10">Spare Parts</h3>
            </a>

            <!-- Arrival History Card -->
            <a href="../admin/batchHistroy.php" class="p-10 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden text-center">
                <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                    <svg class="w-14 h-14 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h3 class="text-sm font-black text-white uppercase tracking-[0.2em] relative z-10">Arrival History</h3>
            </a>
        </div>
    </main>

    <footer class="text-center py-12 relative z-10 border-t border-slate-200 mx-8">
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400">© 2026 VEHICLE SQUARE • PREMIUM OPS SYSTEM</p>
    </footer>
</body>
</html>
