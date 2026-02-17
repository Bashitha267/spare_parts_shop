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
    <title>Manage Oil Inventory - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-4 md:px-6 py-3 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3 md:gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-lg md:text-xl font-black text-slate-800 tracking-tight">Oil Inventory</h1>
            </div>
            <a href="addItems.php" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-all">+ Stock</a>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Search & Filter -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full md:w-1/2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInventory" class="block w-full pl-10 pr-3 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Search Inventory...">
            </div>

            <button id="lowStockToggle" class="flex items-center gap-2 px-4 py-3 bg-slate-50 text-slate-600 border border-slate-100 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-all font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Low Stock Only
            </button>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Product Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Current Stock</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryBody" class="divide-y divide-slate-50">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
            
            <!-- Pagination Controls -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center" id="paginationControls">
                <!-- Loaded via JS -->
            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Edit Product</h3>
            <form id="editForm" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Product Name</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-4 py-2 bg-slate-50 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Brand</label>
                    <input type="text" name="brand" id="edit_brand" class="w-full px-4 py-2 bg-slate-50 border rounded-lg">
                </div>
                <div>
                   <label class="block text-sm font-medium text-slate-700">Vehicle Compatibility</label>
                   <input type="text" name="v_types" id="edit_v_types" class="w-full px-4 py-2 bg-slate-50 border rounded-lg">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let debounceTimer;
        let lowStockOnly = false;

        document.addEventListener('DOMContentLoaded', () => {
            loadInventory(1);

            document.getElementById('searchInventory').addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentPage = 1;
                    loadInventory(1, this.value, lowStockOnly);
                }, 300);
            });

            document.getElementById('lowStockToggle').onclick = function() {
                lowStockOnly = !lowStockOnly;
                this.classList.toggle('bg-rose-600', lowStockOnly);
                this.classList.toggle('text-white', lowStockOnly);
                this.classList.toggle('bg-slate-50', !lowStockOnly);
                this.classList.toggle('text-slate-600', !lowStockOnly);
                loadInventory(1, document.getElementById('searchInventory').value, lowStockOnly);
            };

            document.getElementById('editForm').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_product');
                
                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.success) {
                    closeEditModal();
                    loadInventory(currentPage, document.getElementById('searchInventory').value, lowStockOnly);
                    Swal.fire('Success', 'Product updated!', 'success');
                }
            };
        });

        async function loadInventory(page, search = '', low_stock = false) {
            currentPage = page;
            const res = await fetch(`manage_handler.php?action=fetch_inventory&page=${page}&search=${search}&type=oil&low_stock=${low_stock ? 'active' : ''}`);
            const data = await res.json();
            
            const tbody = document.getElementById('inventoryBody');
            tbody.innerHTML = '';
            
            if(data.products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-slate-400">No products found.</td></tr>';
                return;
            }

            data.products.forEach(p => {
                let qtyDisplay = '';
                if(p.type === 'oil') {
                    if(p.oil_type === 'can') qtyDisplay = `<span class="font-bold text-blue-600">${p.total_stock}</span> Cans`;
                    else qtyDisplay = `<span class="font-bold text-amber-600">${p.total_stock}</span> Liters`;
                } else {
                    qtyDisplay = `<span class="font-bold text-slate-700">${p.total_stock}</span> Units`;
                }

                let statusBadge = p.is_active == 1 
                    ? '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md text-xs font-bold uppercase">Active</span>'
                    : '<span class="px-2 py-1 bg-slate-100 text-slate-500 rounded-md text-xs font-bold uppercase">Deactivated</span>';

                let statusBtn = p.is_active == 1
                    ? `<button onclick="toggleStatus(${p.id}, 0)" class="text-xs text-red-500 hover:underline" title="Mark Out of Stock/Inactive">Deactivate</button>`
                    : `<button onclick="toggleStatus(${p.id}, 1)" class="text-xs text-emerald-600 hover:underline" title="Mark Active">Activate</button>`;

                const row = `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800">${p.name}</p>
                            <p class="text-xs font-mono text-slate-400">${p.barcode}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2 py-1 bg-slate-100 rounded text-slate-600 uppercase">${p.type}</span>
                            <span class="text-xs text-slate-400 ml-2">${p.brand || ''}</span>
                        </td>
                        <td class="px-6 py-4">
                            ${p.type === 'oil' ? `<span class="px-2 py-1 bg-amber-50 text-amber-700 rounded text-[10px] font-black uppercase border border-amber-100">${p.oil_type}</span>` : '<span class="text-slate-300">-</span>'}
                        </td>
                        <td class="px-6 py-4 text-right">
                            ${qtyDisplay}
                        </td>
                        <td class="px-6 py-4 text-center">
                            ${statusBadge}
                        </td>
                        <td class="px-6 py-4 text-center flex justify-center items-center gap-2">
                            <button onclick='showCompatibility(${JSON.stringify(p)})' class="p-2 text-slate-400 hover:text-emerald-600 bg-white hover:bg-emerald-50 border border-slate-200 rounded-lg transition-all" title="View Compatibility">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button onclick='printBarcode("${p.barcode}", "${p.name.replace(/'/g, "\\'")}", "${(p.brand || '').replace(/'/g, "\\'")}")' class="p-2 text-slate-400 hover:text-amber-600 bg-white hover:bg-amber-50 border border-slate-200 rounded-lg transition-all" title="Print Barcode Label">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"></path></svg>
                            </button>
                            <button onclick='editProduct(${JSON.stringify(p)})' class="p-2 text-slate-400 hover:text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 rounded-lg transition-all" title="Edit Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            ${statusBtn}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            renderPagination(data.pagination);
            currentPage = page;
        }

        function renderPagination(pg) {
            const container = document.getElementById('paginationControls');
            let html = '<div class="flex items-center gap-2">';
            
            // Previous Button (Icon only)
            if(pg.current_page > 1) {
                html += `<button onclick="loadInventory(${pg.current_page - 1}, document.getElementById('searchInventory').value, lowStockOnly)" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-400 hover:text-blue-600 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>`;
            }

            // Page Numbers
            for(let i = 1; i <= pg.total_pages; i++) {
                const activeClass = i === pg.current_page ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50';
                html += `<button onclick="loadInventory(${i}, document.getElementById('searchInventory').value, lowStockOnly)" class="w-9 h-9 flex items-center justify-center border rounded-lg text-sm font-bold transition-all ${activeClass}">${i}</button>`;
            }

            // Next Button (Icon only)
            if(pg.current_page < pg.total_pages) {
                html += `<button onclick="loadInventory(${pg.current_page + 1}, document.getElementById('searchInventory').value, lowStockOnly)" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-400 hover:text-blue-600 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`;
            }
            
            html += '</div>';
            container.innerHTML = html;
        }

        async function toggleStatus(id, newStatus) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('id', id);
            formData.append('status', newStatus);

            const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            if(data.success) {
                loadInventory(currentPage, document.getElementById('searchInventory').value);
                const msg = newStatus === 1 ? 'Product Activated' : 'Product Deactivated';
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, showConfirmButton: false, timer: 1500 });
            }
        }

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

        function editProduct(p) {
            document.getElementById('edit_id').value = p.id;
            document.getElementById('edit_name').value = p.name;
            document.getElementById('edit_brand').value = p.brand;
            document.getElementById('edit_v_types').value = p.vehicle_compatibility;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function printBarcode(barcode, name, brand) {
            const printWindow = window.open('', '_blank', 'width=600,height=400');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Barcode - ${barcode}</title>
                        <style>
                            @page { size: auto; margin: 0; }
                            body { 
                                font-family: 'Inter', sans-serif; 
                                display: flex; 
                                flex-direction: column; 
                                align-items: center; 
                                justify-content: center; 
                                height: 100vh; 
                                margin: 0;
                                text-align: center;
                            }
                            .label-container {
                                border: 1px dashed #ccc;
                                padding: 20px;
                                border-radius: 8px;
                                display: inline-block;
                            }
                            .product-name { font-weight: bold; font-size: 14px; margin-bottom: 2px; }
                            .brand-name { font-size: 10px; color: #666; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
                            #barcode { max-width: 100%; }
                        </style>
                    </head>
                    <body>
                        <div class="label-container">
                            <div class="product-name">${name}</div>
                            <div class="brand-name">${brand || 'Generic'}</div>
                            <svg id="barcode"></svg>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                        <script>
                            JsBarcode("#barcode", "${barcode}", {
                                format: "CODE128",
                                width: 2,
                                height: 60,
                                displayValue: true,
                                fontSize: 14,
                                margin: 0
                            });
                            // Wait a bit for barcode to render
                            setTimeout(() => {
                                window.print();
                                window.close();
                            }, 500);
                        <\/script>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</body>
</html>
