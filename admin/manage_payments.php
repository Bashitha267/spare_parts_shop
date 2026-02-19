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
    <title>Payment Clearing - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #0d1117;
            color: #e6edf3;
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
        input, select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        th {
            font-weight: 900;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
        }
        .tab-active { 
            background: #3b82f6 !important; 
            color: white !important;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-50">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-white/10 rounded-xl transition-all text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                  <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Payment Clearing</h1>
                  <p class="hidden sm:block text-[9px] text-blue-300/40 font-black uppercase tracking-[0.2em]">Transaction Approval Center</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="hidden lg:flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-[10px] font-black text-blue-300 uppercase tracking-widest"><?php echo $counts['total']; ?> Pending Review</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="p-4 md:p-8 max-w-7xl mx-auto space-y-10 relative z-10">
        
        <!-- Summary Strip -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-blue-400 border border-white/10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-widest mb-1">Total Pending</p>
                    <h3 class="text-2xl font-black text-white"><?php echo $counts['total']; ?> <span class="text-xs font-bold text-white/30 tracking-normal ml-1">TRX</span></h3>
                </div>
            </div>
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-400/10 flex items-center justify-center text-amber-400 border border-amber-400/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-amber-400/40 uppercase tracking-widest mb-1">Cheque Validation</p>
                    <h3 class="text-2xl font-black text-white"><?php echo $counts['cheques']; ?> <span class="text-xs font-bold text-white/30 tracking-normal ml-1">items</span></h3>
                </div>
            </div>
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-emerald-400/40 uppercase tracking-widest mb-1">Credit Clearance</p>
                    <h3 class="text-2xl font-black text-white"><?php echo $counts['credits']; ?> <span class="text-xs font-bold text-white/30 tracking-normal ml-1">items</span></h3>
                </div>
            </div>
        </div>

        <!-- Filter & Actions Bar -->
        <div class="blue-gradient-card p-6 rounded-[2.5rem] flex flex-col lg:flex-row gap-6 items-center">
            <div class="relative flex-grow w-full lg:w-auto">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-300/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchPayments" class="block w-full pl-14 pr-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 text-sm font-bold bg-white/5 border border-white/10" placeholder="Search by Sale ID or Customer Name...">
            </div>

            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <div class="relative flex-grow lg:flex-none">
                    <input type="date" id="dateFilter" class="w-full lg:w-auto px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest outline-none transition-all border-white/10 bg-white/5">
                </div>
                
                <div class="flex bg-white/5 p-1.5 rounded-2xl border border-white/5 flex-grow lg:flex-none">
                    <button onclick="changeMethod('all')" id="tab-all" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all tab-active">All</button>
                    <button onclick="changeMethod('cheque')" id="tab-cheque" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-white/30 hover:text-white">Cheques</button>
                    <button onclick="changeMethod('credit')" id="tab-credit" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-white/30 hover:text-white">Credits</button>
                </div>

                <button onclick="loadPendingPayments()" class="p-4 bg-white/5 border border-white/10 rounded-2xl text-blue-300 hover:bg-blue-500/10 transition-all shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="blue-gradient-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10">
                        <th class="px-8 py-6">Transaction ID</th>
                        <th class="px-8 py-6">Customer Profile</th>
                        <th class="px-8 py-6">Payment Mode</th>
                        <th class="px-8 py-6">Settlement</th>
                        <th class="px-8 py-6 text-center">Officer</th>
                        <th class="px-8 py-6 text-right">Operational Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentBody" class="divide-y divide-white/5">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', loadPendingPayments);

        let currentMethod = 'all';
        let debounceTimer;

        document.getElementById('searchPayments').addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(loadPendingPayments, 300);
        });

        document.getElementById('dateFilter').addEventListener('change', loadPendingPayments);

        async function changeMethod(method) {
            currentMethod = method;
            document.querySelectorAll('button[id^="tab-"]').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('text-white/30', 'hover:text-white');
            });
            const activeBtn = document.getElementById(`tab-${method}`);
            activeBtn.classList.remove('text-white/30', 'hover:text-white');
            activeBtn.classList.add('tab-active');
            loadPendingPayments();
        }

        async function loadPendingPayments() {
            const search = document.getElementById('searchPayments').value;
            const date = document.getElementById('dateFilter').value;
            const tbody = document.getElementById('paymentBody');
            tbody.innerHTML = `<tr><td colspan="6" class="py-24 text-center"><div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div></td></tr>`;
            
            try {
                const res = await fetch(`payment_handler.php?action=fetch_pending_payments&method=${currentMethod}&search=${search}&date=${date}`);
                const data = await res.json();
                tbody.innerHTML = '';

                if (data.sales.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="py-32 text-center">
                                <div class="flex flex-col items-center gap-6 opacity-20">
                                    <svg class="w-24 h-24 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-lg font-black text-blue-300 uppercase tracking-[0.2em]">Clear Skies. No Pending Items.</p>
                                </div>
                            </td>
                        </tr>`;
                    return;
                }

                data.sales.forEach(sale => {
                    const methodClass = sale.payment_method.toLowerCase() === 'cheque' ? 'bg-amber-400/10 text-amber-400 border-amber-400/20' : 
                                      sale.payment_method.toLowerCase() === 'credit' ? 'bg-emerald-400/10 text-emerald-400 border-emerald-400/20' : 
                                      'bg-white/5 text-white/40 border-white/10';
                    
                    tbody.innerHTML += `
                        <tr class="hover:bg-white/5 transition-all group">
                            <td class="px-8 py-8">
                                <span class="font-mono text-[10px] font-black text-blue-300/40 tracking-tighter uppercase px-3 py-1 bg-white/5 rounded-lg border border-white/5">TRX-${sale.id}</span>
                            </td>
                            <td class="px-8 py-8">
                                <p class="font-black text-white leading-tight text-sm">${sale.cust_name || 'Anonymous Guest'}</p>
                                <p class="text-[9px] text-blue-300/40 font-black uppercase tracking-wider mt-1.5">${new Date(sale.created_at).toLocaleDateString()} @ ${new Date(sale.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            </td>
                            <td class="px-8 py-8">
                                <span class="px-4 py-1.5 border rounded-xl text-[9px] font-black uppercase tracking-widest ${methodClass}">${sale.payment_method}</span>
                            </td>
                            <td class="px-8 py-8">
                                <p class="font-black text-white tracking-widest text-base">Rs. ${numberFormat(sale.final_amount)}</p>
                            </td>
                            <td class="px-8 py-8 text-center text-nowrap">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-white/5 rounded-2xl">
                                    <div class="w-6 h-6 rounded-lg bg-blue-500 text-[10px] flex items-center justify-center font-black text-white shadow-lg shadow-blue-500/20">${sale.cashier_name.charAt(0)}</div>
                                    <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">${sale.cashier_name}</span>
                                </div>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div class="flex flex-row flex-nowrap justify-end items-center gap-3">
                                    <button onclick="updateStatus(${sale.id}, 'approved')" class="whitespace-nowrap px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-500/20 active:scale-95">Approve</button>
                                    <button onclick="updateStatus(${sale.id}, 'rejected')" class="whitespace-nowrap px-6 py-3 bg-white/5 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95">Reject</button>
                                </div>
                            </td>
                        </tr>`;
                });
            } catch (e) {
                console.error(e);
                tbody.innerHTML = '<tr><td colspan="6" class="py-20 text-center text-red-400 font-black uppercase tracking-widest">Failed to connect to transmission engine.</td></tr>';
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
                    popup: 'rounded-[2.5rem] bg-slate-900 border border-white/10 text-white',
                    confirmButton: 'rounded-2xl font-black uppercase text-[10px] px-8 py-4 tracking-widest',
                    cancelButton: 'rounded-2xl font-black uppercase text-[10px] px-8 py-4 tracking-widest bg-white/5 text-white/40 border border-white/10'
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
                        customClass: { popup: 'rounded-[2rem] bg-slate-900 border border-white/10 text-white' }
                    });
                    loadPendingPayments();
                }
            }
        }

        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
