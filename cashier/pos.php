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
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
        }
        .bg-main {
            background: url('../admin/public/admin_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(238, 242, 255, 0.7) 100%);
            pointer-events: none; z-index: 0;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .blue-gradient-header {
            background: linear-gradient(to right, #06266aff, #1e40af);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .btn-blue {
            background: linear-gradient(to right, #2563eb, #1e40af);
            color: white;
            padding: 0.6rem 2rem;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            border: none;
        }
        .btn-blue:hover {
            background: linear-gradient(to right, #1d4ed8, #1e3a8a);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-main h-screen overflow-hidden flex flex-col relative ">
    <div class="colorful-overlay"></div>
    
    <!-- Main Content -->
    <main class="h-screen max-h-screen flex flex-col relative z-20 overflow-hidden">
        <!-- Top Navigation Bar -->
        <header class="blue-gradient-header px-4 md:px-6 py-2 flex flex-col sm:flex-row items-center justify-between shadow-lg z-20 gap-3 sm:gap-0 border-b border-white/10">
            <div class="flex items-center justify-between w-full sm:w-auto gap-4 md:gap-8 text-sm font-medium text-white/70">
                <div class="flex items-center gap-3">
                    <a href="dashboard.php" class="p-1.5 hover:bg-white/10 rounded-lg transition-colors text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/50 animate-pulse"></span>
                        <span class="hidden lg:inline font-bold uppercase tracking-widest text-[9px] text-white">Online</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span id="header_date" class="text-[10px] font-bold text-white"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="header_time" class="text-[10px] font-bold text-white"></span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between w-full sm:w-auto gap-4 md:gap-8">
                <div class="text-right hidden sm:block">
                    <p class="text-[8px] text-white/60 font-bold uppercase tracking-widest leading-none mb-1">Operator</p>
                    <p class="text-[11px] font-bold text-white uppercase truncate max-w-[120px]"><?php echo $_SESSION['full_name']; ?></p>
                </div>
                <div class="h-8 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-3 px-3 py-1.5 backdrop-blur-md border border-white/20 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center text-blue-600 shadow-lg shadow-white/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-black text-white/70 uppercase tracking-widest leading-none mb-1">Today's Sales</p>
                        <p id="today_total" class="text-xs font-black text-white leading-none tracking-tight">LKR 0.00</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- POS Workspace -->
        <div class="flex-1 min-h-0 overflow-hidden flex flex-col p-4 space-y-2">
            
            <!-- Page Title section -->
            <div class="flex justify-between items-center px-1">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-none uppercase">Customer Details</h1>
                    <p class="text-[11px] text-slate-600 font-bold uppercase tracking-widest mt-1">Select or register a customer to begin</p>
                </div>
            </div>

            <!-- Section 1: Customer Details -->
            <div class="glass-panel px-4 py-2.5 bg-white/80 backdrop-blur-sm border-blue-100/50">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="w-8 h-8 bg-blue-100/50 rounded-xl flex items-center justify-center text-blue-600 shrink-0 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div id="custSearchArea" class="flex items-center gap-2">
                                <div class="relative w-full max-w-sm">
                                    <input type="text" id="custSearch" class="block w-full px-3 py-2 bg-slate-50/50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-[11px] outline-none font-bold placeholder:text-slate-300 transition-all uppercase tracking-wider" placeholder="Search by name or contact...">
                                    <div id="custResults" class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 hidden max-h-48 overflow-y-auto p-1"></div>
                                </div>
                            </div>
                            <div id="selectedCustArea" class="hidden flex items-center gap-6 animate-fade-in overflow-hidden">
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] mb-0.5">Customer</span>
                                    <span id="selectedCustName" class="text-[11px] font-black text-slate-950 truncate uppercase tracking-tight"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] mb-0.5">Contact</span>
                                    <span id="selectedCustPhone" class="text-[11px] font-black text-slate-950 tracking-wider"></span>
                                </div>
                                <button onclick="clearCustomer()" class="p-1.5 bg-red-50 text-red-500 rounded-[10px] hover:bg-red-500 hover:text-white transition-all shrink-0 shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button onclick="openNewCustomerModal()" class="w-full lg:w-auto shrink-0 bg-blue-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        New Registration
                    </button>
                </div>
            </div>

            <!-- Page Title section -->
            <div class="flex justify-between items-center px-1 pt-2">
                <div>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight leading-none uppercase">Product Items Info</h1>
                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-wider mt-1">Search and add items to the current order</p>
                </div>
            </div>

            <!-- Section 2: Product Search Bar -->
            <div id="pos_search_area" class="transition-all duration-300 shrink-0">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="prodSearch" class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-sm font-medium outline-none transition-all placeholder:text-xs sm:placeholder:text-sm" placeholder="Scan Barcode or Search Product by Name...">
                    <div id="prodList" class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 hidden max-h-60 overflow-y-auto p-1 space-y-1"></div>
                </div>
            </div>

            <!-- Section  section 3: Sale Items Table -->
            <div id="pos_main_area" class="glass-panel overflow-hidden transition-all duration-300 flex-1 flex flex-col min-h-0 relative bg-white/80 backdrop-blur-sm">
                <div class="hidden lg:grid grid-cols-12 blue-gradient-header text-[11px] font-black uppercase tracking-[0.2em] px-6 py-4 border-b border-white/10">
                    <div class="col-span-3">Product Profile</div>
                    <div class="col-span-2 text-center">Category / Type</div>
                    <div class="col-span-2 text-right">Net Unit Price</div>
                    <div class="col-span-2 text-center">Quantity</div>
                    <div class="col-span-2 text-right">Total Payable</div>
                    <div class="col-span-1 text-center">Action</div>
                </div>
                
                <div id="cartBody" class="flex-1 overflow-y-auto min-h-0 divide-y divide-slate-50 text-xs scrollbar-thin scrollbar-thumb-slate-200 pb-36">
                    <div class="h-full flex flex-col items-center justify-center text-slate-300 py-20">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-40">Scan an item to begin</p>
                    </div>
                </div>

                <!-- Summary Row (Fixed Bottom) -->
                <div class="absolute bottom-0 left-0 right-0 px-4 md:px-8 py-3 md:py-4 bg-white/95 backdrop-blur-3xl border-t border-slate-200/80 z-30 shadow-[0_-20px_40px_-15px_rgba(0,0,0,0.1)]">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 lg:gap-8">
                        <div class="flex-1 hidden xl:flex items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] leading-none mb-1">Active Cart</p>
                                    <p id="item_count_label" class="text-[11px] font-black text-slate-950 uppercase">0 Items Loaded</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full lg:w-auto flex flex-col md:flex-row items-center gap-4 md:gap-8 lg:gap-10">
                            <div class="flex items-center justify-center md:justify-end gap-6 md:gap-10 w-full md:w-auto">
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">Subtotal</p>
                                    <p id="subtotal" class="text-xs md:text-sm font-black text-slate-700 tracking-tight">Rs. 0.00</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-0.5">Discounts</p>
                                    <p id="total_discount" class="text-xs md:text-sm font-black text-red-500 tracking-tight">Rs. 0.00</p>
                                </div>
                                <div class="h-8 w-px bg-slate-200/50 hidden md:block"></div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-tight mb-0.5 opacity-80">Net Payable</p>
                                    <p id="final_payable" class="text-2xl md:text-3xl font-black text-blue-700 tracking-tighter leading-none">Rs. 0.00</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 w-full md:w-auto">
                                <button onclick="clearCart()" class="flex-1 md:flex-none px-6 py-3 bg-slate-50 text-slate-400 rounded-xl font-black text-[10px] hover:bg-red-50 hover:text-red-500 border border-slate-100 transition-all uppercase tracking-widest shadow-sm">Discard</button>
                                <button onclick="checkout()" class="flex-1 md:flex-none px-10 py-3 bg-blue-600 text-white rounded-xl font-black text-[10px] hover:bg-blue-700 transition-all uppercase tracking-[0.2em] shadow-xl shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98] border border-blue-500/50">Complete Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal: Payment Method Selection -->
    <div id="paymentModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-md z-[100] hidden grid place-items-center p-4">
        <div class="bg-white backdrop-blur-2xl w-full max-w-[420px] rounded-[2rem] shadow-[0_30px_80px_-20px_rgba(0,0,0,0.4)] overflow-hidden anim-pop border border-white/40">
            <!-- Header -->
            <div class="px-6 pt-6 pb-4 flex items-center justify-between border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter leading-none">Complete Sale</h3>
                        <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-[0.1em] opacity-60">Finalize transaction</p>
                    </div>
                </div>
                <button onclick="closePaymentModal()" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-slate-300 hover:text-slate-900 hover:rotate-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <!-- Totals Section -->
                <div class="space-y-2">
                    <div class="bg-slate-50/50 rounded-2xl p-3 flex justify-between items-center border border-slate-100/50">
                        <span class="text-slate-400 font-black text-[10px] uppercase tracking-widest">Gross total</span>
                        <span id="pay_modal_total" class="text-xl font-black text-slate-900 tracking-tight">Rs. 0.00</span>
                    </div>

                    <div class="bg-red-50/30 rounded-2xl p-3 flex justify-between items-center border border-red-100/30">
                        <span class="text-red-400 font-black text-[10px] uppercase tracking-widest">Savings / items disc</span>
                        <span id="pay_modal_item_disc" class="text-base font-black text-red-500 tracking-tight">Rs. 0.00</span>
                    </div>

                    <div class="px-1 pt-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Wholesale discount</label>
                        <div class="relative mb-3">
                            <span class="absolute left-5 inset-y-0 flex items-center text-slate-300 font-black text-base">Rs.</span>
                            <input type="number" id="wholesale_discount" value="0" step="0.01" oninput="updatePaymentTotal()" class="w-full pl-12 pr-6 py-3 bg-white border-2 border-slate-100 rounded-xl font-black text-xl text-slate-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all shadow-sm text-center">
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <button onclick="applyPercentageDiscount(5)" class="py-2 bg-white border border-slate-100 hover:bg-blue-600 hover:text-white hover:border-blue-600 rounded-xl text-[11px] font-black transition-all shadow-sm">5%</button>
                            <button onclick="applyPercentageDiscount(10)" class="py-2 bg-white border border-slate-100 hover:bg-blue-600 hover:text-white hover:border-blue-600 rounded-xl text-[11px] font-black transition-all shadow-sm">10%</button>
                            <button onclick="applyPercentageDiscount(20)" class="py-2 bg-white border border-slate-100 hover:bg-blue-600 hover:text-white hover:border-blue-600 rounded-xl text-[11px] font-black transition-all shadow-sm">20%</button>
                            <button onclick="applyPercentageDiscount(25)" class="py-2 bg-white border border-slate-100 hover:bg-blue-600 hover:text-white hover:border-blue-600 rounded-xl text-[11px] font-black transition-all shadow-sm">25%</button>
                        </div>
                    </div>
                </div>

                <!-- Payable Box -->
                <div class="bg-blue-600 rounded-[1.75rem] p-4 flex justify-between items-center shadow-xl shadow-blue-600/30 border border-blue-400/20">
                    <div>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest mb-1 opacity-70">Amount to pay</p>
                        <p id="final_pay_modal_total" class="text-2xl font-black text-white tracking-tighter leading-none">Rs. 0.00</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white backdrop-blur-md border border-white/10 shadow-inner">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.21 1.87 1.15 0 1.94-.54 1.94-1.3 0-.7-.56-1.17-2.02-1.54-1.95-.51-3.54-1.29-3.54-3.32 0-1.7 1.34-2.85 3.05-3.21V5h2.67v1.86c1.29.23 2.53 1.02 2.87 2.44H14.5c-.24-.76-.83-1.19-1.84-1.19-1.02 0-1.56.49-1.56 1.15 0 .64.57 1.01 2.05 1.39 2.02.53 3.51 1.41 3.51 3.32.01 1.91-1.39 3.19-3.2 3.52z"/></svg>
                    </div>
                </div>

                <!-- Payment Methods Selection -->
                <div class="pt-1">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 ml-1">Payment Method</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <button onclick="processPayment('cash')" class="group p-4 bg-slate-50 border-2 border-transparent hover:border-emerald-500 hover:bg-emerald-50 transition-all rounded-[1.5rem] text-center shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-white text-emerald-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest group-hover:text-emerald-700">Cash Sale</span>
                        </button>
                        <button onclick="processPayment('card')" class="group p-4 bg-slate-50 border-2 border-transparent hover:border-blue-500 hover:bg-blue-50 transition-all rounded-[1.5rem] text-center shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-white text-blue-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest group-hover:text-blue-700">Card Payment</span>
                        </button>
                        <button onclick="processPayment('cheque')" class="group p-4 bg-slate-50 border-2 border-transparent hover:border-slate-500 hover:bg-slate-100 transition-all rounded-[1.5rem] text-center shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-white text-slate-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-slate-600 group-hover:text-white transition-all shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest group-hover:text-slate-700">Cheque</span>
                        </button>
                        <button onclick="processPayment('credit')" class="group p-4 bg-slate-50 border-2 border-transparent hover:border-orange-500 hover:bg-orange-50 transition-all rounded-[1.5rem] text-center shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-white text-orange-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-all shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest group-hover:text-orange-700">Credit Sale</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Select Batch -->
    <div id="batchModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white/80 backdrop-blur-xl w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden anim-pop border border-white/50">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter leading-none" id="batchModalTitle">Select Batch</h3>
                        <div class="flex items-center gap-3 mt-1.5">
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest" id="batchModalSub"></p>
                            <span id="batchModalType" class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[8px] font-black uppercase tracking-widest border border-blue-100"></span>
                        </div>
                    </div>
                </div>
                <button onclick="closeBatchModal()" class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto" id="batchList">
                <!-- Batch items -->
            </div>
        </div>
    </div>

    <!-- Modal: Sale Entry Details -->
    <div id="entryModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[70] hidden flex items-center justify-center p-4">
        <div class="bg-white/80 backdrop-blur-xl w-full max-w-[400px] rounded-3xl shadow-2xl p-6 anim-pop relative border border-white/50">
            <button onclick="closeEntryModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Configure Sale</h3>
            </div>

            <div class="space-y-3">
                <div>
                   <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Quantity to Sell</label>
                   <div class="flex items-center gap-2">
                       <input type="number" id="entry_qty" step="1" value="1" oninput="updateEntryTotal()" class="flex-1 px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl font-bold text-center text-lg text-blue-600 outline-none focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm">
                       <span id="entry_unit" class="text-[9px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-3 py-2 rounded-lg border border-slate-200 min-w-[60px] text-center">Units</span>
                   </div>
                   <p id="entry_max" class="text-[9px] text-red-500 font-bold mt-1.5 uppercase tracking-tight ml-1"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-800 uppercase tracking-widest mb-1 ml-1 opacity-70">Labled Price</label>
                        <input type="number" id="entry_labeled_price" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl font-bold text-slate-800 outline-none text-[11px]">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-blue-600 uppercase tracking-widest mb-1 ml-1">Co. Base Price</label>
                        <input type="number" id="entry_base_price" readonly class="w-full px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl font-bold text-blue-700 outline-none text-[11px]">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 pt-0.5">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-900 uppercase tracking-widest mb-1.5 ml-1">Cashier Price (Net)</label>
                        <input type="number" id="entry_price" step="0.01" oninput="syncFromPrice()" class="w-full px-3 py-2.5 bg-white border-2 border-slate-100 rounded-xl font-bold text-slate-800 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-red-500 uppercase tracking-widest mb-1.5 ml-1">Manual Discount</label>
                        <input type="number" id="entry_discount" value="0" step="0.01" oninput="syncFromDiscount()" class="w-full px-3 py-2.5 bg-white border-2 border-red-50 rounded-xl font-bold text-red-500 outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all shadow-sm">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-1">
                    <div class="flex justify-between items-center mb-1">
                        <div>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-0.5">Total Payable</p>
                            <p id="entry_total" class="text-2xl font-black text-slate-950 tracking-tighter">Rs. 0.00</p>
                        </div>
                        <button onclick="addToCart()" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all shadow-md shadow-blue-600/20">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: New Customer -->
    <div id="newCustModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white/80 backdrop-blur-xl w-full max-w-[400px] rounded-3xl shadow-2xl p-6 border border-white/50 relative anim-pop">
            <button onclick="closeNewCustomerModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter mb-6 flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                Register Customer
            </h3>
            <form id="newCustForm" class="space-y-3">
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Full Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 font-bold text-slate-700 transition-all shadow-sm">
                </div>
                <div>
                   <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Contact Number *</label>
                   <input type="text" name="contact" required class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 font-bold text-slate-700 transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Address (Optional)</label>
                    <textarea name="address" class="w-full px-4 py-2 bg-white/80 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 font-bold text-slate-700 transition-all shadow-sm min-h-[70px]"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeNewCustomerModal()" class="px-5 py-2 text-slate-400 hover:text-slate-600 font-bold uppercase text-[9px] tracking-widest transition-all">Cancel</button>
                    <button type="submit" class="btn-blue px-7 py-2.5 text-[10px]">Save Customer</button>
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
        let isCheckingOut = false;

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
            
            setTimeout(() => {
                if(isCheckingOut) {
                    checkout();
                } else {
                    document.getElementById('prodSearch').focus();
                }
            }, 300);
        }

        function clearCustomer() {
            selectedCustomer = null;
            isCheckingOut = false;
            document.getElementById('selectedCustArea').classList.add('hidden');
            document.getElementById('custSearchArea').classList.remove('hidden');
            document.getElementById('custSearch').value = '';
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
                // Add Table Header
                const header = document.createElement('div');
                header.className = 'px-4 py-3 blue-gradient-header border-b border-blue-700 flex items-center text-[9px] uppercase tracking-widest sticky top-0 z-10 rounded-t-xl';
                header.innerHTML = `
                    <div class="w-[22%]">Product Name</div>
                    <div class="w-[15%]">Barcode</div>
                    <div class="w-[10%] text-center">Type</div>
                    <div class="w-[8%] text-center">Stock</div>
                    <div class="w-[15%] text-right pr-2">Buying</div>
                    <div class="w-[15%] text-right pr-2">Labeled</div>
                    <div class="w-[15%] text-right pr-2">Est. Selling</div>
                `;
                list.appendChild(header);

                data.products.forEach(p => {
                    p.batches.forEach(b => {
                        const div = document.createElement('div');
                        const isOil = p.type === 'oil';
                        let typeBadge = '';
                        if(isOil) {
                            if(p.oil_type === 'loose') {
                                typeBadge = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-600 text-[8px] font-black border border-emerald-100 uppercase">
                                    LOOSE
                                </span>`;
                            } else {
                                typeBadge = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[8px] font-black border border-blue-100 uppercase">
                                    CAN
                                </span>`;
                            }
                        } else {
                            typeBadge = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-50 text-slate-500 text-[8px] font-black border border-slate-100 uppercase">
                                SPARE
                            </span>`;
                        }
                        
                        div.className = 'flex items-center gap-3 p-3 hover:bg-blue-50 cursor-pointer border-b border-slate-50 last:border-0 active:bg-blue-100 transition-all m-0.5';
                        div.innerHTML = `
                            <!-- Col 1: Name -->
                            <div class="w-[22%] min-w-0">
                                <p class="text-[12px] font-black text-slate-900 leading-tight uppercase truncate">${p.name}</p>
                            </div>

                            <!-- Col 2: Barcode -->
                            <div class="w-[15%]">
                                <p class="text-[10px] font-mono text-blue-500 font-black tracking-tight">${p.barcode}</p>
                            </div>

                            <!-- Col 3: Type -->
                            <div class="w-[10%] text-center">
                                ${typeBadge}
                            </div>

                            <!-- Col 4: Qty -->
                            <div class="w-[8%] text-center">
                                <span class="text-[11px] font-black text-slate-700">${b.current_qty}</span>
                                <span class="text-[8px] text-slate-400 font-bold uppercase">${isOil ? (p.oil_type === 'loose' ? 'L' : 'C') : 'P'}</span>
                            </div>

                            <!-- Col 5: Buying -->
                            <div class="w-[15%] text-right pr-2">
                                <p class="text-[11px] font-mono font-bold text-slate-400 italic">${numberFormat(b.buying_price)}</p>
                            </div>

                            <!-- Col 6: Labeled -->
                            <div class="w-[15%] text-right pr-2">
                                <p class="text-[11px] font-mono font-bold text-slate-600">${numberFormat(b.selling_price)}</p>
                            </div>

                            <!-- Col 7: Estimated -->
                            <div class="w-[15%] text-right pr-2">
                                <p class="text-[12px] font-mono font-black text-blue-600">${numberFormat(b.estimated_selling_price)}</p>
                            </div>
                        `;
                        div.onclick = () => {
                            selectedProduct = p;
                            openEntryModal(b);
                        };
                        list.appendChild(div);
                    });
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
                    <div class="rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                        <table class="w-full text-left">
                            <thead class="blue-gradient-header">
                                <tr class="text-[10px] uppercase tracking-[0.1em]">
                                    <th class="px-5 py-4">Batch Details</th>
                                    <th class="px-5 py-4 text-center">Retail Price</th>
                                    <th class="px-5 py-4 text-center">Est. Sale</th>
                                    <th class="px-5 py-4 text-right">Available</th>
                                </tr>
                            </thead>
                            <tbody id="batchTableBody" class="divide-y divide-slate-100 bg-white"></tbody>
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
                    tr.className = 'hover:bg-blue-50/50 cursor-pointer transition-all group active:bg-blue-100';
                    tr.innerHTML = `
                        <td class="px-5 py-4">
                            <span class="bg-slate-100 group-hover:bg-blue-100 text-slate-600 group-hover:text-blue-700 px-2 py-0.5 rounded text-[10px] font-black uppercase transition-colors">Batch: ID-${b.id}</span>
                            <p class="text-[10px] font-bold text-slate-400 mt-1.5 flex items-center gap-1.5 opacity-70">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Expiry: ${b.expire_date || 'N/A'}
                            </p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <p class="text-xs font-bold text-slate-400 line-through">${numberFormat(b.selling_price)}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <p class="text-base font-black text-blue-600">${numberFormat(b.estimated_selling_price)}</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <p class="text-lg font-black text-slate-800 tabular-nums">${actualQty} <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">${unit}</span></p>
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
            document.getElementById('entry_labeled_price').value = b.selling_price;
            document.getElementById('entry_base_price').value = b.estimated_selling_price;
            document.getElementById('entry_price').value = b.estimated_selling_price;
            document.getElementById('entry_discount').value = "0.00";
            document.getElementById('entry_unit').innerText = selectedProduct.oil_type === 'loose' ? 'Liters' : (selectedProduct.oil_type === 'can' ? 'Cans' : 'Units');
            document.getElementById('entry_max').innerText = `Max available: ${b.current_qty}`;
            updateEntryTotal();
            document.getElementById('entryModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('entry_qty').focus(), 100);
        }

        function syncFromPrice() {
            const base = parseFloat(document.getElementById('entry_base_price').value) || 0;
            const selling = parseFloat(document.getElementById('entry_price').value) || 0;
            document.getElementById('entry_discount').value = (base - selling).toFixed(2);
            updateEntryTotal();
        }

        function syncFromDiscount() {
            const base = parseFloat(document.getElementById('entry_base_price').value) || 0;
            const discount = parseFloat(document.getElementById('entry_discount').value) || 0;
            document.getElementById('entry_price').value = (base - discount).toFixed(2);
            updateEntryTotal();
        }

        function updateEntryTotal() {
            const qty = parseFloat(document.getElementById('entry_qty').value) || 0;
            const price = parseFloat(document.getElementById('entry_price').value) || 0;
            const total = qty * price;
            document.getElementById('entry_total').innerText = 'Rs. ' + numberFormat(total);
        }

        function addToCart() {
            if(!selectedCustomer) { Swal.fire('Required', 'Please select a customer first.', 'warning'); document.getElementById('entryModal').classList.add('hidden'); return; }
            
            const qty = parseFloat(document.getElementById('entry_qty').value);
            if(qty > selectedBatch.current_qty) { Swal.fire('Over Stock', 'Quantity exceeds available stock.', 'error'); return; }
            if(qty <= 0) return;

            const base_price = parseFloat(document.getElementById('entry_base_price').value);
            const actual_price = parseFloat(document.getElementById('entry_price').value);
            const per_item_discount = parseFloat(document.getElementById('entry_discount').value) || 0;
            
            const total_price = qty * actual_price;
            const total_discount = qty * per_item_discount;

            const existingIndex = cart.findIndex(item => item.batch_id === selectedBatch.id);
            if(existingIndex > -1) {
                cart[existingIndex].qty += qty;
                cart[existingIndex].total_discount = cart[existingIndex].qty * cart[existingIndex].per_item_discount;
                cart[existingIndex].total_price = (cart[existingIndex].qty * cart[existingIndex].unit_price) - cart[existingIndex].total_discount;
            } else {
                cart.push({
                    product_id: selectedProduct.id,
                    batch_id: selectedBatch.id,
                    name: selectedProduct.name,
                    brand: selectedProduct.brand,
                    category: selectedProduct.type === 'oil' ? 'Oil' : 'Spare parts',
                    oil_type: selectedProduct.type === 'oil' ? selectedProduct.oil_type : 'Unit',
                    qty,
                    unit_price: base_price, 
                    per_item_discount: per_item_discount,
                    total_discount: total_discount,
                    total_price: total_price
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
                total_discount += item.total_discount;
                
                const netPrice = item.unit_price - item.per_item_discount;
                const typeLabel = item.oil_type.charAt(0).toUpperCase() + item.oil_type.slice(1);

                // Category Icon
                const catIcon = item.category === 'Oil' 
                    ? `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C12 2 6 9 6 14a6 6 0 0 0 12 0c0-5-6-12-6-12z"/></svg>`
                    : `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>`;
                
                // Type Icon
                let typeIcon = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 11v10l8 4"/></svg>`; // Default box
                if(item.oil_type === 'loose') typeIcon = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 21c-4.418 0-8-3.582-8-8s8-11 8-11 8 6.582 8 11-3.582 8-8 8z"/></svg>`;
                if(item.oil_type === 'can') typeIcon = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>`;

                const div = document.createElement('div');
                div.className = 'grid grid-cols-1 lg:grid-cols-12 items-center px-5 lg:px-6 py-5 lg:py-3.5 hover:bg-blue-50/20 transition-all gap-5 lg:gap-2 group border-b border-slate-100/50 animate-fade-in';
                div.innerHTML = `
                    <!-- Product Info -->
                    <div class="col-span-1 lg:col-span-3">
                        <div class="flex flex-col">
                            <p class="font-black text-slate-900 text-[15px] lg:text-[14px] leading-tight mb-1.5 uppercase tracking-tight">${item.name}</p>
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] opacity-80">${item.brand}</span>
                                <span class="lg:hidden flex items-center gap-1.5 text-[8px] font-black px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-500 uppercase">
                                    ${item.category}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Category / Type (Hidden on Mobile) -->
                    <div class="hidden lg:flex col-span-2 items-center justify-center gap-3">
                        <span class="flex items-center gap-1.5 text-[10px] font-black px-3 py-1.5 rounded-xl border ${item.category === 'Oil' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'} uppercase">
                            ${catIcon} ${item.category}
                        </span>
                        <span class="font-black text-slate-400 text-[10px] uppercase tracking-widest">${typeLabel}</span>
                    </div>

                    <!-- Net Price -->
                    <div class="col-span-1 lg:col-span-2 text-left lg:text-right flex items-center justify-between lg:justify-end">
                        <span class="lg:hidden text-[9px] font-black text-slate-400 uppercase tracking-widest">Unit Price</span>
                        <p class="text-[15px] lg:text-[16px] font-black text-slate-900 tracking-tighter">Rs. ${numberFormat(netPrice)}</p>
                    </div>

                    <!-- Quantity (Large Touch Target) -->
                    <div class="col-span-1 lg:col-span-2 flex justify-center">
                        <div class="flex items-center justify-center gap-4 lg:gap-3 bg-slate-50/50 p-1.5 lg:p-0 rounded-2xl lg:bg-transparent">
                            <button onclick="updateQty(${index}, -1)" class="w-11 h-11 lg:w-9 lg:h-9 flex items-center justify-center bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-red-500 hover:text-white hover:border-red-500 transition-all shadow-sm active:scale-90">
                                <svg class="w-5 h-5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                            </button>
                            <span class="font-black text-slate-900 w-10 lg:w-9 text-2xl lg:text-lg text-center tabular-nums">${item.qty}</span>
                            <button onclick="updateQty(${index}, 1)" class="w-11 h-11 lg:w-9 lg:h-9 flex items-center justify-center bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm active:scale-90">
                                <svg class="w-5 h-5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Line Total -->
                    <div class="col-span-1 lg:col-span-2 text-left lg:text-right flex items-center justify-between lg:justify-end">
                        <div class="lg:hidden flex flex-col">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Saved Info</span>
                            <p class="text-[11px] font-black text-red-500 uppercase tracking-tighter italic">Off Rs.${numberFormat(item.total_discount)}</p>
                        </div>
                        <p class="text-[20px] lg:text-[19px] font-black text-blue-800 tracking-tighter leading-none">Rs. ${numberFormat(item.total_price)}</p>
                    </div>

                    <!-- Remove Action -->
                    <div class="col-span-1 lg:col-span-1 flex justify-end lg:justify-center">
                        <button onclick="removeFromCart(${index})" class="w-12 h-12 lg:w-10 lg:h-10 flex items-center justify-center text-red-400 hover:text-white hover:bg-red-500 rounded-2xl transition-all shadow-sm border border-slate-100 lg:border-transparent hover:shadow-red-500/20 active:scale-90">
                             <svg class="w-6 h-6 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                body.appendChild(div);
            });

            document.getElementById('item_count_label').innerText = `${cart.length} ${cart.length === 1 ? 'Item' : 'Items'} in Cart`;
            updateSummary(subtotal, total_discount);
        }

        function updateQty(index, delta) {
            const item = cart[index];
            const newQty = parseFloat(item.qty) + delta;
            
            if(newQty <= 0) {
                removeFromCart(index);
                return;
            }

            item.qty = newQty;
            item.total_discount = item.qty * item.per_item_discount;
            item.total_price = (item.qty * item.unit_price) - item.total_discount;
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

        function closePaymentModal() { document.getElementById('paymentModal').classList.add('hidden'); }

        async function checkout() {
            if(cart.length === 0) return;
            
            if(!selectedCustomer) {
                isCheckingOut = true;
                Swal.fire({
                    title: 'Customer Details Required',
                    text: "You must select or register a customer before completing this sale.",
                    icon: 'warning',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Register New',
                    denyButtonText: 'Search/Select',
                    cancelButtonText: 'Not Now',
                    confirmButtonColor: '#2563eb',
                    denyButtonColor: '#475569',
                    background: '#ffffff',
                    color: '#1e293b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        openNewCustomerModal();
                    } else if (result.isDenied) {
                        document.getElementById('custSearch').focus();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        isCheckingOut = false;
                    }
                });
                return;
            }

            isCheckingOut = false;
            const sub = cart.reduce((a, b) => a + (b.qty * b.unit_price), 0);
            const itemDisc = cart.reduce((a, b) => a + b.total_discount, 0); 

            document.getElementById('pay_modal_total').innerText = 'Rs. ' + numberFormat(sub);
            document.getElementById('pay_modal_item_disc').innerText = 'Rs. ' + numberFormat(itemDisc);
            document.getElementById('wholesale_discount').value = 0;
            updatePaymentTotal();
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function updatePaymentTotal() {
            const subTotal = cart.reduce((a, b) => a + (b.qty * b.unit_price), 0);
            const itemDiscount = cart.reduce((a, b) => a + b.total_discount, 0);
            const wDiscount = parseFloat(document.getElementById('wholesale_discount').value) || 0;
            const final = (subTotal - itemDiscount) - wDiscount;
            document.getElementById('final_pay_modal_total').innerText = 'Rs. ' + numberFormat(final);
        }

        function applyPercentageDiscount(pct) {
            const subTotal = cart.reduce((a, b) => a + (b.qty * b.unit_price), 0);
            const itemDiscount = cart.reduce((a, b) => a + b.total_discount, 0);
            const netBeforeWholesale = subTotal - itemDiscount;
            const discAmount = netBeforeWholesale * (pct / 100);
            document.getElementById('wholesale_discount').value = discAmount.toFixed(2);
            updatePaymentTotal();
        }

        async function processPayment(payMethod) {
            const sub = cart.reduce((a, b) => a + (b.qty * b.unit_price), 0);
            const itemDiscount = cart.reduce((a, b) => a + b.total_discount, 0);
            const wDiscount = parseFloat(document.getElementById('wholesale_discount').value) || 0;
            const final = (sub - itemDiscount) - wDiscount;

            const fd = new FormData();
            fd.append('customer_id', selectedCustomer.id);
            fd.append('total_amount', sub);
            fd.append('discount', itemDiscount + wDiscount); // Total discount = Item-wise + Wholesale
            fd.append('final_amount', final);
            fd.append('payment_method', payMethod);
            fd.append('items', JSON.stringify(cart.map(i => ({
                product_id: i.product_id,
                batch_id: i.batch_id,
                qty: i.qty,
                unit_price: i.unit_price,
                discount: i.total_discount,
                total_price: i.total_price
            }))));

            const data = await postAPI('submit_sale', fd);
            if(data.success) {
                closePaymentModal();
                printReceipt(data.sale_id, payMethod);
                
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
                
                // Clear search inputs and results after sale
                document.getElementById('prodSearch').value = '';
                document.getElementById('prodList').innerHTML = '';
                document.getElementById('prodList').classList.add('hidden');
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }

        function printReceipt(saleId, payMethod) {
            const now = new Date();
            document.getElementById('bill_date').innerText = now.toLocaleDateString();
            document.getElementById('bill_time').innerText = now.toLocaleTimeString();
            document.getElementById('bill_id').innerText = 'SALE-' + saleId;
            document.getElementById('bill_cust').innerText = selectedCustomer.name;
            document.getElementById('bill_phone').innerText = selectedCustomer.contact;
            document.getElementById('bill_pay').innerText = payMethod.toUpperCase();
            
            const list = document.getElementById('bill_items');
            list.innerHTML = '';
            let sub = 0; let disc = 0;
            cart.forEach(item => {
                sub += (item.qty * item.unit_price);
                disc += item.total_discount;
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
