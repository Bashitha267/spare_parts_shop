<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('cashier');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">

    <!-- Main Content -->
    <main class="flex flex-col">
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-col md:flex-row justify-between items-center shadow-sm z-10 sticky top-0 gap-4">
            <div class="flex items-center justify-between w-full md:w-auto gap-4">
                <div class="flex items-center gap-4">
                    <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-lg transition-all text-slate-400 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <h2 class="text-lg md:text-xl font-black text-slate-800 uppercase tracking-tight">Sales History</h2>
                </div>
                <div class="md:hidden text-right">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Cashier</p>
                    <p class="text-xs font-bold text-slate-700"><?php echo $_SESSION['full_name']; ?></p>
                </div>
            </div>
            <div class="flex items-center justify-between w-full md:w-auto gap-4 md:gap-6">
                <div class="bg-blue-50 px-4 md:px-6 py-1.5 md:py-2 rounded-xl md:rounded-2xl border border-blue-100 flex items-center gap-3 md:gap-4 flex-1 md:flex-none justify-center md:justify-start">
                    <p class="text-[8px] md:text-xs font-bold text-blue-400 uppercase">Today's Total</p>
                    <p id="today_total" class="text-base md:text-xl font-black text-blue-700">LKR 0.00</p>
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Cashier</p>
                    <p class="text-sm font-bold text-slate-700"><?php echo $_SESSION['full_name']; ?></p>
                </div>
            </div>
        </header>

        <div class="flex-1 p-4 md:p-8">
            <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px] md:min-w-0">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sale ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date & Time</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Method</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Net Amount</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        $user_id = $_SESSION['id'];
                        $stmt = $pdo->prepare("SELECT s.*, c.name as cust_name, c.contact as cust_phone 
                                              FROM sales s 
                                              LEFT JOIN customers c ON s.customer_id = c.id 
                                              WHERE s.user_id = ? 
                                              ORDER BY s.created_at DESC");
                        $stmt->execute([$user_id]);
                        while($row = $stmt->fetch()):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 px-3 py-1 rounded-lg text-xs font-black text-slate-600">ID-<?php echo $row['id']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-700"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></p>
                                <p class="text-[10px] text-slate-400 font-bold"><?php echo date('h:i A', strtotime($row['created_at'])); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($row['cust_name'] ?: 'Guest'); ?></p>
                                <p class="text-[10px] text-blue-500 font-medium"><?php echo htmlspecialchars($row['cust_phone'] ?: ''); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black px-2 py-1 bg-blue-50 text-blue-600 rounded-md uppercase"><?php echo $row['payment_method']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-lg font-black text-slate-900">LKR <?php echo number_format($row['final_amount'], 2); ?></p>
                                <?php if($row['discount'] > 0): ?>
                                    <p class="text-[10px] text-red-500 font-bold italic">Disc: -LKR <?php echo number_format($row['discount'], 2); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="viewDetails(<?php echo $row['id']; ?>)" class="p-2 text-slate-300 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Sale Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden anim-pop">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800">Sale Details <span id="modal_sale_id" class="text-slate-400"></span></h3>
                <button onclick="closeDetailModal()" class="text-slate-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="pb-4">Product</th>
                            <th class="pb-4 text-center">Qty</th>
                            <th class="pb-4 text-right">Unit Price</th>
                            <th class="pb-4 text-right">Disc</th>
                            <th class="pb-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody id="detailItems" class="divide-y divide-slate-50"></tbody>
                </table>
                <div id="modalSummary" class="mt-8 pt-6 border-t border-slate-100 space-y-2 text-right"></div>
            </div>
        </div>
    </div>

    <script>
        async function updateTodayTotal() {
            const res = await fetch(`sales_handler.php?action=get_today_total`);
            const data = await res.json();
            document.getElementById('today_total').innerText = 'LKR ' + data.total;
        }
        updateTodayTotal();

        async function viewDetails(id) {
            document.getElementById('modal_sale_id').innerText = '#' + id;
            const res = await fetch(`sales_handler.php?action=fetch_sale_details&id=${id}`);
            const data = await res.json();
            
            const tbody = document.getElementById('detailItems');
            const summary = document.getElementById('modalSummary');
            tbody.innerHTML = '';
            
            data.items.forEach(item => {
                tbody.innerHTML += `
                    <tr class="text-sm">
                        <td class="py-4">
                            <p class="font-bold text-slate-800">${item.name}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">${item.brand}</p>
                        </td>
                        <td class="py-4 text-center font-bold text-slate-600">${item.qty}</td>
                        <td class="py-4 text-right text-slate-500">${numberFormat(item.unit_price)}</td>
                        <td class="py-4 text-right text-red-400">-${numberFormat(item.discount)}</td>
                        <td class="py-4 text-right font-black text-slate-900">LKR ${numberFormat(item.total_price)}</td>
                    </tr>
                `;
            });

            summary.innerHTML = `
                <p class="text-sm font-bold text-slate-400 uppercase">Grand Total: <span class="text-xl font-black text-slate-900 ml-4">LKR ${numberFormat(data.sale.final_amount)}</span></p>
                <p class="text-xs font-bold text-blue-500 uppercase">Paid via ${data.sale.payment_method.toUpperCase()}</p>
            `;

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }
        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
