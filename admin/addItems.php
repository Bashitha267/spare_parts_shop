<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth(['admin', 'cashier']);

// Logic for active invoice
$active_invoice = null;
if (isset($_SESSION['active_invoice_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND status = 'draft'");
    $stmt->execute([$_SESSION['active_invoice_id']]);
    $active_invoice = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Entry - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard.php' : '../cashier/dashboard.php'; ?>" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-slate-800">New Stock / Batch Entry</h1>
            </div>
            
            <?php if($active_invoice): ?>
            <div class="flex items-center gap-3 md:gap-6">
                <div class="hidden sm:block text-right">
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Invoice No</p>
                    <p class="text-xs md:text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($active_invoice['invoice_no']); ?></p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Total Value</p>
                    <p class="text-sm md:text-lg font-bold text-blue-600">LKR <span id="header_total"><?php echo number_format($active_invoice['total_amount'], 2); ?></span></p>
                </div>
                <button onclick="saveAndComplete()" class="bg-blue-600 text-white px-4 md:px-6 py-2 rounded-xl text-xs md:text-sm font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
                    Complete
                </button>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-6">
        
        <?php if(!$active_invoice): ?>
        <!-- Invoice Header Form -->
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Start New Invoice</h2>
            <form id="invoiceHeaderForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Invoice Number *</label>
                        <input type="text" name="invoice_no" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. INV-2024-001">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Invoice Date *</label>
                        <input type="date" name="invoice_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Supplier Name</label>
                    <input type="text" name="supplier_name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Supplier or Company Name">
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold hover:bg-black transition-all">
                    Start Entry &rarr;
                </button>
            </form>
        </div>
        <?php else: ?>

        <!-- Search & Add Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-12">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="mainSearch" class="block w-full pl-10 pr-3 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 text-lg" placeholder="Scan Barcode or Type Product Name...">
                        
                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 hidden max-h-80 overflow-y-auto">
                            <!-- Results populate here -->
                        </div>
                    </div>
                    <button onclick="openProductModal()" class="bg-emerald-50 text-emerald-700 px-6 py-3 rounded-xl font-bold border border-emerald-100 hover:bg-emerald-100 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Create New Product
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="lg:col-span-12">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Barcode</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Qty</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Buying Price</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Selling Price</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody id="batchItemsBody" class="divide-y divide-slate-50">
                            <!-- Items populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Modal for New Product / Add Stock -->
    <div id="productModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-800">Add Product Details</h3>
                <button onclick="closeProductModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="productEntryForm" class="p-8 overflow-y-auto space-y-6">
                <input type="hidden" name="action" value="add_to_batch">
                <input type="hidden" name="product_id" id="modal_product_id">

                <div id="itemSelectionArea" class="space-y-6">
                    <!-- Dynamic fields based on Oil/Spare Part selection -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- New Product Type Selection -->
                        <div id="typeSelectionArea" class="col-span-2 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Item Type</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="p_type" value="oil" class="peer hidden" checked>
                                        <div class="flex items-center justify-center p-4 border-2 border-slate-100 rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                            <span class="font-bold text-slate-600 peer-checked:text-blue-700">Oil</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="p_type" value="spare_part" class="peer hidden">
                                        <div class="flex items-center justify-center p-4 border-2 border-slate-100 rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                            <span class="font-bold text-slate-600 peer-checked:text-blue-700">Spare Part</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="oilSubTypeArea" class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="p_oil_type" value="can" class="peer hidden" checked>
                                    <div class="flex items-center justify-center p-3 border-2 border-slate-100 rounded-2xl peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all">
                                        <span class="font-bold text-slate-600 peer-checked:text-amber-700 text-sm">Cans</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="p_oil_type" value="loose" class="peer hidden">
                                    <div class="flex items-center justify-center p-3 border-2 border-slate-100 rounded-2xl peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all">
                                        <span class="font-bold text-slate-600 peer-checked:text-amber-700 text-sm">Loose (Liters)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Existing Product Type Display -->
                        <div id="typeDisplayArea" class="col-span-2 hidden">
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Product Category</span>
                                <div id="readOnlyType" class="flex items-center gap-2">
                                    <!-- Populated via JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Product Base Info (Hidden for Existing) -->
                        <div id="productInfoArea" class="col-span-2 space-y-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Product Name & Barcode</label>
                                <div class="flex gap-2">
                                    <input type="text" name="p_name" id="modal_p_name" required class="flex-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl" placeholder="Full Product Name">
                                    <input type="text" name="p_barcode" id="modal_p_barcode" class="w-1/3 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl" placeholder="Barcode">
                                    <button type="button" onclick="generateBarcode()" class="px-4 bg-slate-200 rounded-xl text-slate-600 hover:bg-slate-300 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Brand Name (Optional)</label>
                                <input type="text" name="brand" id="modal_p_brand" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl" placeholder="e.g. Castrol, Mobil, Toyota">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Buying Price (LKR)</label>
                            <input type="number" step="0.01" name="b_price" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Label/Selling Price (LKR)</label>
                            <input type="number" step="0.01" name="s_price" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl">
                        </div>

                        <div>
                            <label id="qtyLabel" class="block text-sm font-semibold text-slate-700 mb-2">Qty (Cans)</label>
                            <input type="number" step="0.01" name="qty" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Expire Date (Optional)</label>
                            <input type="date" name="exp_date" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl">
                        </div>

                        <div id="extraInfoArea" class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Vehicle Types (Optional)</label>
                            <input type="text" name="v_types" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl" placeholder="e.g. Scooter, Bike, Car, SUV">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 grid grid-cols-2 gap-4">
                    <button type="button" onclick="closeProductModal()" class="py-4 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Cancel</button>
                    <button type="submit" class="py-4 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">Add to List</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('mainSearch');
            const resultsDiv = document.getElementById('searchResults');

            if(searchInput) {
                // Prevent browser autocomplete interference
                searchInput.setAttribute('autocomplete', 'off');
                searchInput.setAttribute('name', 'search_' + Math.random().toString(36).substring(7));

                searchInput.addEventListener('input', async function() {
                    const val = this.value.trim();
                    if (val.length < 2) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }
                    
                    try {
                        const res = await fetch(`addItems_handler.php?action=search&query=${val}`);
                        const data = await res.json();
                        
                        if (data.products && data.products.length > 0) {
                            resultsDiv.innerHTML = '';
                            data.products.forEach(p => {
                                const row = document.createElement('div');
                                row.className = 'px-6 py-4 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 flex justify-between items-center group';
                                row.innerHTML = `
                                    <div class="flex-1">
                                        <p class="font-bold text-slate-800">${p.name}</p>
                                        <p class="text-xs text-slate-400">${p.barcode} | ${p.brand || 'No Brand'}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button onclick="event.stopPropagation(); showCompatibility(${JSON.stringify(p).replace(/"/g, '&quot;')})" class="p-2 text-slate-400 hover:text-emerald-00 hover:bg-emerald-50 rounded-lg transition-all opacity-0 group-hover:opacity-100" title="View Compatibility">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <span class="text-[9px] font-black px-2.5 py-1 bg-slate-100 rounded-md uppercase text-slate-500 flex items-center gap-1.5 min-w-fit">
                                            ${p.type === 'oil' ? `<span class="text-blue-600">Oil</span><span class="text-slate-300">•</span><span class="text-amber-600">${p.oil_type}</span>` : p.type.replace('_', ' ')}
                                        </span>
                                    </div>
                                `;
                                row.onclick = (e) => {
                                    e.stopPropagation(); // Prevent immediate closing
                                    selectProduct(p);
                                };
                                resultsDiv.appendChild(row);
                            });
                            resultsDiv.classList.remove('hidden');
                        } else {
                            resultsDiv.innerHTML = `<div class="px-6 py-4 text-slate-400 italic">No products found. Press Enter to create new.</div>`;
                            resultsDiv.classList.remove('hidden');
                        }
                    } catch (e) {
                        console.error("Search error:", e);
                    }
                });

                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const firstResult = resultsDiv.querySelector('.cursor-pointer');
                        if (firstResult) {
                            firstResult.click();
                        } else {
                            openProductModal({ barcode: this.value });
                        }
                    }
                });
            }

            // Close results on click outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });

            // Type switching logic
            const typeRadios = document.querySelectorAll('input[name="p_type"]');
            typeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const oilArea = document.getElementById('oilSubTypeArea');
                    const qtyLabel = document.getElementById('qtyLabel');
                    if (this.value === 'oil') {
                        oilArea.classList.remove('hidden');
                        qtyLabel.innerText = 'Qty (Cans)';
                    } else {
                        oilArea.classList.add('hidden');
                        qtyLabel.innerText = 'Qty';
                    }
                });
            });

            // Handle Header form
            const headerForm = document.getElementById('invoiceHeaderForm');
            if (headerForm) {
                headerForm.onsubmit = async (e) => {
                    e.preventDefault();
                    const formData = new FormData(headerForm);
                    formData.append('action', 'start_invoice');
                    
                    const res = await fetch('addItems_handler.php', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                };
            }

            // Load items if active
            <?php if ($active_invoice): ?>
            loadBatchItems();
            <?php endif; ?>
        });

        function showCompatibility(p) {
            const types = p.vehicle_compatibility || 'Universal Application';
            Swal.fire({
                title: p.name,
                html: `
                    <div class="text-left mt-4 space-y-4">
                        <div class="p-5 bg-slate-50 border border-slate-100 rounded-3xl">
                            <div class="flex justify-between items-center mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recommended Vehicles</p>
                                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black uppercase text-slate-500 shadow-sm">${p.type}</span>
                            </div>
                            <p class="text-base font-black text-slate-800 leading-tight mb-4">${types}</p>
                            <div class="flex items-center gap-2 py-2 px-3 bg-blue-50/50 rounded-xl w-fit">
                                <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Brand</span>
                                <span class="text-xs font-black text-blue-700">${p.brand || 'General'}</span>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    container: 'backdrop-blur-sm'
                }
            });
        }

        function selectProduct(p) {
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('mainSearch').value = '';
            openProductModal(p);
        }

        function openProductModal(product = null) {
            document.getElementById('modal_p_barcode').value = product?.barcode || '';
            document.getElementById('modal_p_name').value = product?.name || '';
            document.getElementById('modal_p_brand').value = product?.brand || '';
            document.getElementById('modal_product_id').value = product?.id || '';
            
            const selectionArea = document.getElementById('typeSelectionArea');
            const displayArea = document.getElementById('typeDisplayArea');
            const productInfoArea = document.getElementById('productInfoArea');
            const extraInfoArea = document.getElementById('extraInfoArea');
            const readOnlyType = document.getElementById('readOnlyType');

            if (product?.id) {
                document.getElementById('modalTitle').innerText = 'Add Stock: ' + product.name;
                
                // Hide redundant areas for existing products
                selectionArea.classList.add('hidden');
                displayArea.classList.add('hidden'); // Simplified: removing category display as requested
                productInfoArea.classList.add('hidden');
                extraInfoArea.classList.add('hidden');

                // Sync the hidden form values
                document.querySelector(`input[name="p_type"][value="${product.type}"]`).checked = true;
                if(product.type === 'oil') {
                    document.querySelector(`input[name="p_oil_type"][value="${product.oil_type}"]`).checked = true;
                    document.getElementById('qtyLabel').innerText = product.oil_type === 'can' ? 'Qty (Cans)' : 'Qty (Liters)';
                } else {
                    document.getElementById('qtyLabel').innerText = 'Qty (Units)';
                }
            } else {
                document.getElementById('modalTitle').innerText = 'Create New Product Entry';
                selectionArea.classList.remove('hidden');
                displayArea.classList.add('hidden');
                productInfoArea.classList.remove('hidden');
                extraInfoArea.classList.remove('hidden');
                
                // Default to oil/can
                document.querySelector('input[name="p_type"][value="oil"]').checked = true;
                document.getElementById('oilSubTypeArea').classList.remove('hidden');
                document.getElementById('qtyLabel').innerText = 'Qty (Cans)';
            }
            
            document.getElementById('productModal').classList.remove('hidden');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
            document.getElementById('productEntryForm').reset();
        }

        function generateBarcode() {
            // Generate a 12 digit random number (EAN-13 compatible length without checksum)
            const random = Math.floor(100000000000 + Math.random() * 900000000000);
            document.getElementById('modal_p_barcode').value = random;
        }

        const entryForm = document.getElementById('productEntryForm');
        entryForm.onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(entryForm);
            const res = await fetch('addItems_handler.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                closeProductModal();
                loadBatchItems();
                document.getElementById('header_total').innerText = data.new_total;
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        };

        async function loadBatchItems() {
            const res = await fetch('addItems_handler.php?action=load_items');
            const data = await res.json();
            const body = document.getElementById('batchItemsBody');
            body.innerHTML = '';
            data.items.forEach(item => {
                body.innerHTML += `
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">${item.barcode}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">${item.name}</p>
                            <p class="text-[10px] text-slate-400 uppercase">${item.type} ${item.oil_type !== 'none' ? '('+item.oil_type+')' : ''}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-right">${item.original_qty}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 text-right">${item.buying_price}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 text-right">${item.selling_price}</td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-900 text-right">LKR ${(item.original_qty * item.buying_price).toLocaleString()}</td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="removeItem(${item.id})" class="text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        async function removeItem(batchId) {
            const res = await fetch(`addItems_handler.php?action=remove_item&id=${batchId}`);
            const data = await res.json();
            if (data.success) {
                loadBatchItems();
                document.getElementById('header_total').innerText = data.new_total;
            }
        }

        async function saveAndComplete() {
            const res = await fetch('addItems_handler.php?action=complete_invoice');
            const data = await res.json();
            if (data.success) {
                Swal.fire('Success', 'Inventory updated successfully!', 'success').then(() => {
                    location.href = 'dashboard.php';
                });
            }
        }
    </script>
</body>
</html>
