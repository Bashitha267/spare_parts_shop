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
    <title>Sales Inventory - Vehicle Square</title>
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
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.08);
        }
        .blue-gradient-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px -10px rgba(37, 99, 235, 0.3);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        input, select, textarea {
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
        option {
            background-color: white !important;
            color: #0f172a !important;
        }
        tr:nth-child(even) {
            background-color: rgba(241, 245, 249, 0.5);
        }
        td {
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            color: #0f172a;
        }
        .sh-suggest-dropdown {
            position: fixed;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 20px 50px -10px rgba(0,0,0,0.18);
            z-index: 99999;
            overflow: hidden;
            animation: shDropIn 0.13s ease-out;
        }
        @keyframes shDropIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        .sh-suggest-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.1s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sh-suggest-item:last-child { border-bottom: none; }
        .sh-suggest-item:hover, .sh-suggest-item.active { background: #eff6ff; }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-50">
        <div class="px-4 md:px-6 py-3 flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="dashboard.php" class="p-2 hover:bg-blue-50 rounded-xl transition-all text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Sales History
                    </h1>
                    <p class="text-[8px] sm:text-[9px] text-slate-600 font-bold uppercase tracking-widest">Audit Registry</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 scrollbar-hide">
                <!-- Total Display -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-2.5 shadow-lg shadow-blue-500/20 shrink-0">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-white/10 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[7px] font-black text-white/70 uppercase tracking-widest leading-none">Total</p>
                        <p id="nav_total" class="text-[10px] sm:text-xs md:text-sm font-black text-white leading-none mt-0.5 whitespace-nowrap">Rs. 0.00</p>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-2.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <div>
                        <p class="text-[7px] font-black opacity-60 uppercase tracking-widest leading-none">Apprv.</p>
                        <p id="nav_approved" class="text-[10px] sm:text-xs md:text-sm font-black leading-none mt-0.5 whitespace-nowrap">Rs. 0.00</p>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-rose-50 text-rose-700 border border-rose-200 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-2.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="text-[7px] font-black opacity-60 uppercase tracking-widest leading-none">Pend.</p>
                        <p id="nav_pending" class="text-[10px] sm:text-xs md:text-sm font-black leading-none mt-0.5 whitespace-nowrap">Rs. 0.00</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="p-8 mx-auto space-y-10 relative z-10">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
            <!-- Cash -->
            <div class="glass-card p-8 border-l-4 border-emerald-500 shadow-xl shadow-emerald-500/10 hover:scale-105 transition-all cursor-default group">
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 group-hover:text-emerald-500 transition-colors">Cash Sales</p>
                <h3 id="card_cash" class="text-2xl font-black text-emerald-600 tracking-tight">Rs. 0.00</h3>
            </div>
            <!-- Card -->
            <div class="glass-card p-8 border-l-4 border-indigo-500 shadow-xl shadow-indigo-500/10 hover:scale-105 transition-all cursor-default group">
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 group-hover:text-indigo-500 transition-colors">Card Sales</p>
                <h3 id="card_card" class="text-2xl font-black text-indigo-600 tracking-tight">Rs. 0.00</h3>
            </div>
            <!-- Approved Credit -->
            <div class="glass-card p-8 border-l-4 border-blue-500 shadow-xl shadow-blue-500/10 hover:scale-105 transition-all cursor-default group">
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 group-hover:text-blue-500 transition-colors">Approved Credit</p>
                <h3 id="card_app_credit" class="text-2xl font-black text-blue-600 tracking-tight">Rs. 0.00</h3>
            </div>
            <!-- Approved Cheque -->
            <div class="glass-card p-6 border-l-4 border-amber-500 shadow-xl shadow-amber-500/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 group-hover:text-amber-500 transition-colors">Approved Cheque</p>
                <h3 id="card_app_cheque" class="text-xl font-black text-amber-600 tracking-tight">Rs. 0.00</h3>
            </div>
            <!-- Pending Credit -->
            <div class="glass-card p-6 border-l-4 border-rose-400 shadow-xl shadow-rose-400/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 group-hover:text-rose-500 transition-colors">Pending Credit</p>
                <h3 id="card_pend_credit" class="text-xl font-black text-rose-500 tracking-tight">Rs. 0.00</h3>
            </div>
            <!-- Pending Cheque -->
            <div class="glass-card p-6 border-l-4 border-orange-400 shadow-xl shadow-orange-400/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 group-hover:text-orange-500 transition-colors">Pending Cheque</p>
                <h3 id="card_pend_cheque" class="text-xl font-black text-orange-500 tracking-tight">Rs. 0.00</h3>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="glass-card p-6 rounded-[2.5rem] relative z-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 items-end">
                <div class="sm:col-span-2 md:col-span-1 lg:col-span-1 relative" id="searchWrapper">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Search Records</label>
                    <input type="text" id="search" autocomplete="off" placeholder="ID, Name, Contact..." class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold placeholder:text-slate-300">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">From Date</label>
                    <input type="date" id="date_from" onchange="loadHistory(1)" class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">To Date</label>
                    <input type="date" id="date_to" onchange="loadHistory(1)" class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Payment</label>
                    <select id="method_filter" onchange="loadHistory(1)" class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                        <option value="all">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Pay Status</label>
                    <select id="status_filter" onchange="loadHistory(1)" class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                        <option value="all">Any Pay Stat</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Order Status</label>
                    <select id="order_status_filter" onchange="loadHistory(1)" class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                        <option value="all">Any Order Stat</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Drafted/Pending</option>
                    </select>
                </div>
                <div class="md:col-span-1 lg:col-span-1 flex gap-2">
                    <button onclick="resetFilters()" title="Reset" class="flex-grow bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black hover:bg-slate-200 transition-all uppercase tracking-widest border border-slate-200 flex items-center justify-center gap-2 py-3.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button onclick="loadHistory(1)" class="flex-grow bg-blue-600 text-white rounded-xl text-[10px] font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 uppercase tracking-widest py-3.5 ring-4 ring-blue-600/10">
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] border-collapse">
                <thead>
                    <tr class="text-left text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <th class="px-6 py-6">ID</th>
                        <th class="px-8 py-6 min-w-[150px]">Customer</th>
                        <th class="px-8 py-6 hidden lg:table-cell">Contact</th>
                        <th class="px-8 py-6 hidden xl:table-cell">Issued By</th>
                        <th class="px-8 py-6 hidden md:table-cell">Timestamp</th>
                        <th class="px-2 py-6">Payment</th>
                        <th class="px-8 py-6 text-right hidden xl:table-cell">Discount</th>
                        <th class="px-8 py-6 text-right">Settlement</th>
                        <th class="px-8 py-6 text-center hidden lg:table-cell">Status</th>
                        <th class="px-8 py-6 text-center">Auth Status</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="historyBody" class="divide-y divide-slate-100">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
        <!-- Pagination Controls -->
        <div id="paginationControls" class="p-8 flex justify-center border-t border-slate-100">
            <!-- Rendered via JS -->
        </div>
    </div>
</main>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md hidden items-center justify-center z-[100] p-4">
        <div class="glass-card max-w-lg w-full p-10 space-y-8 shadow-2xl border border-white/50">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Edit Sale <span id="editId" class="text-blue-600"></span></h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="editForm" class="space-y-6">
                <input type="hidden" name="sale_id" id="formSaleId">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Total Adjustment</label>
                    <input type="number" step="0.01" name="total_amount" id="formAmount" class="w-full px-6 py-4 rounded-2xl text-sm font-bold border border-slate-200">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Payment Protocol</label>
                        <select name="payment_method" id="formMethod" class="w-full px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-slate-200">
                            <option value="cash">Cash Flow</option>
                            <option value="card">Terminal Card</option>
                            <option value="cheque">Cheque Pool</option>
                            <option value="credit">Credit Facility</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Verification Status</label>
                        <select name="payment_status" id="formStatus" class="w-full px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-slate-200">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3 ml-1">Change Authorization Reason *</label>
                    <textarea name="reason" required placeholder="Describe why this administrative edit is being executed..." class="w-full px-6 py-4 rounded-2xl text-sm font-bold min-h-[120px] resize-none border border-slate-200"></textarea>
                </div>
                
                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl font-black hover:shadow-lg transition-all uppercase text-sm tracking-widest">Commit Adjustments</button>
                    <button type="button" onclick="closeModal()" class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-200 transition-all uppercase text-xs tracking-widest">Discard</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', () => {
             // Set default date range to today
             const today = new Date().toISOString().split('T')[0];
             document.getElementById('date_from').value = today;
             document.getElementById('date_to').value = today;
             
             loadHistory(1);
        });
        
        function resetFilters() {
            document.getElementById('search').value = '';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_from').value = today;
            document.getElementById('date_to').value = today;
            document.getElementById('method_filter').value = 'all';
            document.getElementById('status_filter').value = 'all';
            document.getElementById('order_status_filter').value = 'all';
            currentPage = 1;
            loadHistory(1);
        }
        
        // ---- Autosuggest ----
        const searchInput = document.getElementById('search');
        const searchWrapper = document.getElementById('searchWrapper');
        const suggestDropdown = document.createElement('div');
        suggestDropdown.className = 'sh-suggest-dropdown';
        suggestDropdown.style.display = 'none';
        suggestDropdown.style.minWidth = '280px';
        suggestDropdown.style.maxHeight = '280px';
        suggestDropdown.style.overflowY = 'auto';
        document.body.appendChild(suggestDropdown);
        let suggestActiveIdx = -1;
        let suggestItems = [];

        function positionSuggest() {
            const r = searchInput.getBoundingClientRect();
            suggestDropdown.style.left = r.left + 'px';
            suggestDropdown.style.top = (r.bottom + 4) + 'px';
            suggestDropdown.style.width = r.width + 'px';
        }

        function hideSuggest() { suggestDropdown.style.display = 'none'; suggestActiveIdx = -1; }

        function renderSuggest(items) {
            suggestItems = items;
            suggestDropdown.innerHTML = '';
            suggestActiveIdx = -1;
            if (!items.length) { hideSuggest(); return; }
            items.forEach((s, i) => {
                const div = document.createElement('div');
                div.className = 'sh-suggest-item';
                if (s.type === 'trx') {
                    div.innerHTML = `<span class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></span><div><p class="font-mono font-black text-[11px] text-blue-600 tracking-widest">${s.label}</p><p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Transaction ID</p></div>`;
                } else {
                    div.innerHTML = `<span class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-500"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span><div><p class="font-black text-[12px] text-slate-900 uppercase tracking-tight">${s.label}</p><p class="text-[9px] text-slate-400 font-bold mt-0.5">${s.sub || ''}</p></div>`;
                }
                div.onmousedown = (e) => { e.preventDefault(); searchInput.value = s.value; hideSuggest(); currentPage = 1; loadHistory(1); };
                suggestDropdown.appendChild(div);
            });
            positionSuggest();
            suggestDropdown.style.display = 'block';
        }

        let suggestTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(suggestTimeout);
            const val = this.value.trim();
            currentPage = 1;
            loadHistory(1);
            if (val.length < 1) { hideSuggest(); return; }
            suggestTimeout = setTimeout(async () => {
                const res = await fetch(`sales_history_handler.php?action=suggest&q=${encodeURIComponent(val)}`);
                const data = await res.json();
                renderSuggest(data.suggestions);
            }, 180);
        });

        searchInput.addEventListener('keydown', function(e) {
            const items = suggestDropdown.querySelectorAll('.sh-suggest-item');
            if (e.key === 'ArrowDown') { e.preventDefault(); suggestActiveIdx = Math.min(suggestActiveIdx + 1, items.length - 1); items.forEach((el, i) => el.classList.toggle('active', i === suggestActiveIdx)); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); suggestActiveIdx = Math.max(suggestActiveIdx - 1, -1); items.forEach((el, i) => el.classList.toggle('active', i === suggestActiveIdx)); }
            else if (e.key === 'Enter' && suggestActiveIdx >= 0) { e.preventDefault(); items[suggestActiveIdx].onmousedown(e); }
            else if (e.key === 'Escape') hideSuggest();
        });

        document.addEventListener('click', function(e) {
            if (!searchWrapper.contains(e.target) && !suggestDropdown.contains(e.target)) hideSuggest();
        });
        window.addEventListener('scroll', () => { if (suggestDropdown.style.display !== 'none') positionSuggest(); }, true);
        window.addEventListener('resize', () => { if (suggestDropdown.style.display !== 'none') positionSuggest(); });
        document.getElementById('date_from').addEventListener('change', () => {
             currentPage = 1;
             loadHistory(1);
        });
        document.getElementById('date_to').addEventListener('change', () => {
             currentPage = 1;
             loadHistory(1);
        });
        document.getElementById('method_filter').addEventListener('change', () => {
             currentPage = 1;
             loadHistory(1);
        });
        document.getElementById('status_filter').addEventListener('change', () => {
             currentPage = 1;
             loadHistory(1);
        });
        document.getElementById('order_status_filter').addEventListener('change', () => {
             currentPage = 1;
             loadHistory(1);
        });

        async function loadHistory(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const from = document.getElementById('date_from').value;
            const to = document.getElementById('date_to').value;
            
            // Load summaries whenever date changes (or on refresh)
            loadSummaries(from, to);

            const method = document.getElementById('method_filter').value;
            const status = document.getElementById('status_filter').value;
            const order_status = document.getElementById('order_status_filter').value;
            const res = await fetch(`sales_history_handler.php?action=fetch&search=${search}&from=${from}&to=${to}&method=${method}&status=${status}&order_status=${order_status}&page=${page}`);
            const data = await res.json();
            const tbody = document.getElementById('historyBody');
            tbody.innerHTML = '';

            if(data.sales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-8 py-20 text-center text-blue-300/20 font-black uppercase tracking-[0.2em] italic">No transaction records detected in this audit window.</td></tr>';
                return;
            }

            data.sales.forEach(sale => {
                const statusClass = sale.payment_status === 'approved' ? 'bg-emerald-500 shadow-emerald-500/20' : 
                                  sale.payment_status === 'pending' ? 'bg-amber-400 shadow-amber-500/20' : 'bg-rose-500 shadow-rose-500/20';

                const methodClasses = {
                    'cash': 'text-emerald-400 border-emerald-500/20 bg-emerald-500/10',
                    'card': 'text-indigo-400 border-indigo-500/20 bg-indigo-500/10',
                    'cheque': 'text-amber-400 border-amber-500/20 bg-amber-500/10',
                    'credit': 'text-rose-400 border-rose-500/20 bg-rose-500/10'
                };
                const methodClass = methodClasses[sale.payment_method.toLowerCase()] || 'text-blue-300 border-white/10 bg-white/5';
                                  
                tbody.innerHTML += `
                    <tr class="hover:bg-blue-50/50 transition-all group">
                        <td class="px-6 py-8">
                            <span class="font-mono text-[10px] font-black text-blue-600 tracking-tighter whitespace-nowrap uppercase px-3 py-1 bg-blue-50 rounded-lg border border-blue-100">TRX-${sale.id}</span>
                        </td>
                        <td class="px-8 py-8">
                            <div class="flex items-center gap-2">
                              
                                <p class="font-black text-slate-800 text-sm tracking-tight">${sale.cust_name || 'Anonymous Guest'}</p>
                            </div>
                        </td>
                        <td class="px-8 py-8 hidden lg:table-cell">
                            <div class="flex items-center gap-2">
                                <svg class="w-3 h-3 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <p class="text-[9px] text-slate-800 font-black uppercase tracking-wider">${sale.cust_contact || 'Private Line'}</p>
                            </div>
                        </td>
                        <td class="px-8 py-8 text-slate-700 hidden xl:table-cell">
                             <p class="font-black text-[11px] tracking-tight uppercase">${sale.officer_name || 'System'}</p>
                        </td>
                        <td class="px-8 py-8 text-slate-600 hidden md:table-cell">
                            <p class="font-bold text-[11px] tracking-widest">${new Date(sale.created_at).toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                        </td>
                        <td class="px-2 py-8 text-left">
                            <span class="px-3 py-1 border rounded-lg text-[9px] font-black uppercase tracking-widest ${methodClass}">${sale.payment_method}</span>
                        </td>
                        <td class="px-8 py-8 text-right font-black text-rose-600 tracking-widest text-sm hidden xl:table-cell">${parseFloat(sale.discount || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="px-8 py-8 text-right font-black text-blue-800 tracking-widest text-sm">${parseFloat(sale.final_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="px-8 py-8 text-center text-nowrap hidden lg:table-cell">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest text-slate-600 border border-slate-200 bg-white shadow-sm">${sale.status === 'pending' ? 'DRAFTED' : 'COMPLETED'}</span>
                        </td>
                        <td class="px-8 py-8 text-center text-nowrap">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest text-white ${statusClass} shadow-lg">${sale.payment_status}</span>
                        </td>
                        <td class="px-8 py-8 text-right">
                            <div class="flex justify-end items-center gap-3">
                                <button onclick="viewSaleItems(${sale.id})" class="p-3 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95" title="View Manifest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button onclick='openEditModal(${JSON.stringify(sale)})' class="p-3 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:scale-95" title="Modify Record">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button onclick="confirmDelete(${sale.id})" class="p-3 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm active:scale-95" title="Purge & Reverse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            renderPagination(data.pagination);
        }

        async function loadSummaries(from, to) {
            const res = await fetch(`sales_history_handler.php?action=fetch_summaries&from=${from}&to=${to}`);
            const data = await res.json();
            if(!data.success) return;

            const s = data.summaries;
            const total = s.cash + s.card + s.approved_credit + s.approved_cheque + s.pending_credit + s.pending_cheque;
            const approved = s.cash + s.card + s.approved_credit + s.approved_cheque;
            const pending = s.pending_credit + s.pending_cheque;

            // Update Nav Mini Cards
            document.getElementById('nav_total').innerText = '' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('nav_approved').innerText = '' + approved.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('nav_pending').innerText = '' + pending.toLocaleString(undefined, {minimumFractionDigits: 2});

            // Update Big Cards
            document.getElementById('card_cash').innerText = '' + s.cash.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('card_card').innerText = '' + s.card.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('card_app_credit').innerText = '' + s.approved_credit.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('card_app_cheque').innerText = '' + s.approved_cheque.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('card_pend_credit').innerText = '' + s.pending_credit.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('card_pend_cheque').innerText = '' + s.pending_cheque.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        function renderPagination(pg) {
            const container = document.getElementById('paginationControls');
            let html = '<div class="flex items-center gap-2">';
            
            // Previous Button
            if(pg.current_page > 1) {
                html += `<button onclick="loadHistory(${pg.current_page - 1})" class="p-2 bg-slate-100 border border-slate-200 rounded-lg hover:bg-slate-200 text-blue-800 transition-all font-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>`;
            }

            // Page Numbers
            for(let i = 1; i <= pg.total_pages; i++) {
                const activeClass = i === pg.current_page ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200';
                html += `<button onclick="loadHistory(${i})" class="w-9 h-9 flex items-center justify-center border rounded-lg text-sm font-bold transition-all ${activeClass}">${i}</button>`;
            }

            // Next Button
            if(pg.current_page < pg.total_pages) {
                html += `<button onclick="loadHistory(${pg.current_page + 1})" class="p-2 bg-slate-100 border border-slate-200 rounded-lg hover:bg-slate-200 text-blue-800 transition-all font-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`;
            }
            
            html += '</div>';
            container.innerHTML = html;
        }

        async function viewSaleItems(id) {
            const res = await fetch(`sales_history_handler.php?action=fetch_items&id=${id}`);
            const data = await res.json();
            
            if(!data.success) return;

            let html = `
                <div class="overflow-x-auto mt-4 px-1 text-left">
                    <table class="w-full min-w-[500px] text-[10px] sm:text-[11px] border-collapse">
                        <thead>
                            <tr class="text-blue-300/50 uppercase tracking-widest border-b border-white/10 italic">
                                <th class="py-2">Item</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-3 text-right">Net Price</th>
                                <th class="py-3 text-right">Discount</th>
                                <th class="py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
            `;

            data.items.forEach(item => {
                const soldPrice = parseFloat(item.total_price) / parseFloat(item.qty);
                html += `
                    <tr>
                        <td class="py-3">
                            <p class="font-black text-slate-800">${item.name}</p>
                            <p class="text-[9px] opacity-80">${item.barcode}</p>
                        </td>
                        <td class="py-3 text-center font-bold">${item.qty}</td>
                        <td class="py-3 text-right font-mono text-blue-800">${soldPrice.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="py-3 text-right font-mono text-rose-500">-${parseFloat(item.discount || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="py-3 text-right font-mono text-emerald-800">${parseFloat(item.total_price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;

            Swal.fire({
                title: '<span class="text-xl font-black uppercase tracking-tighter text-blue-400">Transaction Manifest</span>',
                html: html,
                width: '600px',
                confirmButtonText: 'Done',
                confirmButtonColor: '#3b82f6',
                customClass: { 
                    popup: 'rounded-[2rem] bg-white text-slate-800 shadow-2xl border border-slate-100 px-4',
                    confirmButton: 'rounded-xl font-black uppercase text-[10px] px-8 py-3 tracking-widest w-full bg-blue-600 text-white'
                }
            });
        }

        function openEditModal(sale) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
            document.getElementById('editId').innerText = `TRX-${sale.id}`;
            document.getElementById('formSaleId').value = sale.id;
            document.getElementById('formAmount').value = sale.final_amount;
            document.getElementById('formMethod').value = sale.payment_method;
            document.getElementById('formStatus').value = sale.payment_status;
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        document.getElementById('editForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('action', 'edit');
            
            const res = await fetch('sales_history_handler.php', { method: 'POST', body: fd });
            const data = await res.json();
            
            if(data.success) {
                Swal.fire({
                    title: 'Update Authorized',
                    text: 'Sale record and audit log updated successfully.',
                    icon: 'success',
                    customClass: { popup: 'rounded-[1.5rem] bg-slate-900 text-white' }
                });
                closeModal();
                loadHistory();
            } else {
                Swal.fire('Error', data.message || 'Update failed', 'error');
            }
        });

        async function confirmDelete(saleId) {
            const { value: reason } = await Swal.fire({
                title: 'Authorize Purge TRX-' + saleId + '?',
                text: 'This will reverse inventory stocks and permanently log this removal. Provide authorization reason:',
                input: 'textarea',
                inputPlaceholder: 'Enter professional reason for reversal...',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Yes, Purge & Reverse',
                customClass: { 
                    popup: 'rounded-[2rem] bg-slate-900 border border-white/10 text-white',
                    input: 'bg-white/5 border-white/10 text-white rounded-xl h-32'
                }
            });

            if (reason) {
                const fd = new FormData();
                fd.append('sale_id', saleId);
                fd.append('reason', reason);
                fd.append('action', 'delete');

                const res = await fetch('sales_history_handler.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if(data.success) {
                    Swal.fire({
                        title: 'Purged',
                        text: 'Record deleted and stocks reversed.',
                        icon: 'success',
                        customClass: { popup: 'rounded-[1.5rem] bg-slate-900 text-white' }
                    });
                    loadHistory();
                } else {
                    Swal.fire('Error', data.message || 'Deletion failed', 'error');
                }
            }
        }
    </script>
</body>
</html>
