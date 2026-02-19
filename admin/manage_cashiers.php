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
        input, select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        th {
            font-weight: 900;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-white/10 rounded-xl transition-all text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Staff Management</h1>
            </div>
            <button onclick="openCashierModal()" class="bg-blue-500 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:bg-blue-400 transition-all shadow-lg shadow-blue-500/20 uppercase tracking-widest">+ New Staff Access</button>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative z-10">
        
        <!-- Cashier List -->
        <div class="blue-gradient-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-8 py-5">Staff Identity</th>
                            <th class="px-8 py-5">Access Name</th>
                            <th class="px-8 py-5">Role Profile</th>
                            <th class="px-8 py-5">Joined Timeline</th>
                            <th class="px-8 py-5 text-center">Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cashierBody" class="divide-y divide-white/5">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Cashier Modal -->
    <div id="cashierModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="blue-gradient-card w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 border border-white/20">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Grant System Access</h3>
                <button onclick="closeCashierModal()" class="text-white/40 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="cashierForm" class="space-y-6">
                <input type="hidden" name="action" value="add_cashier">
                <div>
                    <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Full Identity</label>
                    <input type="text" name="full_name" required class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 font-bold" placeholder="Full Legal Name">
                </div>
                <div>
                   <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">System Username</label>
                   <input type="text" name="username" required class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 font-bold" placeholder="Login identifier">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Secure Passkey</label>
                    <input type="password" name="password" required class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 font-bold" placeholder="••••••••">
                </div>
                <div class="flex flex-col gap-3 mt-10 text-center">
                    <button type="submit" class="w-full py-5 bg-blue-500 text-white rounded-2xl font-black hover:bg-blue-400 shadow-xl shadow-blue-500/20 transition-all uppercase text-sm tracking-widest">Activate Access</button>
                    <button type="button" onclick="closeCashierModal()" class="w-full py-4 bg-white/5 text-white/50 rounded-2xl font-bold hover:bg-white/10 transition-all uppercase text-xs tracking-widest">Cancel</button>
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
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-10 text-blue-300/40 font-bold italic tracking-wider">No active staff records in the grid.</td></tr>';
                return;
            }

            data.cashiers.forEach(user => {
                const row = `
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="px-8 py-6">
                            <p class="font-black text-white text-sm tracking-tight">${user.full_name}</p>
                            <p class="text-[10px] font-mono text-blue-300/40 mt-1 uppercase tracking-tighter">ID: ${user.emp_id || 'N/A'}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-blue-300 uppercase tracking-widest">${user.username}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-white/5 text-white/40 border border-white/10 rounded-lg text-[10px] font-black uppercase tracking-widest">Cashier</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-bold text-white/30 uppercase tracking-tight">${new Date(user.created_at).toLocaleDateString()} @ ${new Date(user.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <button onclick="deleteCashier(${user.id})" class="px-4 py-2 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 active:scale-95">Revoke Access</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        async function deleteCashier(id) {
            const result = await Swal.fire({
                title: 'Revoke Access?',
                text: "This will terminate the cashier's login capability immediately.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'Yes, Terminate!',
                customClass: { 
                    popup: 'rounded-[1.5rem]',
                    confirmButton: 'rounded-xl font-bold uppercase text-[10px] px-6 py-3',
                    cancelButton: 'rounded-xl font-bold uppercase text-[10px] px-6 py-3'
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
                        title: 'Terminated',
                        text: 'Security clearance revoked.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[1.5rem]' }
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }
        }

        function openCashierModal() {
            document.getElementById('cashierModal').classList.remove('hidden');
        }

        function closeCashierModal() {
            document.getElementById('cashierModal').classList.add('hidden');
            document.getElementById('cashierForm').reset();
        }
    </script>
</body>
</html>
