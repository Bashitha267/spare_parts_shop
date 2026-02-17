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
    <title>Sales History - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3 md:gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-slate-500 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-lg md:text-xl font-black text-slate-800 tracking-tight">Sales Inventory</h1>
                    <p class="hidden sm:block text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Manage & Audit History</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-black text-slate-800 leading-none"><?php echo $_SESSION['full_name']; ?></p>
                    <p class="text-[9px] text-slate-400 font-bold">Administrator</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-black">
                    <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-8 animate-fade">
        <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:flex-grow md:max-w-md">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="search" placeholder="Search by ID, Customer..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <input type="date" id="date_filter" class="flex-1 md:flex-none bg-slate-50 border border-slate-200 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                <button onclick="loadHistory()" class="flex-1 md:flex-none bg-blue-600 text-white px-6 py-2.5 rounded-2xl text-xs font-black hover:bg-black transition-all shadow-lg shadow-blue-100 uppercase tracking-widest text-center">Refresh</button>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sale ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Details</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Amount</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="historyBody" class="divide-y divide-slate-50">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- Edit Modal (Simplified) -->
    <div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[100] p-4 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] max-w-lg w-full p-8 space-y-6 shadow-2xl animate-fade">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-black text-slate-800">Edit Sale <span id="editId" class="text-blue-600"></span></h3>
                <button onclick="closeModal()" class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="editForm" class="space-y-4 text-left">
                <input type="hidden" name="sale_id" id="formSaleId">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" id="formAmount" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Payment Method</label>
                        <select name="payment_method" id="formMethod" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status</label>
                        <select name="payment_status" id="formStatus" class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1 text-rose-500">Reason for Change *</label>
                    <textarea name="reason" required placeholder="Describe why this edit is being made..." class="w-full bg-slate-50 border-none px-4 py-3 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100 min-h-[100px]"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadHistory);
        document.getElementById('search').addEventListener('input', loadHistory);
        document.getElementById('date_filter').addEventListener('change', loadHistory);

        async function loadHistory() {
            const search = document.getElementById('search').value;
            const date = document.getElementById('date_filter').value;
            const res = await fetch(`sales_history_handler.php?action=fetch&search=${search}&date=${date}`);
            const data = await res.json();
            const tbody = document.getElementById('historyBody');
            tbody.innerHTML = '';

            data.sales.forEach(sale => {
                const statusClass = sale.payment_status === 'approved' ? 'bg-emerald-500' : 
                                  sale.payment_status === 'pending' ? 'bg-amber-400' : 'bg-rose-500';
                                  
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-6">
                            <span class="font-mono text-xs font-black text-slate-400 tracking-tighter">TRX-${sale.id}</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-800 leading-tight">${sale.cust_name || 'Anonymous Guest'}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">${sale.cust_contact || 'No Contact'}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-slate-600 capitalize">${sale.payment_method} Settlement</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">${new Date(sale.created_at).toLocaleString()}</p>
                        </td>
                        <td class="px-8 py-6 text-right font-black text-slate-900">Rs. ${parseFloat(sale.final_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase text-white ${statusClass} shadow-sm">${sale.payment_status}</span>
                        </td>
                        <td class="px-8 py-6 text-right space-x-1">
                            <button onclick='openEditModal(${JSON.stringify(sale)})' class="p-2.5 bg-slate-100 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button onclick="confirmDelete(${sale.id})" class="p-2.5 bg-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;
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
                    customClass: { popup: 'rounded-[1.5rem]' }
                });
                closeModal();
                loadHistory();
            } else {
                Swal.fire('Error', data.message || 'Update failed', 'error');
            }
        });

        async function confirmDelete(saleId) {
            const { value: reason } = await Swal.fire({
                title: 'Delete Sale TRX-' + saleId + '?',
                text: 'This will reverse inventory and log this destructive action. Please provide a reason:',
                input: 'textarea',
                inputPlaceholder: 'Enter deletion reason here...',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Yes, Delete & Reverse',
                customClass: { popup: 'rounded-[1.5rem]' }
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
                        customClass: { popup: 'rounded-[1.5rem]' }
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
