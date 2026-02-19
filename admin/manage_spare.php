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
    <title>Manage Spare Parts - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            cursor: pointer;
        }
        select option {
            background: #0f172a;
            color: white;
            padding: 10px;
        }
        th {
            font-weight: 900;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-white/10 rounded-xl transition-all text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight">SPARE PARTS</h1>
            </div>
            <a href="addItems.php" class="bg-blue-500 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:bg-blue-400 transition-all shadow-lg shadow-blue-500/20 uppercase tracking-widest">+ Stock Entry</a>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative z-10">
        
        <!-- Inventory Valuation & Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-2">
            <div class="blue-gradient-card p-6 rounded-[2rem] flex flex-col justify-center">
                <p class="text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-2 opacity-70">Spare Parts Inventory Total</p>
                <h2 id="grand_inventory_value" class="text-2xl font-black text-white tracking-tighter">Rs. 0.00</h2>
            </div>
            <div class="md:col-span-3 blue-gradient-card p-6 rounded-[2rem] flex flex-col lg:flex-row gap-6 items-center">
                <div class="relative flex-grow w-full">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-300/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInventory" class="block w-full pl-14 pr-6 py-4 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-blue-300/20 text-sm font-bold" placeholder="Find spare by name, barcode or brand...">
                </div>

                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <select id="statusFilter" class="flex-grow lg:flex-none px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] outline-none transition-all hover:bg-white/10 border-blue-500/30">
                        <option value="all">Total Registry</option>
                        <option value="active">Active Stocks</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="blue-gradient-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-white/5 border-b border-white/10 font-black">
                    <tr>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em]">Product Details</th>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em] text-right">Buying Price</th>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em] text-right">Selling Price</th>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em] text-right">Stock Level</th>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em] text-right">Total Value</th>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-8 py-5 text-[10px] text-blue-300 uppercase tracking-[0.2em] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryBody" class="divide-y divide-white/5">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
            
            <!-- Pagination Controls -->
            <div class="px-8 py-6 border-t border-white/10 bg-white/5 flex justify-between items-center" id="paginationControls">
                <!-- Loaded via JS -->
            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="blue-gradient-card w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 border border-white/20">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Edit Spare Registry</h3>
                <button onclick="closeEditModal()" class="text-white/40 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="editForm" class="space-y-6">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Spare Designation</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 font-bold" placeholder="Product Name">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Brand Identity</label>
                    <input type="text" name="brand" id="edit_brand" class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 font-bold" placeholder="Brand Name">
                </div>
                <div>
                   <label class="block text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Fittings Profile</label>
                   <input type="text" name="v_types" id="edit_v_types" class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/20 font-bold" placeholder="Universal / Specific">
                </div>
                <div class="flex flex-col gap-3 mt-10">
                    <button type="submit" class="w-full py-5 bg-blue-500 text-white rounded-2xl font-black hover:bg-blue-400 shadow-xl shadow-blue-500/20 transition-all uppercase text-sm tracking-widest">Commit Changes</button>
                    <button type="button" onclick="closeEditModal()" class="w-full py-4 bg-white/5 text-white/50 rounded-2xl font-bold hover:bg-white/10 transition-all uppercase text-xs tracking-widest">Dismiss</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let debounceTimer;
        let currentStatus = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            loadInventory(1);

            document.getElementById('searchInventory').addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentPage = 1;
                    loadInventory(1, this.value, currentStatus);
                }, 300);
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
                currentStatus = this.value;
                loadInventory(1, document.getElementById('searchInventory').value, currentStatus);
            });

            document.getElementById('editForm').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_product');
                
                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.success) {
                    closeEditModal();
                    loadInventory(currentPage, document.getElementById('searchInventory').value, currentStatus);
                    Swal.fire('Success', 'Product updated!', 'success');
                }
            };
        });

        async function loadInventory(page, search = '', status = 'all') {
            currentPage = page;
            // Updated to fetch only spare_part type
            const res = await fetch(`manage_handler.php?action=fetch_inventory&page=${page}&search=${search}&type=spare_part&status=${status}`);
            const data = await res.json();
            
            const tbody = document.getElementById('inventoryBody');
            tbody.innerHTML = '';
            
            // Update Grand Total Value
            document.getElementById('grand_inventory_value').innerText = 'Rs. ' + parseFloat(data.grand_total_value || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if(data.products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-slate-400">No spare parts found.</td></tr>';
                return;
            }

            data.products.forEach(p => {
                let qtyDisplay = `<span class="font-black text-emerald-400">${p.total_stock}</span> <span class="text-emerald-300/60 font-bold">Units</span>`;

                let statusBadge = p.is_active == 1 
                    ? '<span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest">Active</span>'
                    : '<span class="px-3 py-1 bg-white/5 text-white/40 border border-white/10 rounded-lg text-[10px] font-black uppercase tracking-widest">Out of Stock</span>';

                let statusBtn = p.is_active == 1
                    ? `<button onclick="toggleStatus(${p.id}, 0)" class="text-[10px] font-black text-red-400/60 hover:text-red-400 transition-colors uppercase tracking-tight" title="Mark Out of Stock">Out of Stock</button>`
                    : `<button onclick="toggleStatus(${p.id}, 1)" class="text-[10px] font-black text-emerald-400/60 hover:text-emerald-400 transition-colors uppercase tracking-tight" title="Activate">Activate</button>`;

                const row = `
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="px-8 py-5">
                            <p class="font-black text-white text-sm tracking-tight">${p.name}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-black text-emerald-300 uppercase tracking-widest">${p.brand || 'No Brand'}</span>
                                <span class="w-1 h-1 rounded-full bg-white/20"></span>
                                <span class="text-[10px] font-mono text-emerald-300/50 font-black uppercase tracking-tighter">${p.barcode}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right font-mono font-black text-white/40">
                            Rs. ${parseFloat(p.buying_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-8 py-5 text-right font-mono font-black text-emerald-400">
                             Rs. ${parseFloat(p.selling_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-8 py-5 text-right font-mono">
                            ${qtyDisplay}
                        </td>
                        <td class="px-8 py-5 text-right font-mono font-black text-emerald-400">
                             Rs. ${parseFloat(p.total_value || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-8 py-5 text-center">
                            ${statusBadge}
                        </td>
                        <td class="px-8 py-5 text-center flex justify-center items-center gap-3">
                            <button onclick='showCompatibility(${JSON.stringify(p)})' class="p-2.5 text-blue-400 bg-blue-400/10 border border-blue-400/20 rounded-xl hover:bg-blue-400/20 transition-all" title="View Compatibility">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button onclick='printBarcode("${p.barcode}", "${p.name.replace(/'/g, "\\'")}", "${(p.brand || '').replace(/'/g, "\\'")}")' class="p-2.5 text-amber-400 bg-amber-400/10 border border-amber-400/20 rounded-xl hover:bg-amber-400/20 transition-all" title="Print Barcode">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"></path></svg>
                            </button>
                            <button onclick='editProduct(${JSON.stringify(p)})' class="p-2.5 text-indigo-400 bg-indigo-400/10 border border-indigo-400/20 rounded-xl hover:bg-indigo-400/20 transition-all" title="Edit Details">
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
                html += `<button onclick="loadInventory(${pg.current_page - 1}, document.getElementById('searchInventory').value, currentStatus)" class="p-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 text-blue-300 transition-all font-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>`;
            }

            // Page Numbers
            for(let i = 1; i <= pg.total_pages; i++) {
                const activeClass = i === pg.current_page ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-white/5 text-white/40 border-white/10 hover:bg-white/10';
                html += `<button onclick="loadInventory(${i}, document.getElementById('searchInventory').value, currentStatus)" class="w-9 h-9 flex items-center justify-center border rounded-lg text-sm font-bold transition-all ${activeClass}">${i}</button>`;
            }

            // Next Button (Icon only)
            if(pg.current_page < pg.total_pages) {
                html += `<button onclick="loadInventory(${pg.current_page + 1}, document.getElementById('searchInventory').value, currentStatus)" class="p-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 text-blue-300 transition-all font-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`;
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
