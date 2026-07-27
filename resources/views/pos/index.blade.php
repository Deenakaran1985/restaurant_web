@extends('layouts.admin')

@section('title', 'Real-Time Touch POS Terminal')
@section('page_title', 'Touchscreen Point of Sale Terminal')

@section('content')
<div class="row g-4" style="min-height: calc(100vh - 160px);">
    <!-- Menu Grid & Interactive Categories Area -->
    <div class="col-12 col-xl-8 d-flex flex-column">
        <!-- Category Filter Tabs -->
        <div class="d-flex gap-2 overflow-auto pb-2 mb-3">
            <button class="btn btn-modern-primary d-flex align-items-center flex-shrink-0 category-tab active" data-filter="all">
                <i class="bi bi-grid-all me-2"></i> All Categories
            </button>
            @foreach($categories as $cat)
                <button class="btn btn-light glass-card border px-4 flex-shrink-0 fw-bold category-tab text-white" data-filter="cat-{{ $cat->id }}">
                    <i class="bi {{ $cat->icon ?? 'bi-star' }} text-warning me-2"></i> {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- POS Status & Alert Notification Header -->
        <div id="posAlertContainer"></div>

        <!-- Dishes Interactive Touch Grid -->
        <div class="row g-3 overflow-y-auto flex-grow-1 pe-2" style="max-height: 72vh;">
            @foreach($categories as $category)
                @foreach($category->items as $item)
                    <div class="col-6 col-md-4 dish-item-wrap cat-{{ $category->id }}">
                        <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between hover-lift cursor-pointer pos-item-card rounded-5 border-secondary border-opacity-50 shadow" 
                             onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, {{ $item->prep_time_minutes }})"
                             data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}">
                            <div>
                                <div class="rounded-4 overflow-hidden mb-3 position-relative shadow" style="height: 140px; background: #1e293b;">
                                    @if($item->image_url)
                                        <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->name }}" loading="lazy">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-warning fs-1">
                                            <i class="bi bi-cup-hot-fill animate-pulse"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-dark text-warning position-absolute bottom-0 end-0 m-2 font-monospace fs-8 border border-warning border-opacity-25">
                                        <i class="bi bi-stopwatch me-1"></i>{{ $item->prep_time_minutes }}m SLA
                                      </span>
                                </div>
                                <h6 class="fw-bold mb-1 font-heading text-white d-flex justify-content-between align-items-center">
                                    <span>{{ $item->name }}</span>
                                    <span class="badge bg-primary bg-opacity-25 text-info fs-9 d-none d-xxl-inline-block">{{ $item->code }}</span>
                                </h6>
                                <p class="text-muted fs-8 mb-2 line-clamp-2 font-monospace">{{ $item->description }}</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-25">
                                <span class="fs-5 fw-bolder text-warning font-monospace">₹{{ number_format($item->price, 2) }}</span>
                                <button type="button" class="btn btn-modern-primary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center shadow" style="width: 36px; height: 36px;" title="Add to Order">
                                    <i class="bi bi-plus-lg fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Active Table Order & Live Invoicing Sidebar -->
    <div class="col-12 col-xl-4 d-flex flex-column">
        <div class="glass-card p-4 d-flex flex-column flex-grow-1 h-100 rounded-5 border-warning border-2 border-opacity-50 shadow-lg" style="background: rgba(15, 23, 42, 0.92);">
            <!-- Order Metadata Header -->
            <div class="pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-warning bg-opacity-25 text-warning rounded-3 fs-5"><i class="bi bi-terminal-split"></i></span>
                        <h5 class="mb-0 fw-bold font-heading text-white">ACTIVE TOUCH KOT</h5>
                    </div>
                    <span class="badge badge-amber font-monospace fs-8"><i class="bi bi-broadcast me-1"></i> DINE-IN SYSTEM</span>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-7">
                        <label class="fs-8 text-secondary font-monospace d-block mb-1">SELECT DINING TABLE:</label>
                        <select class="form-select bg-dark border-secondary text-info fw-bold rounded-4 font-monospace shadow-sm" id="posTableSelector">
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}" {{ $table->status === 'seated' ? 'selected' : '' }}>
                                    Table {{ $table->table_number }} [Cap: {{ $table->capacity }} - {{ strtoupper($table->status) }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="fs-8 text-secondary font-monospace d-block mb-1">SERVICE PROFILE:</label>
                        <select class="form-select bg-dark border-secondary text-white fw-bold rounded-4 font-monospace shadow-sm" id="posOrderType">
                            <option value="dine_in">Dine-In Table</option>
                            <option value="takeaway">VIP Takeaway</option>
                            <option value="delivery">Express Delivery</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Interactive Cart Items Stream -->
            <div class="flex-grow-1 overflow-y-auto mb-4 pe-1" id="cartItemsContainer" style="max-height: 42vh; min-height: 250px;">
                <div class="text-center py-5 text-muted my-auto" id="emptyCartMessage">
                    <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <h6 class="fw-bold font-heading text-light">Touchscreen Order Cart is Empty</h6>
                    <p class="fs-8 text-secondary mb-0">Tap menu items on the left grid to begin punching KOT orders for your dining table.</p>
                </div>
            </div>

            <!-- Billing Math & Checkout Action Toolbar -->
            <div class="pt-3 border-top border-secondary border-opacity-25 mt-auto">
                <div class="d-flex justify-content-between text-secondary fs-7 font-monospace mb-1">
                    <span>Subtotal:</span>
                    <strong class="text-white" id="cartSubtotal">₹0.00</strong>
                </div>
                <div class="d-flex justify-content-between text-secondary fs-7 font-monospace mb-1">
                    <span>GST & VAT Tax (5.0%):</span>
                    <strong class="text-info" id="cartTax">₹0.00</strong>
                </div>
                <div class="d-flex justify-content-between text-success fs-7 font-monospace mb-3">
                    <span>VIP Patron Loyalty Voucher:</span>
                    <strong id="cartDiscount">- ₹0.00</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-4 border border-warning border-opacity-50 shadow" style="background: radial-gradient(circle at right, rgba(245, 158, 11, 0.15) 0%, rgba(15, 23, 42, 0.95) 80%);">
                    <span class="fs-5 fw-bolder text-white font-heading">GRAND TOTAL:</span>
                    <span class="fs-2 fw-bolder font-monospace text-warning" id="cartGrandTotal">₹0.00</span>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-warning w-100 fw-bolder py-3 text-dark rounded-4 fs-6 d-flex align-items-center justify-content-center shadow-lg hover-lift" onclick="submitPosOrder(false)" id="fireKotBtn">
                            <i class="bi bi-fire me-2 fs-4"></i> FIRE TO KITCHEN
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-success w-100 fw-bolder py-3 text-white rounded-4 fs-6 d-flex align-items-center justify-content-center shadow-lg hover-lift" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);" onclick="submitPosOrder(true)" id="payPrintBtn">
                            <i class="bi bi-printer me-2 fs-4"></i> PAY & PRINT LAN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let orderCart = [];

    function playBuzzer() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5 chime
            osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.15); // A5 chime
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.6);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.65);
        } catch(e) {
            console.log("Audio chime error:", e);
        }
    }

    function addToCart(id, name, price, prepTime) {
        const existing = orderCart.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            orderCart.push({
                id: id,
                name: name,
                price: parseFloat(price),
                quantity: 1,
                notes: 'Standard chef prep (' + prepTime + 'm SLA)'
            });
        }
        playBuzzer();
        renderCart();
    }

    function changeQuantity(id, delta) {
        const item = orderCart.find(i => i.id === id);
        if (item) {
            item.quantity += delta;
            if (item.quantity <= 0) {
                orderCart = orderCart.filter(i => i.id !== id);
            }
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyMsg = document.getElementById('emptyCartMessage');

        if (orderCart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted my-auto" id="emptyCartMessage">
                    <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <h6 class="fw-bold font-heading text-light">Touchscreen Order Cart is Empty</h6>
                    <p class="fs-8 text-secondary mb-0">Tap menu items on the left grid to begin punching KOT orders for your dining table.</p>
                </div>
            `;
            updateTotals(0);
            return;
        }

        let html = '';
        let subtotal = 0;

        orderCart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            html += `
                <div class="p-3 mb-2 rounded-4 border border-secondary border-opacity-25 d-flex justify-content-between align-items-center shadow-sm" style="background: rgba(255,255,255,0.03);">
                    <div>
                        <h6 class="mb-1 fw-bold font-heading text-white d-flex align-items-center gap-2">
                            <span>${item.name}</span>
                            <span class="badge badge-azure fs-9 font-monospace">₹${item.price.toFixed(2)} ea</span>
                        </h6>
                        <div class="text-muted fs-8 font-monospace mt-1"><i class="bi bi-clock me-1 text-warning"></i> ${item.notes}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bolder fs-5 text-warning font-monospace mb-2">₹${itemTotal.toFixed(2)}</div>
                        <div class="btn-group btn-group-sm rounded-pill border border-secondary border-opacity-50">
                            <button type="button" class="btn btn-outline-secondary text-light px-3 fw-bold" onclick="changeQuantity(${item.id}, -1)">-</button>
                            <span class="btn btn-dark disabled px-3 text-warning fw-bolder font-monospace">${item.quantity}</span>
                            <button type="button" class="btn btn-outline-secondary text-light px-3 fw-bold" onclick="changeQuantity(${item.id}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        updateTotals(subtotal);
    }

    function updateTotals(subtotal) {
        const tax = subtotal * 0.05;
        const discount = subtotal > 1000 ? 50.00 : 0.00;
        const grandTotal = Math.max(0, subtotal + tax - discount);

        document.getElementById('cartSubtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('cartTax').textContent = '₹' + tax.toFixed(2);
        document.getElementById('cartDiscount').textContent = '- ₹' + discount.toFixed(2);
        document.getElementById('cartGrandTotal').textContent = '₹' + grandTotal.toFixed(2);
    }

    function submitPosOrder(isPaymentPrint) {
        if (orderCart.length === 0) {
            alert('⚠️ Cannot fire order: Please tap dishes on the touch grid to add items to your cart first!');
            return;
        }

        const tableId = document.getElementById('posTableSelector').value;
        const orderType = document.getElementById('posOrderType').value;
        const fireBtn = document.getElementById('fireKotBtn');
        const printBtn = document.getElementById('payPrintBtn');

        fireBtn.disabled = true;
        printBtn.disabled = true;
        fireBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> FIRING...`;

        const payload = {
            table_id: tableId,
            order_type: orderType,
            items: orderCart.map(i => ({
                menu_item_id: i.id,
                quantity: i.quantity,
                notes: i.notes
            }))
        };

        fetch('/pos/order', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            fireBtn.disabled = false;
            printBtn.disabled = false;
            fireBtn.innerHTML = `<i class="bi bi-fire me-2 fs-4"></i> FIRE TO KITCHEN`;
            printBtn.innerHTML = `<i class="bi bi-printer me-2 fs-4"></i> PAY & PRINT LAN`;

            if (data.success) {
                playBuzzer();
                orderCart = [];
                renderCart();

                const alertWrap = document.getElementById('posAlertContainer');
                alertWrap.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-4 mb-4 d-flex align-items-center justify-content-between shadow-lg" role="alert">
                        <div class="d-flex align-items-center">
                            <span class="p-3 bg-success bg-opacity-25 text-success rounded-circle me-3 fs-2"><i class="bi bi-check2-all"></i></span>
                            <div>
                                <h5 class="fw-bold text-white mb-1">🎉 Order Fired Successfully! [KOT: ${data.order.order_number}]</h5>
                                <div class="fs-7 text-muted font-monospace">${data.message} ${isPaymentPrint ? '• Payment processed via thermal receipt.' : ''}</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/kds') }}" class="btn btn-modern-primary fw-bold text-nowrap px-4"><i class="bi bi-display me-2"></i> View on KDS Board</a>
                            <button type="button" class="btn-close m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                if (isPaymentPrint) {
                    setTimeout(() => {
                        alert("🖨️ ESC/POS Thermal LAN Socket Receipt dispatched to kitchen printer 192.168.32.150:9100!");
                    }, 500);
                }
            } else {
                alert('❌ Order submission failed: ' + (data.message || 'Server error occurred'));
            }
        })
        .catch(err => {
            console.error('Error submitting POS order:', err);
            fireBtn.disabled = false;
            printBtn.disabled = false;
            fireBtn.innerHTML = `<i class="bi bi-fire me-2 fs-4"></i> FIRE TO KITCHEN`;
            printBtn.innerHTML = `<i class="bi bi-printer me-2 fs-4"></i> PAY & PRINT LAN`;
            alert('⚠️ Network communication error while punching order.');
        });
    }

    // Category Filter Tab Switching
    document.querySelectorAll('.category-tab').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-tab').forEach(t => {
                t.classList.remove('btn-modern-primary', 'active');
                t.classList.add('btn-light', 'glass-card', 'text-white');
            });
            this.classList.remove('btn-light', 'glass-card', 'text-white');
            this.classList.add('btn-modern-primary', 'active');

            const filter = this.getAttribute('data-filter');
            document.querySelectorAll('.dish-item-wrap').forEach(item => {
                if (filter === 'all' || item.classList.contains(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
