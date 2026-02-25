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
            opacity: 1 !important;
            color: white;
            padding: 1.25rem 1.25rem !important;
            font-size: 0.75rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: left;
            border: none;
        }
        th:first-child { border-top-left-radius: 1rem; }
        th:last-child { border-top-right-radius: 1rem; }
        
        tr:nth-child(even) {
            background-color: rgba(248, 250, 252, 0.8);
        }
        
        td {
            padding: 1.25rem 1.25rem !important;
            color: #0f172a;
            border-bottom: 1px solid rgba(226, 232, 240, 0.4);
            font-size: 0.88rem;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(59, 130, 246, 0.05); }

        .pagination-btn {
            padding: 0.5rem 0.875rem;
            border-radius: 0.75rem;
            font-weight: 700;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
        }
        .pagination-active {
            background-color: #2563eb;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
            transform: scale(1.1);
            z-index: 10;
        }
        .pagination-inactive {
            background-color: white;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .pagination-inactive:hover {
            background-color: #eff6ff;
            color: #2563eb;
        }

        .suggest-dropdown {
            position: absolute;
            left: 0; right: 0;
            top: calc(100% + 4px);
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 28px -6px rgba(0,0,0,0.1);
            z-index: 100;
            overflow: hidden;
            animation: dropIn 0.13s ease-out;
        }
        @keyframes dropIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        .suggest-item {
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            transition: background 0.1s;
        }
        .suggest-item:last-child { border-bottom: none; }
        .suggest-item:hover, .suggest-item.active { background: #eff6ff; color: #2563eb; }

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

    <nav class="glass-nav sticky top-0 z-30">
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
                    <p class="text-[8px] sm:text-[9px] text-slate-600 font-bold uppercase tracking-widest">All past sales</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 scrollbar-hide">
                <!-- Total Today -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-2.5 shadow-lg shadow-blue-500/20 shrink-0">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-white/10 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[7px] font-black text-white/70 uppercase tracking-widest leading-none">Today</p>
                        <p id="today_total" class="text-[10px] sm:text-xs md:text-sm font-black text-white leading-none mt-0.5 whitespace-nowrap">Rs. 0.00</p>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-2.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <div>
                        <p class="text-[7px] font-black opacity-60 uppercase tracking-widest leading-none">Apprv.</p>
                        <p id="today_approved" class="text-[10px] sm:text-xs md:text-sm font-black leading-none mt-0.5 whitespace-nowrap">Rs. 0.00</p>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-rose-50 text-rose-700 border border-rose-200 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-2.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="text-[7px] font-black opacity-60 uppercase tracking-widest leading-none">Pend.</p>
                        <p id="today_pending" class="text-[10px] sm:text-xs md:text-sm font-black leading-none mt-0.5 whitespace-nowrap">Rs. 0.00</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="pt-32 px-3 md:px-6 max-w-7xl mx-auto animate-fade relative z-10 pb-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-8">
            <!-- Cash -->
            <div class="glass-card p-4 sm:p-6 border-l-4 border-emerald-500 shadow-xl shadow-emerald-500/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-emerald-500 transition-colors">Cash</p>
                <h3 id="card_cash" class="text-base sm:text-xl font-black text-emerald-600 tracking-tighter">Rs. 0.00</h3>
            </div>
            <!-- Card -->
            <div class="glass-card p-4 sm:p-6 border-l-4 border-indigo-500 shadow-xl shadow-indigo-500/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-indigo-500 transition-colors">Card</p>
                <h3 id="card_card" class="text-base sm:text-xl font-black text-indigo-600 tracking-tighter">Rs. 0.00</h3>
            </div>
            <!-- Approved Credit -->
            <div class="glass-card p-4 sm:p-6 border-l-4 border-blue-500 shadow-xl shadow-blue-500/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-blue-500 transition-colors">App. Credit</p>
                <h3 id="card_app_credit" class="text-base sm:text-xl font-black text-blue-600 tracking-tighter">Rs. 0.00</h3>
            </div>
            <!-- Approved Cheque -->
            <div class="glass-card p-4 sm:p-6 border-l-4 border-amber-500 shadow-xl shadow-amber-500/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-amber-500 transition-colors">App. Cheque</p>
                <h3 id="card_app_cheque" class="text-base sm:text-xl font-black text-amber-600 tracking-tighter">Rs. 0.00</h3>
            </div>
            <!-- Pending Credit -->
            <div class="glass-card p-4 sm:p-6 border-l-4 border-rose-400 shadow-xl shadow-rose-400/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-rose-500 transition-colors">Pend. Credit</p>
                <h3 id="card_pend_credit" class="text-base sm:text-xl font-black text-rose-500 tracking-tighter">Rs. 0.00</h3>
            </div>
            <!-- Pending Cheque -->
            <div class="glass-card p-4 sm:p-6 border-l-4 border-orange-400 shadow-xl shadow-orange-400/10 hover:scale-[1.02] transition-all cursor-default group">
                <p class="text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-orange-500 transition-colors">Pend. Cheque</p>
                <h3 id="card_pend_cheque" class="text-base sm:text-xl font-black text-orange-500 tracking-tighter">Rs. 0.00</h3>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="glass-card p-4 md:p-6 mb-8 mt-2 relative z-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
                <div class="sm:col-span-2 md:col-span-1 relative" id="searchWrapper">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Search Records</label>
                    <input type="text" id="searchInput" autocomplete="off" placeholder="Name or Contact..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold placeholder:text-slate-300">
                    <div id="suggestDropdown" class="suggest-dropdown" style="display:none"></div>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">From Date</label>
                    <input type="date" id="dateFrom" onchange="loadSales(1)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">To Date</label>
                    <input type="date" id="dateTo" onchange="loadSales(1)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Payment Mode</label>
                    <select id="paymentMethod" onchange="loadSales(1)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3 outline-none transition-all font-bold">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <div class="md:col-span-1 flex gap-2">
                    <button onclick="resetFilters()" title="Reset" class="flex-grow bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black hover:bg-slate-200 transition-all uppercase tracking-widest border border-slate-200 flex items-center justify-center gap-2 py-3.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span class="md:hidden lg:inline">Reset</span>
                    </button>
                    <button onclick="loadSales(1)" class="flex-grow bg-blue-600 text-white rounded-xl text-[10px] font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 uppercase tracking-widest py-3.5 ring-4 ring-blue-600/10">
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="glass-card overflow-hidden relative z-10">
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
                    <tbody id="salesBody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            <div id="pagination" class="px-6 py-5 bg-white/50 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <!-- Pagination Buttons -->
            </div>
        </div>
    </div>

    <!-- Sale Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-black/30 backdrop-blur-md z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white/90 backdrop-blur-xl w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden anim-pop border border-white/50">
            <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-blue-500 to-blue-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Sale Details
                    </h3>
                    <p id="modal_sale_id" class="text-xs text-white/60 font-bold mt-0.5"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button id="modalPrintBtn" class="p-2 text-white/90 hover:text-white hover:bg-white/20 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-5 h-5 " fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" ></path></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest hidden bg-white sm:inline text-blue-600 py-3 px-2 ">Print Invoice</span>
                    </button>
                    <button onclick="closeDetailModal()" class="p-2 text-white/60 hover:text-white hover:bg-white/20 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200">
                            <th class="pb-3 px-0">Product</th>
                            <th class="pb-3 text-center px-0">Qty</th>
                            <th class="pb-3 text-right px-0">Price</th>
                            <th class="pb-3 text-right px-0">Discount</th>
                            <th class="pb-3 text-right px-0">Total</th>
                        </tr>
                    </thead>
                    <tbody id="detailItems" class="divide-y divide-slate-100"></tbody>
                </table>
                <div id="modalSummary" class="mt-6 pt-5 border-t-2 border-slate-200 space-y-2 text-right"></div>
            </div>
        </div>
    </div>

    <!-- Receipt Print Area (HIDDEN) -->
    <div id="printArea" class="hidden">
        <div class="receipt-bill p-4 text-center">
            <h2 class="font-black text-lg">VEHICLE SQUARE</h2>
            <p class="text-[10px]">No 123, Main Street, Town Name</p>
            <p class="text-[10px]">Tel: 07x-xxxxxxx</p>
            <div class="border-b border-dashed border-black my-2"></div>
            <div class="text-left text-[10px] space-y-1">
                <p>Date: <span id="bill_date"></span></p>
                <p>Time: <span id="bill_time"></span></p>
                <p>Invoice #: <span id="bill_id"></span></p>
                <p>Customer: <span id="bill_cust"></span></p>
                <p>Contact: <span id="bill_phone"></span></p>
                <p>Mode: <span id="bill_pay"></span></p>
            </div>
            <div class="border-b border-dashed border-black my-2"></div>
            <table class="w-full text-left text-[10px]">
                <thead>
                    <tr class="font-bold border-b border-slate-200">
                        <th class="py-1">ITEM</th>
                        <th class="py-1 text-center">QTY</th>
                        <th class="py-1 text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody id="bill_items"></tbody>
            </table>
            <div class="border-b border-dashed border-black my-2"></div>
            <div class="text-[10px] space-y-1">
                <div class="flex justify-between font-bold">
                    <span>Total Bill:</span>
                    <span id="bill_total">LKR 0.00</span>
                </div>
                <div class="flex justify-between">
                    <span>Discount:</span>
                    <span id="bill_discount">LKR 0.00</span>
                </div>
                <div class="flex justify-between text-lg font-black border-t border-black pt-1">
                    <span>NET TOTAL:</span>
                    <span id="bill_net">LKR 0.00</span>
                </div>
            </div>
            <div class="border-b border-dashed border-black my-4"></div>
            <p class="text-[10px] font-bold">THANK YOU! COME AGAIN</p>
        </div>
    </div>

    <script>
        async function updateTodayTotal() {
            const fromDate = document.getElementById('dateFrom').value || new Date().toISOString().split('T')[0];
            const res = await fetch(`sales_handler.php?action=get_today_total&date=${fromDate}`);
            const data = await res.json();
            
            document.getElementById('today_total').innerText = 'Rs. ' + data.total;
            document.getElementById('today_approved').innerText = 'Rs. ' + data.approved;
            document.getElementById('today_pending').innerText = 'Rs. ' + data.pending;
            
            // Populate summary cards
            if(data.summaries) {
                const s = data.summaries;
                document.getElementById('card_cash').innerText = 'Rs. ' + numberFormat(s.cash);
                document.getElementById('card_card').innerText = 'Rs. ' + numberFormat(s.card);
                document.getElementById('card_app_credit').innerText = 'Rs. ' + numberFormat(s.approved_credit);
                document.getElementById('card_app_cheque').innerText = 'Rs. ' + numberFormat(s.approved_cheque);
                document.getElementById('card_pend_credit').innerText = 'Rs. ' + numberFormat(s.pending_credit);
                document.getElementById('card_pend_cheque').innerText = 'Rs. ' + numberFormat(s.pending_cheque);
            }
        }

        let currentPage = 1;
        document.addEventListener('DOMContentLoaded', () => {
            // Set default dates to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateFrom').value = today;
            document.getElementById('dateTo').value = today;

            updateTodayTotal();
            loadSales(1);

            const searchInput = document.getElementById('searchInput');
            const suggestDropdown = document.getElementById('suggestDropdown');
            let debounceTimer;
            let activeIdx = -1;

            window.resetFilters = function() {
                const today = new Date().toISOString().split('T')[0];
                searchInput.value = '';
                document.getElementById('dateFrom').value = today;
                document.getElementById('dateTo').value = today;
                document.getElementById('paymentMethod').value = '';
                suggestDropdown.style.display = 'none';
                updateTodayTotal();
                loadSales(1);
            };

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const q = this.value.trim();
                activeIdx = -1;

                debounceTimer = setTimeout(() => { loadSales(1); }, 300);

                if (q.length < 2) {
                    suggestDropdown.style.display = 'none';
                    return;
                }

                fetch(`sales_handler.php?action=search_customer&query=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.customers.length > 0) {
                            suggestDropdown.innerHTML = '';
                            data.customers.forEach(c => {
                                // Add by name
                                const item1 = document.createElement('div');
                                item1.className = 'suggest-item';
                                item1.innerHTML = `<div class="font-bold">${c.name}</div><div class="text-[9px] opacity-60">${c.contact}</div>`;
                                item1.onclick = () => {
                                    searchInput.value = c.name;
                                    suggestDropdown.style.display = 'none';
                                    loadSales(1);
                                };
                                suggestDropdown.appendChild(item1);

                                // Add by phone
                                const item2 = document.createElement('div');
                                item2.className = 'suggest-item';
                                item2.innerHTML = `<div class="font-bold">${c.contact}</div><div class="text-[9px] opacity-60">${c.name}</div>`;
                                item2.onclick = () => {
                                    searchInput.value = c.contact;
                                    suggestDropdown.style.display = 'none';
                                    loadSales(1);
                                };
                                suggestDropdown.appendChild(item2);
                            });
                            suggestDropdown.style.display = 'block';
                        } else {
                            suggestDropdown.style.display = 'none';
                        }
                    });
            });

            searchInput.addEventListener('keydown', function(e) {
                const items = suggestDropdown.querySelectorAll('.suggest-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIdx = Math.min(activeIdx + 1, items.length - 1);
                    items.forEach((el, i) => el.classList.toggle('active', i === activeIdx));
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIdx = Math.max(activeIdx - 1, 0);
                    items.forEach((el, i) => el.classList.toggle('active', i === activeIdx));
                } else if (e.key === 'Enter' && activeIdx >= 0) {
                    e.preventDefault();
                    items[activeIdx].click();
                } else if (e.key === 'Escape') {
                    suggestDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', (e) => {
                if (!document.getElementById('searchWrapper').contains(e.target)) {
                    suggestDropdown.style.display = 'none';
                }
            });
        });

        async function loadSales(page = 1) {
            currentPage = page;
            const search = document.getElementById('searchInput').value;
            const from = document.getElementById('dateFrom').value;
            const to = document.getElementById('dateTo').value;
            const method = document.getElementById('paymentMethod').value;

            // Update summaries whenever filters change (if date is involved)
            updateTodayTotal();

            const res = await fetch(`sales_handler.php?action=fetch_sales&page=${page}&search=${encodeURIComponent(search)}&from=${from}&to=${to}&method=${method}`);
            const data = await res.json();
            
            const tbody = document.getElementById('salesBody');
            tbody.innerHTML = '';
            
            if (!data.sales || data.sales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-20 text-slate-400 font-bold uppercase tracking-widest text-xs">No transactions found</td></tr>';
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            data.sales.forEach(row => {
                const methodStyles = {
                    'cash': 'bg-emerald-500 text-white shadow-emerald-500/20',
                    'card': 'bg-blue-600 text-white shadow-blue-500/20',
                    'cheque': 'bg-amber-500 text-white shadow-amber-500/20',
                    'credit': 'bg-rose-500 text-white shadow-rose-500/20'
                };
                const style = methodStyles[row.payment_method] || 'bg-slate-500 text-white';
                
                tbody.innerHTML += `
                    <tr class="group animate-fade">
                        <td><span class="bg-gradient-to-r from-blue-400 to-blue-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black tracking-widest shadow-md shadow-blue-500/20">#${row.id}</span></td>
                        <td>
                            <p class="text-sm font-bold text-slate-800">${formatDate(row.created_at)}</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">${formatTime(row.created_at)}</p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[10px] font-black shadow-sm">
                                    ${(row.cust_name || 'G').charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">${row.cust_name || 'Guest'}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">${row.cust_phone || ''}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-black px-4 py-2 ${style} rounded-full uppercase tracking-widest shadow-lg">
                                ${row.payment_method}
                            </span>
                        </td>
                        <td class="text-right">
                            <p class="text-lg font-black text-blue-800 tracking-tight">Rs. ${numberFormat(row.final_amount)}</p>
                            ${row.discount > 0 ? `<p class="text-xs text-rose-500 font-bold mt-0.5">Saved Rs. ${numberFormat(row.discount)}</p>` : ''}
                        </td>
                        <td class="text-center">
                            <button onclick="viewDetails(${row.id})" class="p-2.5 text-white bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl hover:shadow-lg hover:shadow-blue-500/30 hover:scale-105 transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                `;
            });

            renderPagination(data.pagination);
        }

        function renderPagination(pg) {
            const container = document.getElementById('pagination');
            let html = `<p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Page ${pg.current_page} of ${pg.total_pages}</p>`;
            html += `<div class="flex items-center gap-1.5">`;
            
            for(let i = 1; i <= pg.total_pages; i++) {
                if (pg.total_pages > 5 && (i > 3 && i < pg.total_pages)) {
                   if (i === 4) html += `<span class="px-2">...</span>`;
                   continue;
                }
                const active = i === pg.current_page ? 'pagination-active' : 'pagination-inactive';
                html += `<button onclick="loadSales(${i})" class="pagination-btn ${active}">${i}</button>`;
            }
            
            html += `</div>`;
            container.innerHTML = html;
        }

        function formatDate(str) { 
            const d = new Date(str);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
        function formatTime(str) {
            const d = new Date(str);
            return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        }

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
                            <p class="font-bold text-slate-800 uppercase tracking-tight">${item.name}</p>
                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">${item.brand}</p>
                        </td>
                        <td class="py-3.5 text-center">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-black">${item.qty}</span>
                        </td>
                        <td class="py-3.5 text-right font-bold text-slate-500">Rs. ${numberFormat(item.unit_price)}</td>
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

            // Update print button click handler
            const printBtn = document.getElementById('modalPrintBtn');
            printBtn.onclick = () => printInvoice(data);

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function printInvoice(data) {
            const sale = data.sale;
            const items = data.items;
            const now = new Date(sale.created_at);
            
            document.getElementById('bill_date').innerText = now.toLocaleDateString();
            document.getElementById('bill_time').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            document.getElementById('bill_id').innerText = 'SALE-' + sale.id;
            document.getElementById('bill_cust').innerText = sale.cust_name || 'Walk-in Customer';
            document.getElementById('bill_phone').innerText = sale.cust_contact || 'N/A';
            document.getElementById('bill_pay').innerText = sale.payment_method.toUpperCase();
            
            const list = document.getElementById('bill_items');
            list.innerHTML = '';
            let sub = 0; 
            let totalDisc = parseFloat(sale.discount) || 0;
            
            items.forEach(item => {
                sub += (parseFloat(item.qty) * parseFloat(item.unit_price));
                list.innerHTML += `
                    <tr>
                        <td class="py-1 uppercase">${item.name}</td>
                        <td class="py-1 text-center">${item.qty}</td>
                        <td class="py-1 text-right">${numberFormat(item.total_price)}</td>
                    </tr>
                `;
            });
            
            document.getElementById('bill_total').innerText = 'LKR ' + numberFormat(sub);
            document.getElementById('bill_discount').innerText = 'LKR ' + numberFormat(totalDisc);
            document.getElementById('bill_net').innerText = 'LKR ' + numberFormat(sale.final_amount);

            // Trigger window print on the hidden div content
            const printContent = document.getElementById('printArea').innerHTML;
            const printWindow = window.open('', '_blank', 'width=400,height=600');
            printWindow.document.write(`<html><head><title>Print Receipt</title><script src="https://cdn.tailwindcss.com"><\/script></head><body>${printContent}</body></html>`);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }
        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
