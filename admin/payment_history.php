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
        .blue-tile-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: 3px solid #ffffff;
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.4);
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
            outline: none !important;
        }
        option {
            background-color: #0f172a !important;
            color: white !important;
        }
        th {
            font-weight: 900;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
            padding: 1.5rem 2rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-50">
        <div class="px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-white/10 rounded-xl transition-all text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Approval History</h1>
            </div>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-10 relative z-10">
        
        <!-- Search & Filter Bar -->
        <div class="blue-tile-card p-4 rounded-[2rem] flex flex-wrap lg:flex-nowrap items-center gap-4">
            <div class="relative w-full lg:w-72 flex-shrink-0">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-white/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="search" placeholder="Search TRX..." class="w-full pl-12 pr-4 py-3 rounded-xl text-xs font-bold placeholder:text-white/20">
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
                    <button onclick="clearTemporalFilters()" class="p-3 bg-white/10 border border-white/10 rounded-xl text-white hover:bg-white/20 transition-all" title="Reset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                    <button onclick="loadHistory()" class="px-6 py-3 bg-white text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-50 transition-all shadow-lg active:scale-95">Filter</button>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="blue-tile-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] border-collapse">
                    <thead>
                        <tr class="bg-white/10 border-b border-white/20">
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
        document.getElementById('search').addEventListener('input', loadHistory);
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
            document.getElementById('date_filter').value = '';
            document.getElementById('month_filter').value = '';
            document.getElementById('year_filter').value = '';
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
                    tbody.innerHTML = `<tr><td colspan="7" class="px-8 py-20 text-center text-white/20 font-black uppercase tracking-[0.2em] italic text-sm">No historical records detected in current audit.</td></tr>`;
                    return;
                }

                data.sales.forEach(sale => {
                    const statusClass = sale.payment_status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 
                                      sale.payment_status === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 
                                      'bg-amber-500/20 text-amber-300 border border-amber-500/30';
                    
                    const methodClass = sale.payment_method === 'cheque' ? 'text-amber-200' : 'text-emerald-200';

                    tbody.innerHTML += `
                        <tr class="hover:bg-white/5 transition-all">
                            <td class="px-8 py-6">
                                <span class="font-mono text-[10px] font-black text-white/40 tracking-tighter uppercase px-3 py-1 bg-white/10 rounded-lg border border-white/10">TRX-${sale.id}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="font-black text-white leading-tight text-sm">${sale.cust_name || 'Anonymous Guest'}</p>
                                <p class="text-[9px] text-white/30 font-black uppercase tracking-wider mt-1">Audit Record Verified</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] ${methodClass}">${sale.payment_method}</span>
                            </td>
                            <td class="px-8 py-6 font-black text-white tracking-widest text-sm">
                                LKR ${parseFloat(sale.final_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">${sale.cashier_name}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="status-badge ${statusClass}">${sale.payment_status}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="font-bold text-white text-[11px] tracking-widest">${new Date(sale.created_at).toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' })}</p>
                                <p class="text-[9px] text-white/20 font-black uppercase tracking-widest mt-1">${new Date(sale.created_at).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit' })}</p>
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
