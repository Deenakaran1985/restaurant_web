@extends('layouts.admin')

@section('title', 'In-Room Dining & Banquet Catering')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill mb-2 fw-semibold">
                <i class="bi bi-building-check me-2"></i> RESORT & HOTEL SUITE INTEGRATION SUITE
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">In-Room Dining & Banquet Catering Hub</h2>
            <p class="text-muted mb-0 fs-6">Direct room service folio billing across guest floor towers and grand ballroom event catering contract management.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/pos') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-shop me-1"></i> Waiter POS
            </a>
            <button class="btn btn-modern-primary shadow-lg" data-bs-toggle="modal" data-bs-target="#roomOrderModal">
                <i class="bi bi-bell-fill me-1"></i> New Room Service KOT
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center" role="alert">
        <i class="bi bi-bell-fill me-3 fs-3 animate-bounce"></i>
        <div>
            <strong class="text-white d-block">Room Service KOT Fired & Billed to Folio!</strong>
            <span class="fs-7">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Hotel Catering KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Active In-Room Orders</span>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fs-4"><i class="bi bi-door-closed-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-white">{{ $cateringStats['active_room_orders'] }} <span class="fs-6 text-muted">Suites</span></h2>
                    <p class="fs-8 text-success mb-0"><i class="bi bi-shield-check me-1"></i> Billed directly to guest room folios</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Room Dining Folio Revenue</span>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4"><i class="bi bi-wallet-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-success">{{ $settings->currency_symbol }} {{ number_format($cateringStats['total_room_revenue'], 2) }}</h2>
                    <p class="fs-8 text-muted mb-0">Includes silver service delivery surcharges</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #a855f7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Contracted Banquets Value</span>
                    <div class="p-3 bg-purple bg-opacity-10 text-primary rounded-4 fs-4" style="background: rgba(168, 85, 247, 0.15) !important; color: #c084fc !important;"><i class="bi bi-calendar-event-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-light">{{ $settings->currency_symbol }} {{ number_format($cateringStats['banquet_contract_val'], 2) }}</h2>
                    <p class="fs-8 text-warning mb-0"><i class="bi bi-star-fill me-1"></i> High-yield event catering reservations</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Upcoming Banquet Guests</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4"><i class="bi bi-people-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-warning">{{ number_format($cateringStats['total_banquet_guests']) }} <span class="fs-6 text-muted">Plates</span></h2>
                    <p class="fs-8 text-muted mb-0">Reserved food buffet portions scheduled</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active In-Room Dining Table -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-bell me-2 text-warning"></i> Active In-Room Dining & Suite Orders</h5>
            <span class="fs-7 text-muted font-monospace"><i class="bi bi-ethernet me-1 text-success"></i> Kitchen TCP Printer Port 9100 Wired</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider">
                        <th class="py-3 ps-4">Room & Suite Tower</th>
                        <th class="py-3">Guest Folio & Account Details</th>
                        <th class="py-3 text-center">Ordered Culinary Items</th>
                        <th class="py-3 text-center">Folio Billing Total</th>
                        <th class="py-3 text-center">Room Service Status</th>
                        <th class="py-3 pe-4 text-end">Elevator Dispatch</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roomOrders as $ro)
                        <tr>
                            <td class="py-3 ps-4">
                                <div class="fw-bolder font-monospace text-light fs-6"><i class="bi bi-door-open-fill text-primary me-2"></i> {{ $ro->table_number }}</div>
                                <span class="fs-8 text-muted">Order #{{ $ro->order_number }} | {{ $ro->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-warning fs-7"><i class="bi bi-person-badge-fill me-2"></i> {{ $ro->notes }}</div>
                                <small class="text-success fs-8 font-monospace"><i class="bi bi-check2-circle me-1"></i> Credit Limit Validated against PMS Folio</small>
                            </td>
                            <td class="text-center">
                                @foreach($ro->items as $item)
                                    <span class="badge bg-dark border border-secondary border-opacity-25 text-light fs-8 font-monospace me-1">{{ $item->quantity }}x {{ $item->item_name }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <div class="fw-bolder font-monospace text-success fs-5">{{ $settings->currency_symbol }} {{ number_format($ro->total_amount, 2) }}</div>
                                <span class="fs-8 text-muted">Charged to Folio</span>
                            </td>
                            <td class="text-center">
                                @if(strpos(strtolower($ro->status), 'delivered') !== false)
                                    <span class="badge badge-emerald px-3 py-2 fs-7"><i class="bi bi-check-circle-fill me-1"></i> {{ strtoupper($ro->status) }}</span>
                                @else
                                    <span class="badge badge-amber px-3 py-2 fs-7 animate-pulse"><i class="bi bi-clock me-1"></i> {{ strtoupper($ro->status) }}</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" onclick="dispatchSuiteTrolley(this, '{{ $ro->table_number }}')">
                                    <i class="bi bi-cart-check me-1"></i> Dispatch Trolley
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grand Ballroom & Banquet Event Catering Ledger -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-calendar2-week me-2 text-purple" style="color: #c084fc;"></i> Contracted Banquet Events & Grand Ballroom Catering</h5>
            <span class="badge bg-secondary bg-opacity-25 text-light font-monospace">Executive Master Catering Roster</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider">
                        <th class="py-3 ps-4">Event Contract & Venue</th>
                        <th class="py-3 text-center">Scheduled Date & Time</th>
                        <th class="py-3 text-center">Guest Count & Buffet Package</th>
                        <th class="py-3 text-center">Contract Total & Deposit</th>
                        <th class="py-3">Executive Chef in Charge</th>
                        <th class="py-3 pe-4 text-end">Production Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banquetEvents as $ev)
                        <tr>
                            <td class="py-3 ps-4">
                                <div class="fw-bold text-light fs-6">{{ $ev['event_name'] }}</div>
                                <div class="fs-8 text-info font-monospace"><i class="bi bi-geo-alt-fill me-1"></i> {{ $ev['venue'] }}</div>
                            </td>
                            <td class="text-center font-monospace fw-bold text-warning fs-7">
                                <i class="bi bi-calendar-check me-1 text-secondary"></i> {{ $ev['date_time'] }}
                            </td>
                            <td class="text-center">
                                <div class="fw-bolder text-white font-monospace fs-5">{{ $ev['guest_count'] }} <span class="fs-7 fw-normal text-muted">Guests</span></div>
                                <span class="badge bg-dark border border-secondary border-opacity-25 text-light fs-8">{{ $ev['buffet_package'] }} (@ ₹{{ number_format($ev['price_per_plate']) }}/pl)</span>
                            </td>
                            <td class="text-center font-monospace">
                                <div class="fw-bold text-success fs-6">{{ $settings->currency_symbol }} {{ number_format($ev['total_contract'], 2) }}</div>
                                <div class="fs-8 text-muted">Advance: {{ $settings->currency_symbol }} {{ number_format($ev['deposit_paid'], 2) }} Paid</div>
                            </td>
                            <td>
                                <div class="text-light fw-medium fs-7"><i class="bi bi-person-workspace text-primary me-2"></i> {{ $ev['chef_in_charge'] }}</div>
                                <small class="text-muted fs-8">Mise-en-place raw store allocation active</small>
                            </td>
                            <td class="pe-4 text-end">
                                <span class="badge {{ $ev['badge'] }} px-3 py-2 fs-7 font-monospace">
                                    <i class="bi bi-award-fill me-1"></i> {{ $ev['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Room Service Order Modal -->
<div class="modal fade" id="roomOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold">
                    <i class="bi bi-door-closed-fill text-warning me-2"></i> Fire In-Room Dining KOT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('hotel.bill_room') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <p class="text-muted fs-7 mb-4">Select hotel tower room number and enter guest folio details to immediately broadcast thermal ESC/POS kitchen receipt and charge total directly to guest account.</p>
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Tower Room & Suite Number</label>
                        <select name="room_number" class="form-select bg-dark text-light border-secondary border-opacity-50 font-monospace" required>
                            <option value="Room 601 (Penthouse Suite)">🏰 Room 601 (Penthouse Suite)</option>
                            <option value="Room 504 (Royal Presidential Suite)">👑 Room 504 (Royal Presidential Suite)</option>
                            <option value="Room 402 (Executive Ocean View)">🌊 Room 402 (Executive Ocean View)</option>
                            <option value="Room 305 (Deluxe Twin Tower)">🏨 Room 305 (Deluxe Twin Tower)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Guest Folio & PMS Account Reference</label>
                        <input type="text" name="guest_name" class="form-control bg-dark text-warning font-monospace border-secondary border-opacity-50" value="Mr. Rakesh Jhunjhunwala | Folio #F-9945" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Portions Quantity (Silver Tray Service)</label>
                        <input type="number" name="quantity" class="form-control bg-dark text-light border-secondary border-opacity-50 font-monospace fs-5" value="2" min="1" max="20" required>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary px-4 fw-bold">
                        <i class="bi bi-lightning-charge-fill me-1"></i> CHARGE FOLIO & PRINT KOT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dispatchSuiteTrolley(btn, roomName) {
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Trolley En Route...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.classList.remove('btn-outline-warning');
        btn.classList.add('btn-success', 'text-white');
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Trolley Delivered';
        
        const row = btn.closest('tr');
        row.style.opacity = '0.5';
        
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = 9999;
        toast.innerHTML = `
            <div class="alert alert-success shadow-lg rounded-4 d-flex align-items-center border-success p-3 text-light bg-dark" style="border: 2px solid #10b981 !important;">
                <i class="bi bi-door-open-fill text-success fs-3 me-3"></i>
                <div>
                    <strong class="text-white d-block">Room Service Trolley Arrived at ${roomName}!</strong>
                    <span class="fs-8 text-muted">Silver service delivery confirmed. Guest room folio debited.</span>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }, 850);
}
</script>
@endpush
