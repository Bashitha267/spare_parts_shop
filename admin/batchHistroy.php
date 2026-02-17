<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth(['admin', 'cashier']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch History - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard.php' : '../cashier/dashboard.php'; ?>" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-slate-800">Batch History</h1>
            </div>
            <a href="addItems.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-all">New Stock Entry</a>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-4">
        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-wrap items-center gap-4">
            <form method="GET" class="flex flex-wrap items-center gap-4 w-full">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Filter by Date</label>
                    <input type="date" name="date" onchange="this.form.submit()" value="<?php echo $_GET['date'] ?? ''; ?>" class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">User Role</label>
                    <select name="role" onchange="this.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none min-w-[140px]">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo ($_GET['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="cashier" <?php echo ($_GET['role'] ?? '') === 'cashier' ? 'selected' : ''; ?>>Cashier</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 mt-4 md:mt-0">
                    <?php if(isset($_GET['date']) || isset($_GET['role'])): ?>
                    <a href="batchHistroy.php" class="bg-slate-100 text-slate-600 px-6 py-2 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all">Clear Filters</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Date & User</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Invoice #</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Supplier</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Total Value</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    $where = "WHERE 1=1";
                    $params = [];

                    if(!empty($_GET['date'])) {
                        $where .= " AND DATE(i.created_at) = :date";
                        $params['date'] = $_GET['date'];
                    }

                    if(!empty($_GET['role'])) {
                        $where .= " AND u.role = :role";
                        $params['role'] = $_GET['role'];
                    }

                    $sql = "SELECT i.*, u.full_name, u.role 
                            FROM invoices i 
                            JOIN users u ON i.user_id = u.id 
                            $where 
                            ORDER BY i.created_at DESC";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    while($row = $stmt->fetch()):
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-slate-900"><?php echo date('Y-M-d H:i', strtotime($row['created_at'])); ?></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase <?php echo $row['role'] === 'admin' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600'; ?>">
                                    <?php echo $row['role']; ?>
                                </span>
                                <p class="text-xs text-slate-500">By: <?php echo htmlspecialchars($row['full_name']); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-slate-600"><?php echo htmlspecialchars($row['invoice_no']); ?></td>
                        <td class="px-6 py-4 text-sm text-slate-700"><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600 text-right">LKR <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center gap-3">
                                <button onclick="viewBatch(<?php echo $row['id']; ?>)" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View Items">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                <button onclick="editBatch(<?php echo $row['id']; ?>)" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit Invoice">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="deleteBatch(<?php echo $row['id']; ?>)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Batch">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function viewBatch(id) {
            // Future implementation: Modal to show items in this invoice
            Swal.fire('Info', 'View Modal for Invoice #' + id + ' coming soon!', 'info');
        }

        function editBatch(id) {
            Swal.fire({
                title: 'Edit Batch?',
                text: "This will reopen the invoice for editing.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementation: Send to handler to set session and redirect
                    window.location.href = `batch_actions.php?action=reopen&id=${id}`;
                }
            })
        }

        function deleteBatch(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this batch will remove these items from your inventory!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `batch_actions.php?action=delete&id=${id}`;
                }
            })
        }
    </script>
</body>
</html>
