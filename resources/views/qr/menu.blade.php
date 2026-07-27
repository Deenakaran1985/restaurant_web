<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->hotel_name }} - Guest Table Side Menu</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font-bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --accent-amber: #f59e0b;
            --bg-dark: #090b10;
            --surface-glass: rgba(255, 255, 255, 0.05);
            --border-glass: rgba(255, 255, 255, 0.12);
        }
        body {
            background-color: var(--bg-dark);
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            padding-bottom: 110px;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .hero-banner {
            background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.2) 0%, transparent 75%), #0f131f;
            border-bottom: 1px solid var(--border-glass);
            padding: 2.5rem 1.5rem 1.5rem;
            text-align: center;
            border-radius: 0 0 32px 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .badge-table {
            background: rgba(245, 158, 11, 0.15);
            color: var(--accent-amber);
            border: 1px solid rgba(245, 158, 11, 0.3);
            font-size: 0.85rem;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .category-nav {
            overflow-x: auto;
            white-space: nowrap;
            padding: 1rem 0;
            scrollbar-width: none;
        }
        .category-nav::-webkit-scrollbar { display: none; }
        .pill-category {
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            color: #cbd5e1;
            padding: 0.6rem 1.3rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-right: 0.75rem;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .pill-category.active, .pill-category:hover {
            background: var(--primary-gradient);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }
        .dish-card {
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        .dish-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.4);
        }
        .dish-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .btn-qty {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: none;
            background: rgba(255,255,255,0.1);
            color: #fff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-qty:hover, .btn-qty:active {
            background: #6366f1;
            transform: scale(1.05);
        }
        .floating-cart {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 92%;
            max-width: 600px;
            background: var(--primary-gradient);
            border-radius: 24px;
            padding: 1rem 1.5rem;
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.5);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .floating-cart.hidden {
            transform: translate(-50%, 150%);
        }
        .modal-content-glass {
            background: #111827;
            border: 1px solid var(--border-glass);
            border-radius: 28px;
        }
    </style>
</head>
<body>

    <!-- Establishment Brand & Dining Assignment Hero -->
    <header class="hero-banner mb-4">
        <div class="badge-table mb-3">
            <i class="bi bi-geo-alt-fill me-1"></i> TABLE AT SEAT: T-{{ $table->table_number }} ({{ $table->section->name ?? 'Main Dining' }})
        </div>
        <h2 class="fw-bolder font-heading text-white mb-1">{{ $settings->hotel_name }}</h2>
        <p class="text-muted fs-7 mb-0">Browse executive culinary creations & beverages and place your order directly from your smartphone.</p>
    </header>

    <main class="container py-2" style="max-width: 900px;">
        <!-- Category Navigation Bar -->
        <nav class="category-nav mb-4 px-2">
            @foreach($categories as $index => $category)
                <a href="#cat-{{ $category->id }}" class="pill-category {{ $index === 0 ? 'active' : '' }}" onclick="selectCat(this)">
                    <i class="bi {{ $category->icon ?? 'bi-star-fill' }} me-2"></i> {{ $category->name }}
                </a>
            @endforeach
        </nav>

        <!-- Menu Categories & Food Items Grid -->
        @foreach($categories as $category)
            <div id="cat-{{ $category->id }}" class="mb-5 px-2">
                <div class="d-flex align-items-center mb-3">
                    <h4 class="fw-bold font-heading mb-0 text-white">{{ $category->name }}</h4>
                    <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill ms-2">{{ $category->items->count() }} ITEMS</span>
                </div>
                
                <div class="row g-4">
                    @foreach($category->items as $item)
                        <div class="col-md-6">
                            <div class="dish-card d-flex flex-column justify-content-between">
                                <div>
                                    <div class="position-relative">
                                        <img src="{{ $item->image_url ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80' }}" alt="{{ $item->name }}" class="dish-img">
                                        <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 text-warning rounded-pill px-3 py-2 border border-secondary border-opacity-25 fs-8">
                                            <i class="bi bi-clock-history me-1"></i> ~{{ $item->prep_time_minutes ?? 12 }}m Prep
                                        </span>
                                    </div>
                                    <div class="p-4">
                                        <div class="d-flex align-items-baseline justify-content-between mb-2">
                                            <h5 class="fw-bold font-heading mb-0 text-light">{{ $item->name }}</h5>
                                            <span class="fw-bolder fs-5 text-warning ms-2">{{ $settings->currency_symbol }} {{ number_format($item->price, 2) }}</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-0">{{ $item->description }}</p>
                                    </div>
                                </div>
                                <div class="px-4 pb-4 pt-2 d-flex align-items-center justify-content-between border-top border-secondary border-opacity-25 mt-2">
                                    <span class="fs-8 text-secondary fw-semibold">Tax Included (5%)</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn-qty" onclick="updateQty({{ $item->id }}, -1, '{{ addslashes($item->name) }}', {{ $item->price }})"><i class="bi bi-dash-lg"></i></button>
                                        <span id="qty-{{ $item->id }}" class="fw-bold font-monospace fs-5 px-2">0</span>
                                        <button class="btn-qty" onclick="updateQty({{ $item->id }}, 1, '{{ addslashes($item->name) }}', {{ $item->price }})"><i class="bi bi-plus-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </main>

    <!-- Floating Order Cart Bar -->
    <div id="floatingCart" class="floating-cart hidden" onclick="openCartModal()">
        <div class="d-flex align-items-center">
            <div class="bg-white text-dark fw-bolder rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 1.1rem;" id="cartCount">
                0
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-white fs-6">View Your Table Order</h6>
                <small class="text-white text-opacity-75 fs-8">Tap to verify items and dispatch to Chef</small>
            </div>
        </div>
        <div class="text-end">
            <span class="fs-5 fw-bolder text-white font-monospace" id="cartTotal">{{ $settings->currency_symbol }} 0.00</span>
            <i class="bi bi-arrow-right-circle-fill ms-2 fs-4 text-white"></i>
        </div>
    </div>

    <!-- Order Verification & Checkout Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-glass text-light p-3">
                <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                    <h5 class="modal-title font-heading fw-bold d-flex align-items-center" id="cartModalLabel">
                        <i class="bi bi-cart-check-fill text-warning me-2"></i> Table T-{{ $table->table_number }} Order Cart
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div id="cartItemsList" class="mb-4">
                        <!-- Dynamic items populate here -->
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fs-7 fw-bold text-muted">Optional Patron Name for Chef Callout</label>
                        <input type="text" id="guestName" class="form-control bg-dark text-light border-secondary border-opacity-25" placeholder="e.g. Rahul / Family Dining">
                    </div>

                    <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-7">Subtotal:</span>
                            <span class="fw-semibold font-monospace" id="modalSubtotal">{{ $settings->currency_symbol }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-7">GST & VAT Tax ({{ $settings->default_tax_rate }}%):</span>
                            <span class="fw-semibold font-monospace text-warning" id="modalTax">{{ $settings->currency_symbol }} 0.00</span>
                        </div>
                        <hr class="border-secondary border-opacity-25 my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Estimated Bill Total:</span>
                            <span class="fw-bolder fs-4 text-warning font-monospace" id="modalGrandTotal">{{ $settings->currency_symbol }} 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Add More Items</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" onclick="submitTableOrder()" style="background: var(--primary-gradient); border: none;">
                        <i class="bi bi-send-fill me-2"></i> FIRE TO KITCHEN CHEFS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Success Notification Dialog -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content modal-content-glass text-light p-4">
                <div class="modal-body py-5">
                    <div class="p-4 bg-success bg-opacity-10 text-success rounded-circle d-inline-block mb-4" style="width: 90px; height: 90px; font-size: 2.5rem;">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h3 class="fw-bold font-heading mb-2">Order Fired Successfully!</h3>
                    <p class="text-muted mb-4 fs-6">Your order has been routed directly to our kitchen team via 
                        <strong>{{ $settings->kds_routing_mode === 'thermal_printer_only' ? 'Direct Thermal Kitchen Receipt Slip (TCP 9100)' : 'Interactive Kitchen Display Monitor' }}</strong>.
                    </p>
                    <div class="p-3 rounded-4 bg-dark border border-secondary border-opacity-25 mb-4 text-start">
                        <div class="fs-7 text-muted mb-1"><i class="bi bi-table me-2 text-warning"></i> Table Assigned: <span class="text-white fw-bold">T-{{ $table->table_number }}</span></div>
                        <div class="fs-7 text-muted"><i class="bi bi-clock-history me-2 text-info"></i> Estimated Service Time: <span class="text-white fw-bold">12 - 15 Minutes</span></div>
                    </div>
                    <button class="btn btn-outline-light rounded-pill px-5 py-2 fw-bold" onclick="location.reload()">Order Additional Drinks or Dessert</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Vanilla JS Logic -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = {};
        const taxRate = {{ $settings->default_tax_rate }} / 100;
        const currency = "{{ $settings->currency_symbol }}";
        let cartModalObj, successModalObj;

        window.addEventListener('DOMContentLoaded', () => {
            cartModalObj = new bootstrap.Modal(document.getElementById('cartModal'));
            successModalObj = new bootstrap.Modal(document.getElementById('successModal'));
        });

        function selectCat(link) {
            document.querySelectorAll('.pill-category').forEach(p => p.classList.remove('active'));
            link.classList.add('active');
        }

        function updateQty(id, delta, name, price) {
            if (!cart[id]) {
                cart[id] = { menu_item_id: id, name: name, price: price, quantity: 0, notes: "" };
            }
            cart[id].quantity += delta;
            if (cart[id].quantity <= 0) {
                delete cart[id];
                document.getElementById('qty-' + id).innerText = "0";
            } else {
                document.getElementById('qty-' + id).innerText = cart[id].quantity;
            }
            renderCartUI();
        }

        function renderCartUI() {
            let totalQty = 0;
            let subtotal = 0;

            for (let id in cart) {
                totalQty += cart[id].quantity;
                subtotal += cart[id].price * cart[id].quantity;
            }

            const tax = subtotal * taxRate;
            const grandTotal = subtotal + tax;

            if (totalQty > 0) {
                document.getElementById('floatingCart').classList.remove('hidden');
                document.getElementById('cartCount').innerText = totalQty;
                document.getElementById('cartTotal').innerText = currency + " " + subtotal.toFixed(2);
            } else {
                document.getElementById('floatingCart').classList.add('hidden');
            }

            // Populate modal lists
            let itemsHtml = '';
            for (let id in cart) {
                let item = cart[id];
                itemsHtml += `
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                        <div>
                            <span class="fw-bold text-light">${item.quantity}x ${item.name}</span>
                        </div>
                        <span class="fw-semibold font-monospace text-warning">${currency} ${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                `;
            }
            document.getElementById('cartItemsList').innerHTML = itemsHtml || '<p class="text-muted text-center py-2">Your dining cart is empty.</p>';
            document.getElementById('modalSubtotal').innerText = currency + " " + subtotal.toFixed(2);
            document.getElementById('modalTax').innerText = currency + " " + tax.toFixed(2);
            document.getElementById('modalGrandTotal').innerText = currency + " " + grandTotal.toFixed(2);
        }

        function openCartModal() {
            cartModalObj.show();
        }

        function submitTableOrder() {
            const itemsArray = [];
            for (let id in cart) {
                itemsArray.push({
                    menu_item_id: cart[id].menu_item_id,
                    quantity: cart[id].quantity,
                    notes: "Guest table-side self order"
                });
            }

            if (itemsArray.length === 0) return;

            const guestName = document.getElementById('guestName').value;

            fetch("{{ route('qr.order', $table->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ items: itemsArray, guest_name: guestName })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cartModalObj.hide();
                    successModalObj.show();
                    cart = {};
                    renderCartUI();
                } else {
                    alert('Order could not be transmitted: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error while reaching dining server.');
            });
        }
    </script>
</body>
</html>
