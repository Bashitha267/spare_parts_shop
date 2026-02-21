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
    <title>Staff Management - Vehicle Square</title>
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
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.05);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 2px solid #000;
        }
        input, select {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            outline: none !important;
        }
        th {
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
        }
        td {
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            color: #0f172a;
        }
    </style>
</head>
<body class="min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-8 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-6">
                <a href="dashboard.php" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                   <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Staff Management</h1>
                   <p class="text-[9px] text-slate-500 font-black uppercase tracking-[0.2em] mt-0.5">Control Security Clearances</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="openCashierModal('admin')" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl text-[10px] font-black hover:bg-slate-800 transition-all shadow-xl shadow-slate-500/20 uppercase tracking-widest ring-4 ring-slate-900/10">+ New Admin Access</button>
                <button onclick="openCashierModal('cashier')" class="bg-blue-600 text-white px-8 py-3.5 rounded-2xl text-[10px] font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 uppercase tracking-widest ring-4 ring-blue-600/10">+ New Cashier Access</button>
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative z-10">
        <!-- Cashier List -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-5">Staff Identity</th>
                            <th class="px-8 py-5">Access Name</th>
                            <th class="px-8 py-5">Role Profile</th>
                            <th class="px-8 py-5">Joined Timeline</th>
                            <th class="px-8 py-5 text-center">Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cashierBody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Cashier Modal -->
    <div id="cashierModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-lg rounded-[2.5rem] p-10 shadow-2xl">
            <div class="flex justify-between items-start mb-10">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Grant Access</h3>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1">Assign Security Credentials</p>
                </div>
                <button onclick="closeCashierModal()" class="p-2 bg-slate-100 text-slate-400 hover:text-slate-900 hover:bg-slate-200 transition-all rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="cashierForm" class="space-y-6">
                <input type="hidden" name="action" value="add_staff">
                <input type="hidden" name="role" id="staffRole" value="cashier">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Full Identity</label>
                    <input type="text" name="full_name" required class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-slate-300 font-bold" placeholder="Full Legal Name">
                </div>
                <div>
                   <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">System Username</label>
                   <input type="text" name="username" required class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-slate-300 font-bold" placeholder="Login identifier">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Secure Passkey</label>
                    <input type="password" name="password" required class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-slate-300 font-bold" placeholder="••••••••">
                </div>
                <div class="flex flex-col gap-3 mt-10">
                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all uppercase text-sm tracking-widest">Activate Access</button>
                    <button type="button" onclick="closeCashierModal()" class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-200 transition-all uppercase text-[10px] tracking-widest">Cancel Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadCashiers();

            document.getElementById('cashierForm').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                
                const res = await fetch('cashier_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if(data.success) {
                    closeCashierModal();
                    loadCashiers();
                    Swal.fire({
                        title: 'Success',
                        text: 'Access granted successfully!',
                        icon: 'success',
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            };
        });

        async function loadCashiers() {
            const res = await fetch('cashier_handler.php?action=fetch_cashiers');
            const data = await res.json();
            
            const tbody = document.getElementById('cashierBody');
            tbody.innerHTML = '';
            
            if(data.cashiers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-16 text-slate-400 font-black uppercase tracking-widest text-xs italic">No active staff records in the grid.</td></tr>';
                return;
            }

            data.cashiers.forEach(user => {
                const row = `
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-900 text-sm tracking-tight">${user.full_name}</p>
                            <p class="text-[10px] font-mono text-slate-400 mt-1 uppercase tracking-tighter font-black">ID: ${user.emp_id || 'N/A'}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-blue-800 uppercase tracking-widest">${user.username}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 ${user.role === 'admin' ? 'bg-slate-900 text-white' : 'bg-blue-50 text-blue-800 border border-blue-100'} rounded-lg text-[10px] font-black uppercase tracking-widest">${user.role}</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">${new Date(user.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'})}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">${new Date(user.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <button onclick="deleteCashier(${user.id})" class="px-5 py-2.5 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-sm active:scale-95">Revoke Access</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        async function deleteCashier(id) {
            const result = await Swal.fire({
                title: 'Revoke Access?',
                text: "Termination will be immediate and logged.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Confirm Revocation',
                customClass: { 
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-2xl font-black uppercase text-[10px] px-8 py-4',
                    cancelButton: 'rounded-2xl font-black uppercase text-[10px] px-8 py-4'
                }
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_cashier');
                formData.append('id', id);

                const res = await fetch('cashier_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if(data.success) {
                    loadCashiers();
                    Swal.fire({
                        title: 'Revoked',
                        text: 'Security clearance terminated.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }
        }

        function openCashierModal(role = 'cashier') {
            document.getElementById('staffRole').value = role;
            const title = role === 'admin' ? 'Grant Admin Access' : 'Grant Cashier Access';
            document.querySelector('#cashierModal h3').innerText = title;
            document.getElementById('cashierModal').classList.remove('hidden');
        }

        function closeCashierModal() {
            document.getElementById('cashierModal').classList.add('hidden');
            document.getElementById('cashierForm').reset();
        }
    </script>
</body>
</html>
