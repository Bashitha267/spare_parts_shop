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
        input, select, textarea {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            outline: none !important;
        }
        th {
            font-weight: 900;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
        }
        option {
            background-color: #0f172a !important;
            color: white !important;
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
                  <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Sales History</h1>
                  <p class="hidden sm:block text-[9px] text-blue-300/40 font-black uppercase tracking-[0.2em]">Manage & Audit Registry</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-[9px] font-black text-blue-300 uppercase tracking-widest">Live Audit</span>
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-10 relative z-10">
        
        <!-- Search & Filter Bar -->
        <div class="blue-gradient-card p-6 rounded-[2.5rem] flex flex-col md:flex-row gap-6 items-center">
            <div class="relative w-full md:flex-grow">
                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-blue-300/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="search" placeholder="Locate by ID, Customer Identity..." class="w-full pl-16 pr-6 py-4 rounded-2xl text-sm font-bold placeholder:text-blue-300/20">
            </div>
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <input type="date" id="date_filter" class="flex-1 md:flex-none px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest outline-none">
                
                <select id="method_filter" class="flex-1 md:flex-none px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest outline-none cursor-pointer">
                    <option value="all">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="cheque">Cheque</option>
                    <option value="credit">Credit</option>
                </select>

                <select id="status_filter" class="flex-1 md:flex-none px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest outline-none cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>

                <button onclick="loadHistory()" class="px-8 py-4 bg-blue-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-blue-400 transition-all shadow-xl shadow-blue-500/20 active:scale-95">Synchronize</button>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="blue-gradient-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10">
                        <th class="px-8 py-6">Transaction ID</th>
                        <th class="px-8 py-6">Customer Profile</th>
                        <th class="px-8 py-6">Issued By</th>
                        <th class="px-8 py-6">Date & Time</th>
                        <th class="px-8 py-6">Payment Method</th>
                        <th class="px-8 py-6 text-right">Settlement (LKR)</th>
                        <th class="px-8 py-6 text-center">Status</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="historyBody" class="divide-y divide-white/5">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/80 backdrop-blur-md hidden items-center justify-center z-[100] p-4">
        <div class="blue-gradient-card rounded-[2.5rem] max-w-lg w-full p-10 space-y-8 shadow-2xl border border-white/20">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Edit Sale <span id="editId" class="text-blue-400"></span></h3>
                <button onclick="closeModal()" class="text-white/40 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="editForm" class="space-y-6">
                <input type="hidden" name="sale_id" id="formSaleId">
                <div>
                    <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Total Adjustment</label>
                    <input type="number" step="0.01" name="total_amount" id="formAmount" class="w-full px-6 py-4 rounded-2xl text-sm font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Payment Protocol</label>
                        <select name="payment_method" id="formMethod" class="w-full px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                            <option value="cash">Cash Flow</option>
                            <option value="card">Terminal Card</option>
                            <option value="cheque">Cheque Pool</option>
                            <option value="credit">Credit Facility</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Verification Status</label>
                        <select name="payment_status" id="formStatus" class="w-full px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-rose-400 uppercase tracking-widest mb-3 ml-1">Change Authorization Reason *</label>
                    <textarea name="reason" required placeholder="Describe why this administrative edit is being executed..." class="w-full px-6 py-4 rounded-2xl text-sm font-bold min-h-[120px] resize-none"></textarea>
                </div>
                
                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" class="w-full py-5 bg-blue-500 text-white rounded-2xl font-black hover:bg-blue-400 shadow-xl shadow-blue-500/20 transition-all uppercase text-sm tracking-widest">Commit Adjustments</button>
                    <button type="button" onclick="closeModal()" class="w-full py-4 bg-white/5 text-white/50 rounded-2xl font-bold hover:bg-white/10 transition-all uppercase text-xs tracking-widest">Discard</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadHistory);
        document.getElementById('search').addEventListener('input', loadHistory);
        document.getElementById('date_filter').addEventListener('change', loadHistory);
        document.getElementById('method_filter').addEventListener('change', loadHistory);
        document.getElementById('status_filter').addEventListener('change', loadHistory);

        async function loadHistory() {
            const search = document.getElementById('search').value;
            const date = document.getElementById('date_filter').value;
            const method = document.getElementById('method_filter').value;
            const status = document.getElementById('status_filter').value;
            const res = await fetch(`sales_history_handler.php?action=fetch&search=${search}&date=${date}&method=${method}&status=${status}`);
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
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="px-8 py-8">
                            <span class="font-mono text-[10px] font-black text-blue-300/40 tracking-tighter uppercase px-3 py-1 bg-white/5 rounded-lg border border-white/5">TRX-${sale.id}</span>
                        </td>
                        <td class="px-8 py-8">
                            <p class="font-black text-white text-sm tracking-tight">${sale.cust_name || 'Anonymous Guest'}</p>
                            <p class="text-[9px] text-blue-300/40 font-black uppercase tracking-wider mt-1.5">${sale.cust_contact || 'Private Line'}</p>
                        </td>
                        <td class="px-8 py-8">
                            <div class="flex items-center gap-3">
                               
                                <div>
                                    <p class="font-black text-white text-[11px] tracking-tight uppercase">${sale.officer_name || 'System'}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-8">
                            <p class="font-bold text-white text-[11px] tracking-widest">${new Date(sale.created_at).toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                        </td>
                        <td class="px-8 py-8 text-left">
                            <span class="px-3 py-1 border rounded-lg text-[9px] font-black uppercase tracking-widest ${methodClass}">${sale.payment_method}</span>
                        </td>
                        <td class="px-8 py-8 text-right font-black text-white tracking-widest text-sm">${parseFloat(sale.final_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="px-8 py-8 text-center text-nowrap">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest text-white ${statusClass} shadow-lg">${sale.payment_status}</span>
                        </td>
                        <td class="px-8 py-8 text-right">
                            <div class="flex justify-end items-center gap-3">
                                <button onclick="viewSaleItems(${sale.id})" class="p-3 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-xl hover:bg-emerald-500 hover:text-white transition-all shadow-lg active:scale-95" title="View Manifest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button onclick='openEditModal(${JSON.stringify(sale)})' class="p-3 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-lg active:scale-95" title="Modify Record">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button onclick="confirmDelete(${sale.id})" class="p-3 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-lg active:scale-95" title="Purge & Reverse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        async function viewSaleItems(id) {
            const res = await fetch(`sales_history_handler.php?action=fetch_items&id=${id}`);
            const data = await res.json();
            
            if(!data.success) return;

            let html = `
                <div class="overflow-x-auto mt-4 px-2 text-left">
                    <table class="w-full text-[11px] border-collapse">
                        <thead>
                            <tr class="text-blue-300/50 uppercase tracking-widest border-b border-white/10 italic">
                                <th class="py-2">Item</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Price</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
            `;

            data.items.forEach(item => {
                html += `
                    <tr>
                        <td class="py-3">
                            <p class="font-black text-white">${item.name}</p>
                            <p class="text-[9px] opacity-40">${item.barcode}</p>
                        </td>
                        <td class="py-3 text-center font-bold">${item.qty}</td>
                        <td class="py-3 text-right font-mono text-blue-300">${parseFloat(item.unit_price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="py-3 text-right font-mono text-emerald-400">${parseFloat(item.total_price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
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
                    popup: 'rounded-[2rem] bg-slate-900 text-white border border-white/10 px-4',
                    confirmButton: 'rounded-xl font-black uppercase text-[10px] px-8 py-3 tracking-widest w-full'
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
