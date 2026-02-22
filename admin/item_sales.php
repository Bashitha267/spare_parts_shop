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
    <title>Sale Items History - Vehicle Square</title>
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
            background: url('public/admin_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
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
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 2rem;
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.08);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white !important;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.25rem 1.5rem !important;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-blue-50 rounded-xl transition-all text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Sale Items History</h1>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="exportExcel()" class="bg-white text-emerald-600 px-6 py-2.5 rounded-xl text-xs font-black hover:bg-emerald-50 transition-all uppercase tracking-widest border-2 border-emerald-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export to Excel
                </button>
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative z-10">
        
        <!-- Filters -->
        <div class="glass-card p-6 border-2 border-white flex flex-col md:flex-row gap-6 items-center">
            <div class="flex flex-wrap items-center gap-4 w-full">
                <div class="flex-grow md:flex-none">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Product Type</label>
                    <select id="typeFilter" onchange="loadSales()" class="w-full md:w-48 px-6 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                        <option value="all">All Products</option>
                        <option value="oil">Oil Only</option>
                        <option value="spare_part">Spare Parts Only</option>
                    </select>
                </div>

                <div class="flex-grow md:flex-none">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Time Period</label>
                    <select id="periodFilter" onchange="loadSales()" class="w-full md:w-48 px-6 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                        <option value="today">Today's Sales</option>
                        <option value="monthly">This Month</option>
                        <option value="yearly">This Year</option>
                    </select>
                </div>

                <div class="flex-grow md:flex-none">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Sort By</label>
                    <select id="sortFilter" onchange="loadSales()" class="w-full md:w-48 px-6 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                        <option value="most_sold">Most Sold Items</option>
                        <option value="least_sold">Least Sold Items</option>
                        <option value="highest_earning">Highest Earning</option>
                    </select>
                </div>

                <div class="flex-grow">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Quick Search</label>
                    <div class="relative">
                        <input type="text" id="searchInput" oninput="loadSales()" class="w-full px-6 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all pl-12" placeholder="Search by name or barcode...">
                        <svg class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="glass-card overflow-hidden border-4 border-blue-500/20">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em]">Product Details</th>
                            <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em]">Product Type</th>
                            <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em]">Category</th>
                            <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-center">Total Sold Qty</th>
                            <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-center">Current Inventory</th>
                            <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-right">Total Earned (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody" class="divide-y divide-slate-100">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadSales();
        });

        async function loadSales() {
            const type = document.getElementById('typeFilter').value;
            const period = document.getElementById('periodFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const search = document.getElementById('searchInput').value;

            const tableBody = document.getElementById('salesTableBody');
            tableBody.innerHTML = '<tr><td colspan="6" class="px-8 py-10 text-center text-slate-400 font-bold uppercase tracking-widest animate-pulse italic">Analyzing sale data...</td></tr>';

            try {
                const response = await fetch(`item_sales_handler.php?action=fetch_item_sales&type=${type}&period=${period}&sort=${sort}&search=${encodeURIComponent(search)}`);
                const result = await response.json();

                if (result.success) {
                    tableBody.innerHTML = '';
                    if (result.data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="px-8 py-20 text-center text-slate-400 font-black uppercase tracking-[0.3em]">No sales recorded for this period</td></tr>';
                        return;
                    }

                    result.data.forEach(item => {
                        let typeBadge = item.type === 'oil' 
                            ? `<span class="px-3 py-1 bg-blue-100/50 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-black uppercase tracking-widest">Oil </span>`
                            : `<span class="px-3 py-1 bg-indigo-100/50 text-green-700 border border-indigo-200 rounded-lg text-[10px] font-black uppercase tracking-widest">Spare Parts</span>`;

                        let catBadge = '';
                        if(item.type === 'oil') {
                            catBadge = item.oil_type === 'can'
                                ? `<span class="px-3 py-1 bg-cyan-100/50 text-cyan-700 border border-cyan-200 rounded-lg text-[9px] font-black uppercase italic">Cans</span>`
                                : `<span class="px-3 py-1 bg-amber-100/50 text-amber-700 border border-amber-200 rounded-lg text-[9px] font-black uppercase italic">Loose</span>`;
                        } else {
                            catBadge = `<span class="px-3 py-1 bg-slate-100/50 text-slate-600 border border-slate-200 rounded-lg text-[9px] font-black uppercase italic">Parts</span>`;
                        }

                        const row = `
                            <tr class="hover:bg-blue-50/50 transition-all group">
                                <td class="px-8 py-3">
                                    <p class="font-black text-blue-800 text-sm tracking-tight">${item.name}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-bold text-blue-600/40 uppercase tracking-widest">${item.brand || 'No Brand'}</span>
                                        <span class="text-[9px] font-mono text-slate-800">#${item.barcode}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-3">
                                    ${typeBadge}
                                </td>
                                <td class="px-8 py-3">
                                    ${catBadge}
                                    </td>
                                <td class="px-8 py-3 text-center">
                                    <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-2xl font-black text-sm border-2 border-blue-200">
                                        ${parseFloat(item.total_qty).toFixed(2)}
                                    </span>
                                </td>
                                <td class="px-8 py-3 text-center">
                                    <span class="inline-block px-4 py-2 ${item.current_stock > 10 ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-rose-50 text-rose-700 border-rose-100'} rounded-2xl font-black text-sm border-2">
                                        ${parseFloat(item.current_stock).toFixed(2)}
                                    </span>
                                </td>
                                <td class="px-8 py-3 text-right">
                                    <p class="text-lg font-black text-blue-800 tracking-tighter">Rs. ${parseFloat(item.total_revenue).toLocaleString(undefined, {minimumFractionDigits: 2})}</p>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            } catch (error) {
                console.error('Error fetching sales:', error);
                tableBody.innerHTML = '<tr><td colspan="5" class="px-8 py-10 text-center text-rose-500 font-black">FAIL: COULD NOT CONNECT TO DATA STREAM</td></tr>';
            }
        }

        function exportExcel() {
            const type = document.getElementById('typeFilter').value;
            const period = document.getElementById('periodFilter').value;
            const search = document.getElementById('searchInput').value;
            window.location.href = `item_sales_handler.php?action=export_excel&type=${type}&period=${period}&search=${encodeURIComponent(search)}`;
        }
    </script>
</body>
</html>
