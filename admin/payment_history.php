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
    <title>Payment Approval History - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
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
        <div class="px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Approval History</h1>
            </div>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-10 relative z-10">
        
        <!-- Search \u0026 Filter Bar -->
        <div class="glass-card p-6 rounded-[2rem] flex flex-wrap lg:flex-nowrap items-center gap-4">
            <div class="relative w-full lg:w-72 flex-shrink-0" id="searchWrapper">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="search" autocomplete="off" placeholder="Search TRX, Name or Contact..." class="w-full pl-12 pr-4 py-3 rounded-xl text-xs font-bold placeholder:text-slate-400">
            </div>
            
            <div class="flex flex-wrap lg:flex-nowrap items-center gap-3 w-full lg:flex-grow">
                <input type="date" id="date_filter" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer" title="Date">
                <input type="month" id="month_filter" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer" title="Month">

                <select id="year_filter" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">
                    <option value="">Year</option>
                    <?php for($y=date('Y'); $y>=2020; $y--): ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>

                <select id="method_filter" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">
                    <option value="all">Mode</option>
                    <option value="cheque">Cheque</option>
                    <option value="credit">Credit</option>
                </select>

                <select id="status_filter" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">
                    <option value="all">Status</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>

                <div class="flex items-center gap-2 ml-auto">
                    <button onclick="clearTemporalFilters()" class=" flex flex-row gap-2  items-center p-2 bg-blue-600 border border-slate-200 rounded-xl text-white hover:bg-blue-800 transition-all font-black" title="Reset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- History Table -->
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
                            <th class="text-center">State</th>
                            <th class="text-right">Settled At</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody" class="divide-y divide-white/10">
                        <!-- AJAX Content -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', loadHistory);

        // ---- Autosuggest ----
        const searchInput = document.getElementById('search');
        const searchWrapper = document.getElementById('searchWrapper');
        const suggestDropdown = document.createElement('div');
        suggestDropdown.className = 'ph-suggest-dropdown';
        suggestDropdown.style.display = 'none';
        suggestDropdown.style.minWidth = '260px';
        suggestDropdown.style.maxHeight = '280px';
        suggestDropdown.style.overflowY = 'auto';
        document.body.appendChild(suggestDropdown);
        let suggestActiveIdx = -1;

        function positionSuggest() {
            const r = searchInput.getBoundingClientRect();
            suggestDropdown.style.left = r.left + 'px';
            suggestDropdown.style.top = (r.bottom + 4) + 'px';
            suggestDropdown.style.width = r.width + 'px';
        }
        function hideSuggest() { suggestDropdown.style.display = 'none'; suggestActiveIdx = -1; }

        function renderSuggest(items) {
            suggestDropdown.innerHTML = '';
            suggestActiveIdx = -1;
            if (!items.length) { hideSuggest(); return; }
            items.forEach((s) => {
                const div = document.createElement('div');
                div.className = 'ph-suggest-item';
                if (s.type === 'trx') {
                    div.innerHTML = `<span class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></span><div><p class="font-mono font-black text-[11px] text-blue-600 tracking-widest">${s.label}</p><p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Transaction ID</p></div>`;
                } else {
                    div.innerHTML = `<span class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-lg bg-amber-50 text-amber-500"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span><div><p class="font-black text-[12px] text-slate-900 uppercase tracking-tight">${s.label}</p><p class="text-[9px] text-slate-400 font-bold mt-0.5">${s.sub || ''}</p></div>`;
                }
                div.onmousedown = (e) => { e.preventDefault(); searchInput.value = s.value; hideSuggest(); loadHistory(); };
                suggestDropdown.appendChild(div);
            });
            positionSuggest();
            suggestDropdown.style.display = 'block';
        }

        let suggestTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(suggestTimeout);
            const val = this.value.trim();
            loadHistory();
            if (val.length < 1) { hideSuggest(); return; }
            suggestTimeout = setTimeout(async () => {
                const res = await fetch(`payment_handler.php?action=suggest&q=${encodeURIComponent(val)}`);
                const data = await res.json();
                renderSuggest(data.suggestions);
            }, 180);
        });

        searchInput.addEventListener('keydown', function(e) {
            const items = suggestDropdown.querySelectorAll('.ph-suggest-item');
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

        document.getElementById('date_filter').addEventListener('change', () => { resetOthers('date'); loadHistory(); });
        document.getElementById('month_filter').addEventListener('change', () => { resetOthers('month'); loadHistory(); });
        document.getElementById('year_filter').addEventListener('change', () => { resetOthers('year'); loadHistory(); });
        document.getElementById('method_filter').addEventListener('change', loadHistory);
        document.getElementById('status_filter').addEventListener('change', loadHistory);

        function resetOthers(active) {
            if(active !== 'date') document.getElementById('date_filter').value = '';
            if(active !== 'month') document.getElementById('month_filter').value = '';
            if(active !== 'year') document.getElementById('year_filter').value = '';
        }

        function clearTemporalFilters() {
            document.getElementById('search').value = '';
            document.getElementById('date_filter').value = '';
            document.getElementById('month_filter').value = '';
            document.getElementById('year_filter').value = '';
            document.getElementById('method_filter').value = 'all';
            document.getElementById('status_filter').value = 'all';
            loadHistory();
        }

        async function loadHistory() {
            const tbody = document.getElementById('historyBody');
            const search = document.getElementById('search').value;
            const date = document.getElementById('date_filter').value;
            const month = document.getElementById('month_filter').value;
            const year = document.getElementById('year_filter').value;
            const method = document.getElementById('method_filter').value;
            const status = document.getElementById('status_filter').value;

            try {
                const res = await fetch(`payment_handler.php?action=fetch_payment_history&search=${search}&date=${date}&month=${month}&year=${year}&method=${method}&status=${status}`);
                const data = await res.json();
                
                tbody.innerHTML = '';

                if(data.sales.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="px-8 py-20 text-center text-slate-400 font-black uppercase tracking-[0.2em] italic text-sm">No historical records detected in current audit.</td></tr>`;
                    return;
                }

                data.sales.forEach(sale => {
                    const statusClass = sale.payment_status === 'approved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 
                                      sale.payment_status === 'rejected' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 
                                      'bg-amber-100 text-amber-800 border border-amber-200';
                    
                    const methodClass = sale.payment_method === 'cheque' ? 'text-amber-600' : 'text-emerald-600';

                    tbody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="px-8 py-6">
                                <span class="font-mono text-[10px] font-black text-blue-800 tracking-tighter uppercase px-3 py-1 bg-blue-50 rounded-lg border border-blue-100">TRX-${sale.id}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="font-black text-slate-900 leading-tight text-sm">${sale.cust_name || 'Anonymous Guest'}</p>
                                <p class="text-[9px] text-slate-400 font-black uppercase tracking-wider mt-1">Audit Record Verified</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] ${methodClass}">${sale.payment_method}</span>
                            </td>
                            <td class="px-8 py-6 font-black text-slate-900 tracking-widest text-sm">
                                LKR ${parseFloat(sale.final_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">${sale.cashier_name}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="status-badge ${statusClass}">${sale.payment_status}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="font-bold text-slate-900 text-[11px] tracking-widest">${new Date(sale.created_at).toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' })}</p>
                                <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-1">${new Date(sale.created_at).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit' })}</p>
                            </td>
                        </tr>
                    `;
                });
            } catch (e) {
                console.error(e);
            }
        }
    </script>
</body>
</html>
