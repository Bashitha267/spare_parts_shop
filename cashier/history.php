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
    <title>Sales History - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            
            color: #1e293b;
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
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            pointer-events: none;
            z-index: 0;
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 2px 12px -2px rgba(0, 0, 0, 0.08);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.08);
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 0.75rem 1.25rem !important;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: left;
        }
        th:first-child { border-top-left-radius: 0.75rem; }
        th:last-child { border-top-right-radius: 0.75rem; }
        td {
            padding: 0.75rem 1.25rem !important;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.82rem;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(99, 102, 241, 0.04); }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes pop { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .anim-pop { animation: pop 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-main min-h-screen relative">
    <div class=""></div>

    <!-- Nav -->
    <nav class="glass-nav fixed w-full z-30 top-0">
        <div class="px-4 md:px-6 py-3 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <a href="dashboard.php" class="p-2 hover:bg-blue-50 rounded-xl transition-all text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-sm font-black text-blue-700 uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Sales History
                    </h1>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">All your past sales</p>
                </div>
            </div>
            <div class="flex items-center gap-3 md:gap-5">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-4 md:px-5 py-2 rounded-xl flex items-center gap-2.5 shadow-lg shadow-emerald-500/20">
                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="text-[8px] font-black text-white/70 uppercase tracking-widest leading-none">Today</p>
                        <p id="today_total" class="text-sm md:text-base font-black text-white leading-none mt-0.5">LKR 0.00</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Cashier</p>
                        <p class="text-xs font-bold text-slate-800"><?php echo $_SESSION['full_name']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-24 px-3 md:px-6 max-w-7xl mx-auto animate-fade relative z-10 pb-8">
        <!-- Table Card -->
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[700px] md:min-w-0">
                    <thead>
                        <tr>
                            <th>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                    Sale ID
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    When
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Customer
                                </div>
                            </th>
                            <th>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Paid By
                                </div>
                            </th>
                            <th class="text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    Amount
                                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </th>
                            <th class="text-center">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user_id = $_SESSION['id'];
                        $stmt = $pdo->prepare("SELECT s.*, c.name as cust_name, c.contact as cust_phone 
                                              FROM sales s 
                                              LEFT JOIN customers c ON s.customer_id = c.id 
                                              WHERE s.user_id = ? 
                                              ORDER BY s.created_at DESC");
                        $stmt->execute([$user_id]);
                        while($row = $stmt->fetch()):
                        ?>
                        <tr class="group">
                            <td>
                                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-lg text-[10px] font-black tracking-tight">#<?php echo $row['id']; ?></span>
                            </td>
                            <td>
                                <p class="text-sm font-bold text-slate-800"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5"><?php echo date('h:i A', strtotime($row['created_at'])); ?></p>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-white text-[10px] font-black shadow-sm">
                                        <?php echo strtoupper(substr($row['cust_name'] ?: 'G', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($row['cust_name'] ?: 'Guest'); ?></p>
                                        <p class="text-[10px] text-slate-400 font-medium"><?php echo htmlspecialchars($row['cust_phone'] ?: ''); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $method = $row['payment_method'];
                                $methodStyles = [
                                    'cash'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'card'   => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'cheque' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'credit' => 'bg-rose-50 text-rose-700 border-rose-200'
                                ];
                                $methodIcons = [
                                    'cash'   => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
                                    'card'   => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>',
                                    'cheque' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                                    'credit' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                ];
                                $style = $methodStyles[$method] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                                $icon = $methodIcons[$method] ?? '';
                                ?>
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-black px-3 py-1.5 <?php echo $style; ?> border rounded-lg uppercase tracking-wider">
                                    <?php echo $icon; ?>
                                    <?php echo $method; ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <p class="text-base font-black text-slate-900 tracking-tight">Rs. <?php echo number_format($row['final_amount'], 2); ?></p>
                                <?php if($row['discount'] > 0): ?>
                                    <p class="text-[10px] text-rose-500 font-bold mt-0.5 flex items-center justify-end gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                        -Rs. <?php echo number_format($row['discount'], 2); ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button onclick="viewDetails(<?php echo $row['id']; ?>)" class="p-2.5 text-white bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl hover:shadow-lg hover:shadow-blue-500/30 hover:scale-105 transition-all active:scale-95" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sale Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-black/30 backdrop-blur-md z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white/90 backdrop-blur-xl w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden anim-pop border border-white/50">
            <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-blue-800 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Sale Details
                    </h3>
                    <p id="modal_sale_id" class="text-xs text-white/60 font-bold mt-0.5"></p>
                </div>
                <button onclick="closeDetailModal()" class="p-2 text-white/60 hover:text-white hover:bg-white/20 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200">
                            <th class="pb-3">Product</th>
                            <th class="pb-3 text-center">Qty</th>
                            <th class="pb-3 text-right">Price</th>
                            <th class="pb-3 text-right">Discount</th>
                            <th class="pb-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody id="detailItems" class="divide-y divide-slate-100"></tbody>
                </table>
                <div id="modalSummary" class="mt-6 pt-5 border-t-2 border-slate-200 space-y-2 text-right"></div>
            </div>
        </div>
    </div>

    <script>
        async function updateTodayTotal() {
            const res = await fetch(`sales_handler.php?action=get_today_total`);
            const data = await res.json();
            document.getElementById('today_total').innerText = 'LKR ' + data.total;
        }
        updateTodayTotal();

        async function viewDetails(id) {
            document.getElementById('modal_sale_id').innerText = '#SALE-' + id;
            const res = await fetch(`sales_handler.php?action=fetch_sale_details&id=${id}`);
            const data = await res.json();
            
            const tbody = document.getElementById('detailItems');
            const summary = document.getElementById('modalSummary');
            tbody.innerHTML = '';
            
            data.items.forEach(item => {
                tbody.innerHTML += `
                    <tr class="text-sm">
                        <td class="py-3.5">
                            <p class="font-bold text-slate-800">${item.name}</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">${item.brand}</p>
                        </td>
                        <td class="py-3.5 text-center">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-black">${item.qty}</span>
                        </td>
                        <td class="py-3.5 text-right font-semibold text-slate-500">Rs. ${numberFormat(item.unit_price)}</td>
                        <td class="py-3.5 text-right font-bold text-rose-500">-Rs. ${numberFormat(item.discount)}</td>
                        <td class="py-3.5 text-right font-black text-slate-800">Rs. ${numberFormat(item.total_price)}</td>
                    </tr>
                `;
            });

            summary.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wide">Grand Total</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight">Rs. ${numberFormat(data.sale.final_amount)}</span>
                </div>
                <div class="flex justify-end mt-2">
                    <span class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm">${data.sale.payment_method.toUpperCase()}</span>
                </div>
            `;

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }
        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
