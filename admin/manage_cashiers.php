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
    <title>Manage Cashiers - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-slate-800">Staff Management</h1>
            </div>
            <button onclick="openCashierModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-all text-sm">+ Add New Cashier</button>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Cashier List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">EMP ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Full Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Username</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Joined Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="cashierBody" class="divide-y divide-slate-50">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- Cashier Modal -->
    <div id="cashierModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Register New Cashier</h3>
            <form id="cashierForm" class="space-y-4">
                <input type="hidden" name="action" value="add_cashier">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Full Name</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-2 bg-slate-50 border rounded-lg">
                </div>
                <div>
                   <label class="block text-sm font-medium text-slate-700">Username</label>
                   <input type="text" name="username" required class="w-full px-4 py-2 bg-slate-50 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 bg-slate-50 border rounded-lg">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeCashierModal()" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Add Cashier</button>
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
                    Swal.fire('Success', 'New cashier added successfully!', 'success');
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
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-slate-400">No cashiers found.</td></tr>';
                return;
            }

            data.cashiers.forEach(user => {
                const row = `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-mono text-slate-600">${user.emp_id || 'N/A'}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">${user.full_name}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">${user.username}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">${new Date(user.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="deleteCashier(${user.id})" class="text-sm text-red-500 hover:text-red-700 font-semibold hover:underline">Remove Access</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        async function deleteCashier(id) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "This will remove the cashier's access immediately.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!'
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_cashier');
                formData.append('id', id);

                const res = await fetch('cashier_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if(data.success) {
                    loadCashiers();
                    Swal.fire('Deleted!', 'Cashier has been removed.', 'success');
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
