<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');
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
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <!-- Sidebar / Nav -->
    <nav class="bg-white border-b border-slate-200 fixed w-full z-10 top-0">
        <div class="px-4 md:px-6 py-3 flex justify-between items-center max-w-7xl mx-auto">
            <h1 class="text-lg md:text-xl font-bold text-slate-800">Vehicle <span class="text-blue-600">Square</span></h1>
            <div class="flex items-center gap-2 md:gap-4">
                <span class="hidden sm:inline-block text-xs md:text-sm text-slate-500"><?php echo $_SESSION['full_name']; ?></span>
                <a href="../logout.php" class="text-xs md:text-sm font-semibold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <div class="pt-24 px-6 max-w-7xl mx-auto space-y-10 animate-fade">
        

        <!-- Quick Actions -->
        <div>
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Management Systems</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Add Items / New Stock -->
                <a href="addItems.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-blue-600 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Add Items / New Stock</span>
                </a>

                <!-- Manage Oil Inventory -->
                <a href="manage.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-amber-600 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700 text-center">Manage Oil Inventory</span>
                </a>

                <!-- Manage Spare Parts Inventory -->
                <a href="manage_spare.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-blue-600 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700 text-center">Manage Spare Parts Inventory</span>
                </a>

                <!-- Manage Cashiers -->
                <a href="manage_cashiers.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-emerald-500 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Manage Cashiers</span>
                </a>

                <!-- Batch History -->
                <a href="batchHistroy.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-slate-800 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700 text-center">Batch History</span>
                </a>

                <!-- Manage Payments -->
                <a href="manage_payments.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-amber-500 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white transition-colors shadow-sm relative">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM sales WHERE payment_status = 'pending'");
                        $pending_count = $stmt->fetchColumn();
                        if($pending_count > 0):
                        ?>
                        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full ring-2 ring-white"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="text-sm font-bold text-slate-700 text-center">Approve Payments</span>
                </a>

                <!-- Reports -->
                <a href="reports.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-purple-500 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-500 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Reports</span>
                </a>

                <!-- Sales History -->
                <a href="sales_history.php" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl border border-slate-200 hover:border-indigo-500 hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-500 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Sales History</span>
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
