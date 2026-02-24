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
    <title>System Logs - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
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
                radial-gradient(circle at 10% 10%, rgba(37, 99, 235, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
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
            font-size: 10px;
        }
        td {
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            color: #0f172a;
        }
        .suggest-dropdown {
            position: absolute;
            left: 0; right: 0;
            top: calc(100% + 4px);
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 28px -6px rgba(0,0,0,0.1);
            z-index: 9999;
            overflow: hidden;
            animation: dropIn 0.13s ease-out;
        }
        @keyframes dropIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        .suggest-item {
            padding: 8px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            transition: background 0.1s;
        }
        .suggest-item:last-child { border-bottom: none; }
        .suggest-item:hover, .suggest-item.active { background: #eff6ff; color: #2563eb; }
    </style>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-8 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-6">
                <a href="dashboard.php" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                   <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">System Logs</h1>
                   <p class="text-[9px] text-slate-500 font-black uppercase tracking-[0.2em] mt-0.5">Audit Activity Trail</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative" id="logSearchWrapper">
                    <input type="text" id="logSearch" autocomplete="off" class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-blue-500 outline-none w-64" placeholder="Search logs...">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <div id="logSuggestDropdown" class="suggest-dropdown" style="display:none"></div>
                </div>
                <button onclick="resetLogFilters()" title="Reset" class="flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black hover:bg-slate-200 transition-all uppercase tracking-widest border border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset
                </button>
                <button onclick="loadLogs(1)" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl text-[10px] font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 uppercase tracking-widest ring-4 ring-blue-600/10">Refresh</button>
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative z-10">
        <div class="glass-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr>
                            <th class="px-8 py-5">Timestamp</th>
                            <th class="px-8 py-5">User</th>
                            <th class="px-8 py-5">Action</th>
                            <th class="px-8 py-5">Details</th>
                        </tr>
                    </thead>
                    <tbody id="logsBody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="p-6 border-t border-slate-100 flex justify-center gap-2">
                <!-- Pagination Buttons -->
            </div>
        </div>
    </main>

    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', () => {
            loadLogs(1);

            window.resetLogFilters = function() {
                document.getElementById('logSearch').value = '';
                document.getElementById('logSuggestDropdown').style.display = 'none';
                loadLogs(1);
            };

            const LOG_ACTIONS = [
                // Sales
                'New Sale',
                'Edit Sale',
                'Purge Sale',
                // Payments
                'Payment Update',
                'Payment Approved',
                'Payment Rejected',
                // Users
                'New Cashier',
                'New Admin',
                'Login',
                'Logout',
            ];
            let debounceTimer;
            let logActiveIdx = -1;
            const logSearch = document.getElementById('logSearch');
            const logDropdown = document.getElementById('logSuggestDropdown');

            logSearch.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => { loadLogs(1); }, 300);
                const q = this.value.trim().toLowerCase();
                logDropdown.innerHTML = '';
                logActiveIdx = -1;
                if (q.length < 1) { logDropdown.style.display = 'none'; return; }
                const matches = LOG_ACTIONS.filter(a => a.toLowerCase().includes(q));
                if (!matches.length) { logDropdown.style.display = 'none'; return; }
                matches.forEach(a => {
                    const el = document.createElement('div');
                    el.className = 'suggest-item';
                    el.textContent = a;
                    el.onclick = () => {
                        logSearch.value = a;
                        logDropdown.style.display = 'none';
                        loadLogs(1);
                    };
                    logDropdown.appendChild(el);
                });
                logDropdown.style.display = 'block';
            });

            logSearch.addEventListener('keydown', function(e) {
                const items = logDropdown.querySelectorAll('.suggest-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    logActiveIdx = Math.min(logActiveIdx + 1, items.length - 1);
                    items.forEach((el, i) => el.classList.toggle('active', i === logActiveIdx));
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    logActiveIdx = Math.max(logActiveIdx - 1, 0);
                    items.forEach((el, i) => el.classList.toggle('active', i === logActiveIdx));
                } else if (e.key === 'Enter' && logActiveIdx >= 0) {
                    e.preventDefault();
                    items[logActiveIdx].click();
                } else if (e.key === 'Escape') {
                    logDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!document.getElementById('logSearchWrapper').contains(e.target)) {
                    logDropdown.style.display = 'none';
                }
            });
        });

        async function loadLogs(page = 1) {
            currentPage = page;
            const search = document.getElementById('logSearch').value;
            const res = await fetch(`logs_handler.php?action=fetch_logs&page=${page}&search=${search}`);
            const data = await res.json();
            
            const tbody = document.getElementById('logsBody');
            tbody.innerHTML = '';
            
            if(data.logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-16 text-slate-400 font-black uppercase tracking-widest text-xs italic">No activity logs found.</td></tr>';
                return;
            }

            data.logs.forEach(log => {
                const role = log.user_role ? log.user_role.toLowerCase() : 'system';
                let roleClass = 'bg-slate-100 text-slate-500 border-slate-200'; // Default System
                
                if (role === 'admin') {
                    roleClass = 'bg-violet-100 text-violet-700 border-violet-200';
                } else if (role === 'cashier') {
                    roleClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                }

                const action = log.action.toLowerCase();
                let actionClass = 'bg-blue-50 text-blue-700 border-blue-100'; // Default
                
                if (action.includes('payment')) {
                    actionClass = 'bg-amber-50 text-amber-700 border-amber-200';
                } else if (action.includes('delete')) {
                    actionClass = 'bg-rose-50 text-rose-700 border-rose-200';
                } else if (action.includes('sale')) {
                    actionClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                } else if (action.includes('update')) {
                    actionClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                } else if (action.includes('add')) {
                    actionClass = 'bg-violet-50 text-violet-700 border-violet-200';
                }

                const row = `
                    <tr class="hover:bg-slate-50 transition-all group border-b border-slate-50 last:border-0 text-sm">
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">${new Date(log.created_at).toLocaleDateString('en-GB')}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">${new Date(log.created_at).toLocaleTimeString()}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-900 text-sm tracking-tight">${log.user_name || 'System'}</p>
                            <span class="px-2 py-0.5 border ${roleClass} rounded text-[8px] font-black uppercase tracking-widest">${role}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1.5 border ${actionClass} rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap shadow-sm">${log.action}</span>
                        </td>
                        <td class="px-8 py-6 max-w-md">
                            <p class="text-[11px] font-medium text-slate-600 leading-relaxed whitespace-pre-wrap">${(log.details || '-').replace(/~~(.*?)~~\s(.*?)(?=, |$)/g, '<span class="line-through opacity-100 text-slate-800">$1</span> <span class="text-blue-400 font-bold mx-1">→</span> <span class="text-rose-600 font-black tracking-tight">$2</span>')}</p>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            renderPagination(data.pagination);
        }

        function renderPagination(pg) {
            const container = document.getElementById('pagination');
            let html = '';
            
            for(let i = 1; i <= pg.total_pages; i++) {
                const activeClass = i === pg.current_page ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200';
                html += `<button onclick="loadLogs(${i})" class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black transition-all ${activeClass}">${i}</button>`;
            }
            container.innerHTML = html;
        }
    </script>
</body>
</html>
