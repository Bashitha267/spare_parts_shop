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
            <button onclick="loadLogs()" class="bg-blue-600 text-white px-8 py-3.5 rounded-2xl text-[10px] font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 uppercase tracking-widest ring-4 ring-blue-600/10">Refresh Logs</button>
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
        });

        async function loadLogs(page = 1) {
            currentPage = page;
            const res = await fetch(`logs_handler.php?action=fetch_logs&page=${page}`);
            const data = await res.json();
            
            const tbody = document.getElementById('logsBody');
            tbody.innerHTML = '';
            
            if(data.logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-16 text-slate-400 font-black uppercase tracking-widest text-xs italic">No activity logs found.</td></tr>';
                return;
            }

            data.logs.forEach(log => {
                const row = `
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">${new Date(log.created_at).toLocaleDateString('en-GB')}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">${new Date(log.created_at).toLocaleTimeString()}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-900 text-sm tracking-tight">${log.user_name || 'System'}</p>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[8px] font-black uppercase tracking-widest">${log.user_role || 'N/A'}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-blue-50 text-blue-800 border border-blue-100 rounded-lg text-[10px] font-black uppercase tracking-widest">${log.action}</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-medium text-slate-600 leading-relaxed">${log.details || '-'}</p>
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
