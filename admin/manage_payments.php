<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');

// Fetch counts for the approval tab
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
    <title>Payment Management - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #1e293b;
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
                radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .blue-gradient-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: 2px solid #ffffff;
            box-shadow: 0 10px 30px -10px rgba(37, 99, 235, 0.3);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 2px solid #ffffff;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        input, select {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            outline: none !important;
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white !important;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.25rem 1.5rem !important;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: rgba(241, 245, 249, 0.5);
        }
        td {
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            color: #0f172a;
        }
        .tab-active { 
            background: #2563eb !important; 
            color: white !important;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .ph-suggest-dropdown {
            position: fixed;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 20px 50px -10px rgba(0,0,0,0.18);
            z-index: 99999;
            overflow: hidden;
            animation: phDropIn 0.13s ease-out;
        }
        @keyframes phDropIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        .ph-suggest-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.1s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ph-suggest-item:last-child { border-bottom: none; }
        .ph-suggest-item:hover, .ph-suggest-item.active { background: #eff6ff; }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-50">
        <div class="px-4 md:px-6 py-4 flex flex-col sm:flex-row justify-between items-center max-w-7xl mx-auto gap-4">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                  <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Payment Management</h1>
                  <p class="hidden sm:block text-[9px] text-slate-800 font-black uppercase tracking-[0.2em]">Approve Transactions & View History</p>
                </div>
            </div>
            
            <!-- Tab Switcher -->
            <div class="flex w-full sm:w-auto bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                <button onclick="switchMainTab('approve')" id="main-tab-approve" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all tab-active text-black">Approvals</button>
                <button onclick="switchMainTab('history')" id="main-tab-history" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-black hover:text-slate-600">History</button>
            </div>
        </div>
    </nav>

    <!-- SECTION: APPROVALS -->
    <main id="section-approve" class="p-4 md:p-8 max-w-7xl mx-auto space-y-10 relative z-10">
        <!-- Summary Strip -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-blue-400 border border-white/10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1">Total Pending</p>
                    <h3 id="count_total" class="text-2xl font-black text-white"><?php echo $counts['total']; ?> <span class="text-xs font-bold text-white/40 tracking-normal ml-1">TRX</span></h3>
                </div>
            </div>
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-400/10 flex items-center justify-center text-amber-400 border border-amber-400/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-1">Cheque Validation</p>
                    <h3 id="count_cheques" class="text-2xl font-black text-white"><?php echo $counts['cheques']; ?> <span class="text-xs font-bold text-white/30 tracking-normal ml-1">items</span></h3>
                </div>
            </div>
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Credit Clearance</p>
                    <h3 id="count_credits" class="text-2xl font-black text-white"><?php echo $counts['credits']; ?> <span class="text-xs font-bold text-white/40 tracking-normal ml-1">items</span></h3>
                </div>
            </div>
        </div>

        <!-- Filter Bar Approvals -->
        <div class="glass-card p-6 rounded-[2.5rem] flex flex-col lg:flex-row gap-6 items-center">
            <div class="relative flex-grow w-full lg:w-auto">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchPayments" class="block w-full pl-14 pr-6 py-4 rounded-2xl outline-none transition-all placeholder:text-slate-400 text-sm font-bold bg-white border border-slate-200" placeholder="Search by TRX or Customer...">
            </div>
            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <input type="date" id="dateFilterApprove" class="flex-grow lg:flex-none px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest outline-none border-slate-200 bg-white">
                <div class="flex flex-grow sm:flex-none bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                    <button onclick="changeMethod('all')" id="tab-all" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all tab-active">All</button>
                    <button onclick="changeMethod('cheque')" id="tab-cheque" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400">Cheques</button>
                    <button onclick="changeMethod('credit')" id="tab-credit" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400">Credits</button>
                </div>
                <button onclick="loadPendingPayments()" class="flex-grow sm:flex-none p-4 bg-white border border-slate-200 rounded-2xl text-blue-600 hover:bg-slate-50 transition-all font-black flex justify-center items-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-[2.5rem] overflow-hidden border-4 border-blue-500/20">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] border-collapse">
                    <thead>
                        <tr>
                        <th class="px-8 py-6">Transaction ID</th>
                        <th class="px-8 py-6">Customer Profile</th>
                        <th class="px-8 py-6">Payment Mode</th>
                        <th class="px-8 py-6">Settlement</th>
                        <th class="px-8 py-6 text-center text-rose-500">Pending Days</th>
                        <th class="px-8 py-6 text-center">Officer</th>
                        <th class="px-8 py-6 text-right">Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <!-- Pagination Approvals -->
            <div id="paginationApprove" class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-center gap-2"></div>
        </div>
    </main>

    <!-- SECTION: HISTORY -->
    <main id="section-history" class="hidden p-4 md:p-8 max-w-7xl mx-auto space-y-10 relative z-10">
        <!-- Search \u0026 Filter Bar History -->
        <div class="glass-card p-6 rounded-[2rem] flex flex-wrap lg:flex-nowrap items-center gap-4">
            <div class="relative w-full lg:w-72 flex-shrink-0" id="searchWrapperHist">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="searchHistory" autocomplete="off" placeholder="Search TRX, Name or Contact..." class="w-full pl-12 pr-4 py-3 rounded-xl text-xs font-bold placeholder:text-slate-400">
            </div>
            
            <div class="flex flex-wrap lg:flex-nowrap items-center gap-3 w-full lg:flex-grow">
                <input type="date" id="hist_date" class="px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">
                <select id="hist_year" class="px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">
                    <option value="">Year</option>
                    <?php for($y=date('Y'); $y>=2020; $y--): ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
                <select id="hist_method" class="px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">
                    <option value="all">Mode</option>
                    <option value="cheque">Cheque</option>
                    <option value="credit">Credit</option>
                </select>
                <button onclick="clearHistoryFilters()" class="flex items-center gap-2 p-3 bg-blue-600 border border-slate-200 rounded-xl text-white font-black hover:bg-blue-800 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-[2.5rem] overflow-hidden border-4 border-blue-500/20">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] border-collapse">
                    <thead>
                        <tr>
                            <th>Record ID</th>
                            <th>Customer Identity</th>
                            <th>Strategy</th>
                            <th>Fiscal Volume</th>
                            <th class="text-center">Officer</th>
                            <th class="text-right">Settled At</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody" class="divide-y divide-white/10"></tbody>
                </table>
            </div>
            <!-- Pagination History -->
            <div id="paginationHistory" class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-center gap-2"></div>
        </div>
    </main>

    <script>
        let currentMethod = 'all';
        let mainTab = 'approve';
        let debounceTimer;
        let currentApprovePage = 1;
        let currentHistoryPage = 1;

        document.addEventListener('DOMContentLoaded', () => {
            loadPendingPayments();
            initHistoryFilters();
            initHistoryAutosuggest();
        });

        function initHistoryFilters() {
            const histFilters = ['hist_date', 'hist_year', 'hist_method'];
            histFilters.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', () => loadHistory(1));
            });
        }

        function switchMainTab(tab) {
            mainTab = tab;
            document.getElementById('section-approve').classList.toggle('hidden', tab !== 'approve');
            document.getElementById('section-history').classList.toggle('hidden', tab !== 'history');
            
            document.getElementById('main-tab-approve').classList.toggle('tab-active', tab === 'approve');
            document.getElementById('main-tab-approve').classList.toggle('text-slate-400', tab !== 'approve');
            document.getElementById('main-tab-history').classList.toggle('tab-active', tab === 'history');
            document.getElementById('main-tab-history').classList.toggle('text-slate-400', tab !== 'history');

            if(tab === 'history') loadHistory();
            else loadPendingPayments();
        }

        // ---- APPROVAL LOGIC ----
        document.getElementById('searchPayments').addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(loadPendingPayments, 300);
        });
        document.getElementById('dateFilterApprove').addEventListener('change', loadPendingPayments);

        async function changeMethod(method) {
            currentMethod = method;
            const tabs = ['all', 'cheque', 'credit'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-${t}`);
                btn.classList.toggle('tab-active', t === method);
                btn.classList.toggle('text-slate-400', t !== method);
            });
            loadPendingPayments();
        }

        async function loadPendingPayments(page = 1) {
            currentApprovePage = page;
            const search = document.getElementById('searchPayments').value;
            const date = document.getElementById('dateFilterApprove').value;
            const tbody = document.getElementById('paymentBody');
            tbody.innerHTML = '<tr><td colspan="7" class="py-24 text-center"><div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent inline-block"></div></td></tr>';
            
            const res = await fetch(`payment_handler.php?action=fetch_pending_payments&method=${currentMethod}&search=${search}&date=${date}&page=${page}`);
            const data = await res.json();
            
            // Update metrics dynamically
            if (data.counts) {
                document.getElementById('count_total').innerHTML = `${data.counts.total} <span class="text-xs font-bold text-white/40 tracking-normal ml-1">TRX</span>`;
                document.getElementById('count_cheques').innerHTML = `${data.counts.cheques || 0} <span class="text-xs font-bold text-white/30 tracking-normal ml-1">items</span>`;
                document.getElementById('count_credits').innerHTML = `${data.counts.credits || 0} <span class="text-xs font-bold text-white/40 tracking-normal ml-1">items</span>`;
            }

            tbody.innerHTML = '';

            if (data.sales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="py-32 text-center opacity-20"><p class="text-lg font-black uppercase tracking-widest">No Pending Approvals</p></td></tr>';
                document.getElementById('paginationApprove').innerHTML = '';
                return;
            }

            data.sales.forEach(sale => {
                const methodClass = sale.payment_method.toLowerCase() === 'cheque' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200';
                
                // Calculate days pending
                const saleDate = new Date(sale.created_at);
                const today = new Date();
                const diffTime = Math.abs(today - saleDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const dayLabel = diffDays === 1 ? '1 Day' : diffDays + ' Days';
                
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-8"><span class="font-mono text-[10px] font-black text-blue-800 tracking-tighter uppercase px-3 py-1 bg-blue-50 rounded-lg border border-blue-100">TRX-${sale.id}</span></td>
                        <td class="px-8 py-8"><p class="font-black text-slate-900 leading-tight text-sm">${sale.cust_name || 'Walk-in'}</p><p class="text-[9px] text-slate-400 font-black uppercase tracking-wider mt-1.5">${new Date(sale.created_at).toLocaleDateString()} @ ${new Date(sale.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p></td>
                        <td class="px-8 py-8"><span class="px-4 py-1.5 border rounded-xl text-[9px] font-black uppercase tracking-widest ${methodClass}">${sale.payment_method}</span></td>
                        <td class="px-8 py-8"><p class="font-black text-slate-900 tracking-widest text-base">Rs. ${numberFormat(sale.final_amount)}</p></td>
                        <td class="px-8 py-8 text-center text-rose-500 font-bold "><span class="text-xs font-black text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 uppercase tracking-tighter">${dayLabel}</span></td>
                        <td class="px-8 py-8 text-center text-nowrap"><div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-2xl"><div class="w-6 h-6 rounded-lg bg-blue-600 text-[10px] flex items-center justify-center font-black text-white">${sale.cashier_name.charAt(0)}</div><span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">${sale.cashier_name}</span></div></td>
                        <td class="px-8 py-8 text-right"><div class="flex justify-end gap-3"><button onclick="updateStatus(${sale.id}, 'approved')" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-md">Approve</button><button onclick="updateStatus(${sale.id}, 'rejected')" class="px-6 py-3 bg-white border border-rose-200 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest">Reject</button></div></td>
                    </tr>`;
            });
            renderPagination(data.total_pages, data.current_page, 'paginationApprove', loadPendingPayments);
        }

        async function updateStatus(saleId, status) {
            const result = await Swal.fire({
                title: 'Authorize Transaction?',
                text: `Mark TRX-${saleId} as ${status}?`,
                icon: status === 'approved' ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonColor: status === 'approved' ? '#10b981' : '#f43f5e',
                confirmButtonText: 'Yes, Confirm',
                customClass: { popup: 'rounded-[2.5rem]' }
            });

            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('sale_id', saleId);
                fd.append('status', status);
                fd.append('action', 'update_payment_status');
                const res = await fetch('payment_handler.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ title: 'Success', icon: 'success', timer: 1000, showConfirmButton: false });
                    loadPendingPayments();
                }
            }
        }

        // ---- HISTORY LOGIC ----
        function initHistoryAutosuggest() {
            const searchInput = document.getElementById('searchHistory');
            const suggestDropdown = document.createElement('div');
            suggestDropdown.className = 'ph-suggest-dropdown';
            suggestDropdown.style.display = 'none';
            document.body.appendChild(suggestDropdown);

            searchInput.addEventListener('input', function() {
                const val = this.value.trim();
                loadHistory();
                if (val.length < 1) { suggestDropdown.style.display = 'none'; return; }
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(async () => {
                    const res = await fetch(`payment_handler.php?action=suggest&q=${encodeURIComponent(val)}`);
                    const data = await res.json();
                    renderSuggest(data.suggestions, suggestDropdown, searchInput);
                }, 180);
            });

            document.addEventListener('click', (e) => {
                if (!document.getElementById('searchWrapperHist').contains(e.target) && !suggestDropdown.contains(e.target))
                    suggestDropdown.style.display = 'none';
            });
        }

        function renderSuggest(items, dropdown, input) {
            dropdown.innerHTML = '';
            if (!items.length) { dropdown.style.display = 'none'; return; }
            items.forEach(s => {
                const div = document.createElement('div');
                div.className = 'ph-suggest-item';
                div.innerHTML = `<p class="font-black text-sm uppercase">${s.label}</p>`;
                div.onmousedown = () => { input.value = s.value; dropdown.style.display = 'none'; loadHistory(); };
                dropdown.appendChild(div);
            });
            const r = input.getBoundingClientRect();
            dropdown.style.left = r.left + 'px';
            dropdown.style.top = (r.bottom + 4) + 'px';
            dropdown.style.width = r.width + 'px';
            dropdown.style.display = 'block';
        }

        async function loadHistory(page = 1) {
            currentHistoryPage = page;
            const tbody = document.getElementById('historyBody');
            const search = document.getElementById('searchHistory').value;
            const date = document.getElementById('hist_date').value;
            const year = document.getElementById('hist_year').value;
            const method = document.getElementById('hist_method').value;

            const res = await fetch(`payment_handler.php?action=fetch_payment_history&search=${search}&date=${date}&year=${year}&method=${method}&page=${page}`);
            const data = await res.json();
            tbody.innerHTML = '';

            if(data.sales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-8 py-20 text-center opacity-30 italic font-black">No Audit Records Found</td></tr>';
                document.getElementById('paginationHistory').innerHTML = '';
                return;
            }

            data.sales.forEach(sale => {
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="px-8 py-6"><span class="font-mono text-[10px] font-black text-blue-800 tracking-tighter uppercase px-3 py-1 bg-blue-50 rounded-lg border border-blue-100">TRX-${sale.id}</span></td>
                        <td class="px-8 py-6"><p class="font-black text-slate-900 leading-tight text-sm">${sale.cust_name || 'Walk-in'}</p></td>
                        <td class="px-8 py-6"><span class="text-[10px] font-black uppercase text-blue-600 tracking-widest">${sale.payment_method}</span></td>
                        <td class="px-8 py-6 font-black text-slate-900 text-sm">Rs. ${numberFormat(sale.final_amount)}</td>
                        <td class="px-8 py-6 text-center font-black text-slate-500 uppercase text-[10px]">${sale.cashier_name}</td>
                        <td class="px-8 py-6 text-right"><p class="font-bold text-slate-900 text-[11px]">${new Date(sale.created_at).toLocaleDateString()}</p><p class="text-[9px] text-slate-400 font-black">${new Date(sale.created_at).toLocaleTimeString()}</p></td>
                    </tr>`;
            });
            renderPagination(data.total_pages, data.current_page, 'paginationHistory', loadHistory);
        }

        function renderPagination(totalPages, currentPage, elementId, callback) {
            const container = document.getElementById(elementId);
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const maxVisible = 5;
            let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(totalPages, start + maxVisible - 1);
            if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1);

            if (currentPage > 1) {
                container.innerHTML += `<button onclick="${callback.name}(${currentPage - 1})" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase text-slate-500 hover:bg-slate-50 transition-all">Prev</button>`;
            }

            for (let i = start; i <= end; i++) {
                container.innerHTML += `<button onclick="${callback.name}(${i})" class="w-10 h-10 flex items-center justify-center rounded-xl text-[10px] font-black border transition-all ${i === currentPage ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-600/30' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50'}">${i}</button>`;
            }

            if (currentPage < totalPages) {
                container.innerHTML += `<button onclick="${callback.name}(${currentPage + 1})" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase text-slate-500 hover:bg-slate-50 transition-all">Next</button>`;
            }
        }

        function clearHistoryFilters() {
            ['searchHistory', 'hist_date', 'hist_year'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('hist_method').value = 'all';
            loadHistory();
        }

        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
