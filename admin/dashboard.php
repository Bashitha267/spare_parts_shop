<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');

// Fetch Metrics
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
    <title>Admin Dashboard - Oil POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
        }
        .bg-main {
            background:  url('public/admin_background.jpg');
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
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <!-- Sidebar / Nav -->
    <nav class="glass-nav fixed w-full z-30 top-0 text-slate-900 font-black">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Dashboard</h1>
            
            <div class="flex items-center gap-4 md:gap-8">
                <!-- Metrics Badges (Aligned Right) -->
                <div class="hidden lg:flex items-center gap-4">
                    <div class="px-5 py-2.5 bg-indigo-50 border-2 border-indigo-200 rounded-2xl flex flex-col items-end shadow-sm">
                        <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest leading-none mb-1.5">Total Inventory</span>
                        <span class="text-sm font-black text-indigo-800 leading-none">Rs. <?php echo number_format($total_inventory, 2); ?></span>
                    </div>
                    <div class="px-5 py-2.5 bg-emerald-50 border-2 border-emerald-200 rounded-2xl flex flex-col items-end shadow-sm">
                        <span class="text-[8px] font-black text-emerald-400 uppercase tracking-widest leading-none mb-1.5">Today's Sales</span>
                        <span class="text-sm font-black text-emerald-800 leading-none">Rs. <?php echo number_format($today_sales, 2); ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-l border-slate-200 pl-8">
                    <div class="hidden sm:block text-right">
                        <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest leading-none mb-1">Administrator</p>
                        <p class="text-xs font-bold text-slate-900"><?php echo $_SESSION['full_name']; ?></p>
                    </div>
                    <a href="../logout.php" class="text-xs font-black text-white bg-red-600 border-2 border-white px-5 py-2.5 rounded-xl hover:bg-red-700 hover:shadow-lg transition-all uppercase tracking-wider">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-24 px-6 max-w-7xl mx-auto space-y-10 animate-fade">
        

        <!-- Quick Actions -->
        <div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">


                <!-- Manage Oil Inventory -->
                <a href="manage.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Oil Inventory</span>
                </a>

                <!-- Manage Spare Parts Inventory -->
                <a href="manage_spare.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Spare Parts Inventory</span>
                </a>

                <!-- Manage Cashiers -->
                <a href="manage_cashiers.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Staff Management</span>
                </a>

                <!-- Batch History -->
                <a href="batchHistroy.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Added Item Logs</span>
                </a>

                <!-- Manage Payments -->
                <a href="manage_payments.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10 flex flex-col items-center">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM sales WHERE payment_status = 'pending'");
                        $pending_count = $stmt->fetchColumn();
                        if($pending_count > 0):
                        ?>
                        <span class="absolute -top-4 -right-4 bg-white text-blue-600 text-[10px] font-black w-6 h-6 flex items-center justify-center rounded-full ring-4 ring-blue-600"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Cheque and Credits Approvals</span>
                </a>

                <!-- Reports -->
                <a href="reports.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Reports</span>
                </a>

                <!-- Sales History -->
                <a href="sales_history.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Sales History</span>
                </a>

                <!-- Payments Approval History -->
                <a href="payment_history.php" class="p-8 blue-gradient-card rounded-[2.5rem] group relative overflow-hidden">
                    <div class="mb-5 group-hover:rotate-12 transition-transform relative z-10">
                        <svg class="w-12 h-12 icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em] text-center relative z-10">Payments Approval History</span>
                </a>
            </div>
        </div>
    </div>
    <script>
        function showCompatibility(p) {
            const types = p.vehicle_compatibility || 'Universal Application';
            Swal.fire({
                title: p.name,
                html: `
                    <div class="text-left mt-4 space-y-4">
                        <div class="p-5 bg-slate-50 border border-slate-100 rounded-3xl">
                            <div class="flex justify-between items-center mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recommended Vehicles</p>
                                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black uppercase text-slate-500 shadow-sm">${p.type}</span>
                            </div>
                            <p class="text-base font-black text-slate-800 leading-tight mb-4">${types}</p>
                            <div class="flex items-center gap-2 py-2 px-3 bg-blue-50/50 rounded-xl w-fit">
                                <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Brand</span>
                                <span class="text-xs font-black text-blue-700">${p.brand || 'General'}</span>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    container: 'backdrop-blur-sm'
                }
            });
        }
    </script>
</body>
</html>
