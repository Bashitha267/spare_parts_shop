<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');

// Fetch some quick counts for the header
$count_stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN payment_method = 'cheque' THEN 1 ELSE 0 END) as cheques,
    SUM(CASE WHEN payment_method = 'credit' THEN 1 ELSE 0 END) as credits
    FROM sales WHERE payment_status = 'pending'");
$counts = $count_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approvals - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .tab-active { @apply bg-white shadow-md text-blue-600; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">
    <!-- Premium Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3 md:gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-slate-500 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-lg md:text-xl font-black text-slate-800 tracking-tight">Payment Clearing</h1>
                    <p class="hidden sm:block text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Transaction Approval Center</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                <div class="hidden lg:flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-xs font-bold text-slate-500"><?php echo $counts['total']; ?> Pending</span>
                </div>
                <div class="hidden sm:block h-8 w-[1px] bg-slate-200"></div>
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="hidden sm:block text-right">
                        <p class="text-xs font-black text-slate-800 leading-none"><?php echo $_SESSION['full_name']; ?></p>
                        <p class="text-[9px] text-slate-400 font-bold">Admin</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-black">
                        <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="p-4 md:p-6 max-w-7xl mx-auto space-y-8 animate-fade">
        
        <!-- Summary Strip -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Pending</p>
                    <h3 class="text-2xl font-black text-slate-800"><?php echo $counts['total']; ?> <span class="text-sm font-medium text-slate-400">items</span></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cheque Validation</p>
                    <h3 class="text-2xl font-black text-slate-800"><?php echo $counts['cheques']; ?> <span class="text-sm font-medium text-slate-400">items</span></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Credit Clearance</p>
                    <h3 class="text-2xl font-black text-slate-800"><?php echo $counts['credits']; ?> <span class="text-sm font-medium text-slate-400">items</span></h3>
                </div>
            </div>
        </div>

        <!-- Filter & Actions Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex bg-slate-200/50 p-1.5 rounded-2xl w-full md:w-auto">
                <button onclick="changeMethod('all')" id="tab-all" class="flex-1 md:flex-none px-6 py-2 rounded-xl text-xs font-black transition-all bg-white shadow-sm text-blue-600">Total Pool</button>
                <button onclick="changeMethod('cheque')" id="tab-cheque" class="flex-1 md:flex-none px-6 py-2 rounded-xl text-xs font-black transition-all text-slate-500 hover:text-slate-700">Cheques</button>
                <button onclick="changeMethod('credit')" id="tab-credit" class="flex-1 md:flex-none px-6 py-2 rounded-xl text-xs font-black transition-all text-slate-500 hover:text-slate-700">Credit Sales</button>
            </div>
            
            <button onclick="loadPendingPayments()" class="p-2.5 bg-white border border-slate-200 rounded-2xl text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Transaction ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer Profile</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Payment Mode</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Settlement</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Officer</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentBody" class="divide-y divide-slate-50">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', loadPendingPayments);

        let currentMethod = 'all';

        async function changeMethod(method) {
            currentMethod = method;
            document.querySelectorAll('main button[id^="tab-"]').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
                btn.classList.add('text-slate-500', 'hover:text-slate-700');
            });
            const activeBtn = document.getElementById(`tab-${method}`);
            activeBtn.classList.remove('text-slate-500', 'hover:text-slate-700');
            activeBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
            loadPendingPayments();
        }

        async function loadPendingPayments() {
            const tbody = document.getElementById('paymentBody');
            tbody.innerHTML = `<tr><td colspan="6" class="py-20 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div></td></tr>`;
            
            try {
                const res = await fetch(`payment_handler.php?action=fetch_pending_payments&method=${currentMethod}`);
                const data = await res.json();
                tbody.innerHTML = '';

                if (data.sales.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="py-24 text-center">
                                <div class="flex flex-col items-center gap-4 grayscale opacity-40">
                                    <svg class="w-20 h-20 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Clear Skies. No Pending Items.</p>
                                </div>
                            </td>
                        </tr>`;
                    return;
                }

                data.sales.forEach(sale => {
                    const methodClass = sale.payment_method.toLowerCase() === 'cheque' ? 'bg-amber-50 text-amber-600 ring-amber-200' : 
                                      sale.payment_method.toLowerCase() === 'credit' ? 'bg-emerald-50 text-emerald-600 ring-emerald-200' : 
                                      'bg-slate-100 text-slate-600 ring-slate-200';
                    
                    tbody.innerHTML += `
                        <tr class="hover:bg-slate-50/80 transition-all group">
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs font-black text-slate-400 tracking-tighter">TRX-${sale.id}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="font-black text-slate-800 leading-tight">${sale.cust_name || 'Anonymous Guest'}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">${new Date(sale.created_at).toLocaleDateString()} @ ${new Date(sale.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase ring-1 ring-inset ${methodClass}">${sale.payment_method}</span>
                            </td>
                            <td class="px-8 py-6 font-black text-slate-900 tracking-tight">Rs. ${numberFormat(sale.final_amount)}</td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg">
                                    <div class="w-4 h-4 rounded-full bg-slate-300 text-[8px] flex items-center justify-center font-black text-white">${sale.cashier_name.charAt(0)}</div>
                                    <span class="text-[10px] font-bold text-slate-500">${sale.cashier_name}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex flex-row flex-nowrap justify-end items-center gap-2">
                                    <button onclick="updateStatus(${sale.id}, 'approved')" class="whitespace-nowrap px-5 py-2.5 bg-emerald-500 hover:bg-black text-white rounded-xl text-[10px] font-black uppercase transition-all shadow-lg shadow-emerald-100 hover:shadow-none translate-y-0 active:scale-95">Approve</button>
                                    <button onclick="updateStatus(${sale.id}, 'rejected')" class="whitespace-nowrap px-5 py-2.5 bg-white border border-rose-100 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl text-[10px] font-black uppercase transition-all active:scale-95">Reject</button>
                                </div>
                            </td>
                        </tr>`;
                });
            } catch (e) {
                console.error(e);
                tbody.innerHTML = '<tr><td colspan="6" class="py-10 text-center text-rose-500 font-bold">Failed to connect to transmission engine.</td></tr>';
            }
        }

        async function updateStatus(saleId, status) {
            const result = await Swal.fire({
                title: 'Authorize Transaction?',
                text: `Are you sure you want to mark this TRX-${saleId} as ${status}?`,
                icon: status === 'approved' ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonColor: status === 'approved' ? '#10b981' : '#f43f5e',
                confirmButtonText: status === 'approved' ? 'Yes, Authorize' : 'Yes, Reject',
                cancelButtonText: 'Wait, Cancel',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl font-bold uppercase text-[10px] px-6 py-3',
                    cancelButton: 'rounded-xl font-bold uppercase text-[10px] px-6 py-3'
                }
            });

            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('sale_id', saleId);
                fd.append('status', status);
                fd.append('action', 'update_payment_status');

                const res = await fetch('payment_handler.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        title: 'Clearance Success',
                        text: `The transaction has been successfully ${status}.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                    loadPendingPayments();
                }
            }
        }

        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
