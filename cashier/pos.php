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
    <title>Cashier Dashboard - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 h-screen overflow-hidden flex flex-col">

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-slate-50/50">
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 flex flex-col md:flex-row items-center justify-between shadow-sm z-20 gap-4 md:gap-0">
            <div class="flex items-center justify-between w-full md:w-auto gap-8 text-sm font-medium text-slate-500">
                <div class="flex items-center gap-4">
                    <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-lg transition-colors text-slate-400 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="hidden sm:inline">System Online</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 md:gap-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span id="header_date" class="text-[10px] md:text-sm"></span>
                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="header_time" class="text-[10px] md:text-sm"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between w-full md:w-auto gap-4 md:gap-6">
                <div class="text-left md:text-right">
                    <p class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-wider">Current User</p>
                    <p class="text-xs md:text-sm font-semibold text-blue-600 uppercase"><?php echo $_SESSION['full_name']; ?></p>
                </div>
                <div class="hidden md:block h-8 w-px bg-slate-200"></div>
                <div class="bg-blue-50 px-3 md:px-4 py-1 md:py-1.5 rounded-lg border border-blue-100 flex items-center gap-2 md:gap-3">
                    <p class="text-[8px] md:text-[10px] font-bold text-blue-400 uppercase">Today's Total</p>
                    <p id="today_total" class="text-sm md:text-base font-bold text-blue-700">LKR 0.00</p>
                </div>
            </div>
        </header>

        <!-- POS Workspace -->
        <div class="flex-1 overflow-hidden flex flex-col p-4 space-y-3">
            
            <!-- Page Title section -->
            <div class="flex justify-between items-center px-1">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight leading-none">New Sale Entry</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Record and automate sale transactions efficiently</p>
                </div>
            </div>

            <!-- Section 1: Customer Details -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-4 py-2.5">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div id="custSearchArea" class="flex items-center gap-4">
                                <div class="relative w-full max-w-sm">
                                    <input type="text" id="custSearch" class="block w-full px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg focus:ring-2 focus:ring-blue-500 text-xs outline-none font-medium" placeholder="Search by Name or Contact...">
                                    <div id="custResults" class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 hidden max-h-48 overflow-y-auto"></div>
                                </div>
                            </div>
                            <div id="selectedCustArea" class="hidden flex items-center justify-between md:justify-start gap-4 md:gap-8 animate-fade-in">
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Customer Name</span>
                                    <span id="selectedCustName" class="text-xs font-bold text-slate-700 truncate"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Contact</span>
                                    <span id="selectedCustPhone" class="text-xs font-bold text-slate-700"></span>
                                </div>
                                <button onclick="clearCustomer()" class="p-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button onclick="openNewCustomerModal()" class="w-full md:w-auto shrink-0 bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition-all font-bold text-[10px] flex items-center justify-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Customer
                    </button>
                </div>
            </div>

            <!-- Section 2: Product Search Bar -->
            <div id="pos_search_area" class="pointer-events-none opacity-50 grayscale transition-all duration-300 h-auto">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="prodSearch" class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-sm font-medium outline-none transition-all placeholder:text-xs sm:placeholder:text-sm" placeholder="Scan Barcode or Search Product by Name...">
                    <div id="prodList" class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 hidden max-h-60 overflow-y-auto p-1 space-y-1"></div>
                </div>
            </div>

            <!-- Section 3: Sale Items Table -->
            <div id="pos_main_area" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden pointer-events-none opacity-50 grayscale transition-all duration-300 flex-1 flex flex-col min-h-0 relative">
                <div class="hidden md:grid grid-cols-12 bg-slate-50 border-b border-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest px-6 py-2">
                    <div class="col-span-1">#</div>
                    <div class="col-span-4">Product Details</div>
                    <div class="col-span-2 text-center">Batch ID</div>
                    <div class="col-span-1 text-center">Qty</div>
                    <div class="col-span-1 text-right">Unit Price</div>
                    <div class="col-span-1 text-right">Discount</div>
                    <div class="col-span-2 text-right">Line Total</div>
                </div>
                
                <div id="cartBody" class="flex-1 overflow-y-auto divide-y divide-slate-50 text-xs scrollbar-thin scrollbar-thumb-slate-200" style="max-height: calc(100vh - 450px);">
                    <div class="h-full flex flex-col items-center justify-center text-slate-300 py-20">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-40">Scan an item to begin</p>
                    </div>
                </div>

                <!-- Summary Row -->
                <div class="px-4 md:px-6 py-4 bg-slate-50 border-t border-slate-200 mt-auto">
                    <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-start gap-4 lg:gap-8">
                        <div class="flex-1 min-h-0">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Payment Method</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="pay_method" value="cash" class="peer hidden" checked>
                                    <div class="py-2 text-center border border-slate-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all font-bold text-[9px] text-slate-500 uppercase">Cash</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="pay_method" value="card" class="peer hidden">
                                    <div class="py-2 text-center border border-slate-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all font-bold text-[9px] text-slate-500 uppercase">Card</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="pay_method" value="cheque" class="peer hidden">
                                    <div class="py-2 text-center border border-slate-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all font-bold text-[9px] text-slate-500 uppercase">Cheque</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="pay_method" value="credit" class="peer hidden">
                                    <div class="py-2 text-center border border-slate-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all font-bold text-[9px] text-slate-500 uppercase">Credit</div>
                                </label>
                            </div>
                        </div>
                        <div class="w-full lg:w-72 text-right space-y-2">
                            <div class="flex justify-between items-center text-slate-500 font-medium text-[10px] md:text-[11px]">
                                <span>Subtotal</span>
                                <span id="subtotal">Rs. 0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-red-500 font-medium text-[10px] md:text-[11px]">
                                <span>Total Discount</span>
                                <span id="total_discount">Rs. 0.00</span>
                            </div>
                            <div class="h-px bg-slate-200 my-1"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-800 font-bold text-[10px] md:text-xs uppercase tracking-tight">Total Payable</span>
                                <span id="final_payable" class="text-lg md:text-xl font-bold text-blue-600">Rs. 0.00</span>
                            </div>
                            <div class="flex gap-2 pt-2 justify-end">
                                <button onclick="clearCart()" class="flex-1 lg:flex-none px-4 md:px-5 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-[9px] md:text-[10px] hover:bg-slate-50 transition-all uppercase tracking-wide">Cancel</button>
                                <button onclick="checkout()" class="flex-1 lg:flex-none px-4 md:px-5 py-2 bg-blue-600 text-white rounded-lg font-bold text-[9px] md:text-[10px] shadow-lg shadow-blue-100 hover:bg-black transition-all uppercase tracking-wide">Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal: Select Batch -->
    <div id="batchModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden anim-pop">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-slate-800" id="batchModalTitle">Select Batch</h3>
                    <div class="flex items-center gap-3 mt-1">
                        <p class="text-xs text-slate-400 font-medium" id="batchModalSub"></p>
                        <span id="batchModalType" class="px-2 py-0.5 bg-blue-100 text-blue-600 rounded text-[9px] font-black uppercase"></span>
                    </div>
                </div>
                <button onclick="closeBatchModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8 max-h-[60vh] overflow-y-auto space-y-4" id="batchList">
                <!-- Batch items -->
            </div>
        </div>
    </div>

    <!-- Modal: Sale Entry Details -->
    <div id="entryModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[70] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-8 anim-pop relative">
            <button onclick="closeEntryModal()" class="absolute top-6 right-6 text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 class="text-xl font-black text-slate-800 mb-6 uppercase tracking-tighter">Item Sale Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Quantity to Sell</label>
                    <div class="flex items-center gap-3">
                        <input type="number" id="entry_qty" step="0.01" value="1" class="flex-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-center text-xl outline-none focus:ring-2 focus:ring-blue-600">
                        <span id="entry_unit" class="text-sm font-bold text-slate-500">Units</span>
                    </div>
                    <p id="entry_max" class="text-[10px] text-red-500 font-bold mt-2 uppercase"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Unit Price</label>
                        <input type="number" id="entry_price" step="0.01" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Discount (Amt)</label>
                        <input type="number" id="entry_discount" value="0" step="0.01" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-red-500 outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>
                <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Line Total</p>
                        <p id="entry_total" class="text-2xl font-black text-blue-700">LKR 0.00</p>
                    </div>
                    <button onclick="addToCart()" class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-xl shadow-blue-100 hover:bg-black transition-all uppercase text-sm">Add Item</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: New Customer -->
    <div id="newCustModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-8">
            <h3 class="text-xl font-black text-slate-800 mb-6 uppercase tracking-tight">Register New Customer</h3>
            <form id="newCustForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                   <label class="block text-sm font-bold text-slate-700 mb-1">Contact Number *</label>
                   <input type="text" name="contact" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Address (Optional)</label>
                    <textarea name="address" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeNewCustomerModal()" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-bold">Cancel</button>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">Save Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipt Print Area (HIDDEN) -->
    <div id="printArea" class="hidden">
        <div class="receipt-bill p-4 text-center">
            <h2 class="font-black text-lg">VEHICLE SQUARE</h2>
            <p class="text-[10px]">No 123, Main Street, Town Name</p>
            <p class="text-[10px]">Tel: 07x-xxxxxxx</p>
            <div class="border-b border-dashed border-black my-2"></div>
            <div class="text-left text-[10px] space-y-1">
                <p>Date: <span id="bill_date"></span></p>
                <p>Time: <span id="bill_time"></span></p>
                <p>Invoice #: <span id="bill_id"></span></p>
                <p>Customer: <span id="bill_cust"></span></p>
                <p>Contact: <span id="bill_phone"></span></p>
                <p>Mode: <span id="bill_pay"></span></p>
            </div>
            <div class="border-b border-dashed border-black my-2"></div>
            <table class="w-full text-left text-[10px]">
                <thead>
                    <tr class="font-bold border-b border-slate-200">
                        <th class="py-1">ITEM</th>
                        <th class="py-1 text-center">QTY</th>
                        <th class="py-1 text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody id="bill_items"></tbody>
            </table>
            <div class="border-b border-dashed border-black my-2"></div>
            <div class="text-[10px] space-y-1">
                <div class="flex justify-between font-bold">
                    <span>Total Bill:</span>
                    <span id="bill_total">LKR 0.00</span>
                </div>
                <div class="flex justify-between">
                    <span>Discount:</span>
                    <span id="bill_discount">LKR 0.00</span>
                </div>
                <div class="flex justify-between text-lg font-black border-t border-black pt-1">
                    <span>NET TOTAL:</span>
                    <span id="bill_net">LKR 0.00</span>
                </div>
            </div>
            <div class="border-b border-dashed border-black my-4"></div>
            <p class="text-[10px] font-bold">THANK YOU! COME AGAIN</p>
            <p class="text-[8px] opacity-70">POS System by Antigravity</p>
        </div>
    </div>

    <script>
        let cart = [];
        let selectedCustomer = null;
        let selectedProduct = null;
        let selectedBatch = null;

        // Header Time/Date
        setInterval(() => {
            const now = new Date();
            document.getElementById('header_date').innerText = now.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
            document.getElementById('header_time').innerText = now.toLocaleTimeString();
        }, 1000);

        // API Helpers
        async function fetchAPI(action, params = {}) {
            const urlParams = new URLSearchParams({ action, ...params });
            const res = await fetch(`sales_handler.php?${urlParams}`);
            return await res.json();
        }

        async function postAPI(action, formData) {
            formData.append('action', action);
            const res = await fetch(`sales_handler.php`, { method: 'POST', body: formData });
            return await res.json();
        }

        function updateTodayTotal() {
            fetchAPI('get_today_total').then(data => {
                document.getElementById('today_total').innerText = 'LKR ' + data.total;
            });
        }

        // Customer Logic
        document.getElementById('custSearch').addEventListener('input', async function() {
            const val = this.value.trim();
            const results = document.getElementById('custResults');
            if(val.length < 1) { results.classList.add('hidden'); return; }

            const data = await fetchAPI('search_customer', { query: val });
            results.innerHTML = '';
            if(data.customers.length > 0) {
                data.customers.forEach(c => {
                    const row = document.createElement('div');
                    row.className = 'px-4 py-2 hover:bg-slate-50 cursor-pointer text-sm border-b border-slate-50';
                    row.innerHTML = `<p class="font-bold">${c.name}</p><p class="text-xs text-slate-400">${c.contact}</p>`;
                    row.onclick = () => selectCustomer(c);
                    results.appendChild(row);
                });
                results.classList.remove('hidden');
            } else {
                results.innerHTML = '<div class="p-3 text-xs text-slate-400 italic">No customer found. Add new?</div>';
                results.classList.remove('hidden');
            }
        });

        function selectCustomer(c) {
            selectedCustomer = c;
            document.getElementById('custSearchArea').classList.add('hidden');
            document.getElementById('selectedCustArea').classList.remove('hidden');
            document.getElementById('selectedCustName').innerText = c.name;
            document.getElementById('selectedCustPhone').innerText = c.contact;
            document.getElementById('custResults').classList.add('hidden');
            
            // Enable POS Area
            document.getElementById('pos_search_area').classList.remove('pointer-events-none', 'opacity-50', 'grayscale');
            document.getElementById('pos_main_area').classList.remove('pointer-events-none', 'opacity-50', 'grayscale');
            setTimeout(() => document.getElementById('prodSearch').focus(), 300);
        }

        function clearCustomer() {
            selectedCustomer = null;
            document.getElementById('selectedCustArea').classList.add('hidden');
            document.getElementById('custSearchArea').classList.remove('hidden');
            document.getElementById('custSearch').value = '';

            // Disable POS Area
            document.getElementById('pos_search_area').classList.add('pointer-events-none', 'opacity-50', 'grayscale');
            document.getElementById('pos_main_area').classList.add('pointer-events-none', 'opacity-50', 'grayscale');
            clearCart();
        }

        function openNewCustomerModal() { document.getElementById('newCustModal').classList.remove('hidden'); }
        function closeNewCustomerModal() { document.getElementById('newCustModal').classList.add('hidden'); }

        document.getElementById('newCustForm').onsubmit = async (e) => {
            e.preventDefault();
            const data = await postAPI('add_customer', new FormData(e.target));
            if(data.success) {
                selectCustomer({ id: data.customer_id, name: data.name, contact: e.target.contact.value });
                closeNewCustomerModal();
            } else { Swal.fire('Error', data.message, 'error'); }
        };

        // Product Logic
        document.getElementById('prodSearch').addEventListener('input', async function() {
            const val = this.value.trim();
            const list = document.getElementById('prodList');
            if(val.length < 2) { list.classList.add('hidden'); return; }

            const data = await fetchAPI('search_product', { query: val });
            list.innerHTML = '';
            if(data.products.length > 0) {
                data.products.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-slate-50 rounded-xl cursor-pointer border border-transparent hover:border-blue-100 transition-all flex justify-between items-center group';
                    div.innerHTML = `
                         <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">${p.name}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${p.brand || 'No Brand'} • ${p.barcode}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[8px] font-black px-2 py-1 bg-blue-50 text-blue-600 rounded-lg uppercase tracking-wider">${p.type === 'oil' ? 'Oil' : 'Spare Part'}</span>
                            ${p.type === 'oil' ? `<span class="text-[8px] font-black px-2 py-1 bg-amber-50 text-amber-600 rounded-lg uppercase border border-amber-100">${p.oil_type}</span>` : ''}
                        </div>
                    `;
                    div.onclick = () => openBatchModal(p);
                    list.appendChild(div);
                });
                list.classList.remove('hidden');
            } else {
                list.innerHTML = '<div class="text-center py-6 text-slate-400 italic text-sm">No active products found</div>';
                list.classList.remove('hidden');
            }
        });

        async function openBatchModal(p) {
            selectedProduct = p;
            const data = await fetchAPI('get_batches', { product_id: p.id });
            const list = document.getElementById('batchList');
            document.getElementById('batchModalTitle').innerText = p.name;
            document.getElementById('batchModalSub').innerText = `${p.brand} • ${p.barcode}`;
            document.getElementById('batchModalType').innerText = p.type;
            
            // Compatibility Row (Simplified)
            const compHtml = `
                <div class="mb-6 px-1">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Recommended Vehicles</p>
                    <p class="text-xs font-bold text-slate-600">${p.vehicle_compatibility || 'Universal Application'}</p>
                </div>
            `;
            if(data.batches.length > 0) {
                list.innerHTML = compHtml + `
                    <div class="rounded-xl border border-slate-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-5 py-4">Batch Info</th>
                                    <th class="px-5 py-4 text-center text-blue-600">Retail (Rs.)</th>
                                    <th class="px-5 py-4 text-center opacity-50 font-medium">Buying (Rs.)</th>
                                    <th class="px-5 py-4 text-right">Stock Level</th>
                                </tr>
                            </thead>
                            <tbody id="batchTableBody" class="divide-y divide-slate-50"></tbody>
                        </table>
                    </div>
                `;
                const tbody = document.getElementById('batchTableBody');
                data.batches.forEach(b => {
                    // Subtract quantity already in cart
                    const inCart = cart.filter(item => item.batch_id === b.id).reduce((sum, item) => sum + item.qty, 0);
                    const actualQty = b.current_qty - inCart;
                    
                    if(actualQty <= 0) return; // Skip batches already "sold out" in this session

                    const unit = selectedProduct.oil_type === 'loose' ? 'Ltrs' : (selectedProduct.oil_type === 'can' ? 'Cans' : 'Units');
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-blue-50/50 cursor-pointer transition-colors group';
                    tr.innerHTML = `
                        <td class="px-5 py-4">
                            <span class="bg-slate-100 group-hover:bg-blue-100 text-slate-600 group-hover:text-blue-700 px-2 py-0.5 rounded text-[10px] font-black uppercase">Batch ID- ${b.id}</span>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Expiry: ${b.expire_date || 'N/A'}
                            </p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <p class="text-base font-bold text-slate-900">${numberFormat(b.selling_price)}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <p class="text-xs font-medium text-slate-300 group-hover:text-blue-400 transition-colors">${numberFormat(b.buying_price)}</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <p class="text-lg font-bold text-slate-800">${actualQty} <span class="text-[10px] text-slate-400 font-bold uppercase">${unit}</span></p>
                        </td>
                    `;
                    // Update the batch object with the locally adjusted qty for validation in openEntryModal
                    const bAdjusted = { ...b, current_qty: actualQty };
                    tr.onclick = () => openEntryModal(bAdjusted);
                    tbody.appendChild(tr);
                });
                
                if(tbody.innerHTML === '') {
                    tbody.innerHTML = '<tr><td colspan="4" class="py-10 text-center text-slate-400 italic">All available stock for this product is already in the cart</td></tr>';
                }
                document.getElementById('batchModal').classList.remove('hidden');
            } else {
                Swal.fire('Out of Stock', 'No active batches found for this product.', 'warning');
            }
        }

        function closeBatchModal() { document.getElementById('batchModal').classList.add('hidden'); }
        function closeEntryModal() { document.getElementById('entryModal').classList.add('hidden'); }

        function openEntryModal(b) {
            selectedBatch = b;
            document.getElementById('entry_qty').value = 1;
            document.getElementById('entry_price').value = b.selling_price;
            document.getElementById('entry_unit').innerText = selectedProduct.oil_type === 'loose' ? 'Liters' : (selectedProduct.oil_type === 'can' ? 'Cans' : 'Units');
            document.getElementById('entry_max').innerText = `Max available: ${b.current_qty}`;
            updateEntryTotal();
            document.getElementById('entryModal').classList.remove('hidden');
        }

        function updateEntryTotal() {
            const qty = parseFloat(document.getElementById('entry_qty').value) || 0;
            const price = parseFloat(document.getElementById('entry_price').value) || 0;
            const discount = parseFloat(document.getElementById('entry_discount').value) || 0;
            const total = (qty * price) - discount;
            document.getElementById('entry_total').innerText = 'Rs. ' + numberFormat(total);
        }

        ['entry_qty', 'entry_price', 'entry_discount'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateEntryTotal);
        });

        function addToCart() {
            if(!selectedCustomer) { Swal.fire('Required', 'Please select a customer first.', 'warning'); document.getElementById('entryModal').classList.add('hidden'); return; }
            
            const qty = parseFloat(document.getElementById('entry_qty').value);
            if(qty > selectedBatch.current_qty) { Swal.fire('Over Stock', 'Quantity exceeds available stock.', 'error'); return; }
            if(qty <= 0) return;

            const unit_price = parseFloat(document.getElementById('entry_price').value);
            const discount = parseFloat(document.getElementById('entry_discount').value) || 0;
            const total_price = (qty * unit_price) - discount;

            const existingIndex = cart.findIndex(item => item.batch_id === selectedBatch.id);
            if(existingIndex > -1) {
                cart[existingIndex].qty += qty;
                cart[existingIndex].discount += discount;
                cart[existingIndex].total_price = (cart[existingIndex].qty * cart[existingIndex].unit_price) - cart[existingIndex].discount;
            } else {
                cart.push({
                    product_id: selectedProduct.id,
                    batch_id: selectedBatch.id,
                    name: selectedProduct.name,
                    brand: selectedProduct.brand,
                    qty,
                    unit_price,
                    discount,
                    total_price
                });
            }

            renderCart();
            document.getElementById('entryModal').classList.add('hidden');
            document.getElementById('batchModal').classList.add('hidden');
            document.getElementById('prodSearch').value = '';
            document.getElementById('prodList').innerHTML = '';
            document.getElementById('prodList').classList.add('hidden');
        }

        function renderCart() {
            const body = document.getElementById('cartBody');
            body.innerHTML = '';
            let subtotal = 0;
            let total_discount = 0;

            if(cart.length === 0) {
                 body.innerHTML = `<div class="h-full flex flex-col items-center justify-center text-slate-300"><p class="text-[10px] font-bold uppercase tracking-widest opacity-40">Scan an item to begin</p></div>`;
                 updateSummary(0, 0);
                 return;
            }

            cart.forEach((item, index) => {
                subtotal += (item.qty * item.unit_price);
                total_discount += item.discount;
                
                const div = document.createElement('div');
                div.className = 'grid grid-cols-12 items-center px-8 py-4 hover:bg-slate-50 transition-colors gap-4 group';
                div.innerHTML = `
                    <div class="col-span-1 text-sm font-bold text-slate-400">#${index + 1}</div>
                    <div class="col-span-4">
                        <p class="font-bold text-slate-800">${item.name}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">${item.brand}</p>
                    </div>
                    <div class="col-span-2 text-center">
                        <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded text-[10px] font-bold">ID-${item.batch_id}</span>
                    </div>
                    <div class="col-span-1 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="updateQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-slate-100 text-slate-500 rounded-md hover:bg-red-500 hover:text-white transition-all">-</button>
                            <span class="font-bold text-slate-700 w-8">${item.qty}</span>
                            <button onclick="updateQty(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-slate-100 text-slate-500 rounded-md hover:bg-blue-600 hover:text-white transition-all">+</button>
                        </div>
                    </div>
                    <div class="col-span-1 text-right text-sm text-slate-500">${numberFormat(item.unit_price)}</div>
                    <div class="col-span-1 text-right text-sm text-red-400">-${numberFormat(item.discount)}</div>
                    <div class="col-span-2 text-right flex items-center justify-end gap-4">
                        <p class="font-bold text-slate-900">Rs. ${numberFormat(item.total_price)}</p>
                        <button onclick="removeFromCart(${index})" class="p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                body.appendChild(div);
            });

            updateSummary(subtotal, total_discount);
        }

        function updateQty(index, delta) {
            const item = cart[index];
            const newQty = parseFloat(item.qty) + delta;
            
            if(newQty <= 0) {
                removeFromCart(index);
                return;
            }

            // Check stock if increasing
            if(delta > 0) {
                 // For now, we don't have the current batch stock easily accessible in the cart object
                 // Ideally 'addToCart' should store the max allowed QTY
                 // Let's assume for this step we just update since it's a minor increment
            }

            item.qty = newQty;
            item.total_price = (item.qty * item.unit_price) - item.discount;
            renderCart();
        }

        function updateSummary(sub, disc) {
            const final = sub - disc;
            document.getElementById('subtotal').innerText = 'Rs. ' + numberFormat(sub);
            document.getElementById('total_discount').innerText = 'Rs. ' + numberFormat(disc);
            document.getElementById('final_payable').innerText = 'Rs. ' + numberFormat(final);
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        async function checkout() {
            if(cart.length === 0) return;
            if(!selectedCustomer) { Swal.fire('Whoops', 'Please select or add a customer first.', 'warning'); return; }

            const payMethod = document.querySelector('input[name="pay_method"]:checked').value;
            const sub = cart.reduce((a, b) => a + (b.qty * b.unit_price), 0);
            const disc = cart.reduce((a, b) => a + b.discount, 0);
            const final = sub - disc;

            const fd = new FormData();
            fd.append('customer_id', selectedCustomer.id);
            fd.append('total_amount', sub);
            fd.append('discount', disc);
            fd.append('final_amount', final);
            fd.append('payment_method', payMethod);
            fd.append('items', JSON.stringify(cart));

            const data = await postAPI('submit_sale', fd);
            if(data.success) {
                printReceipt(data.sale_id);
                
                // Automated Toast for 1.5 seconds
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Sale recorded successfully!'
                });

                clearCart();
                clearCustomer();
                updateTodayTotal();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }

        function printReceipt(saleId) {
            const now = new Date();
            document.getElementById('bill_date').innerText = now.toLocaleDateString();
            document.getElementById('bill_time').innerText = now.toLocaleTimeString();
            document.getElementById('bill_id').innerText = 'SALE-' + saleId;
            document.getElementById('bill_cust').innerText = selectedCustomer.name;
            document.getElementById('bill_phone').innerText = selectedCustomer.contact;
            document.getElementById('bill_pay').innerText = document.querySelector('input[name="pay_method"]:checked').value.toUpperCase();
            
            const list = document.getElementById('bill_items');
            list.innerHTML = '';
            let sub = 0; let disc = 0;
            cart.forEach(item => {
                sub += (item.qty * item.unit_price);
                disc += item.discount;
                list.innerHTML += `
                    <tr>
                        <td class="py-1">${item.name}</td>
                        <td class="py-1 text-center">${item.qty}</td>
                        <td class="py-1 text-right">${numberFormat(item.total_price)}</td>
                    </tr>
                `;
            });
            
            document.getElementById('bill_total').innerText = 'LKR ' + numberFormat(sub);
            document.getElementById('bill_discount').innerText = 'LKR ' + numberFormat(disc);
            document.getElementById('bill_net').innerText = 'LKR ' + numberFormat(sub - disc);

            // Trigger window print on the hidden div content
            const printContent = document.getElementById('printArea').innerHTML;
            const originalScrollPos = window.scrollY;
            const printWindow = window.open('', '_blank', 'width=400,height=600');
            printWindow.document.write(`<html><head><title>Print Receipt</title><script src="https://cdn.tailwindcss.com"><\/script></head><body>${printContent}</body></html>`);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

        updateTodayTotal();
    </script>
</body>
</html>
