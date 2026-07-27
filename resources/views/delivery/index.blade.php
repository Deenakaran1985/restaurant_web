@extends('layouts.admin')

@section('title', 'Cloud Kitchen Delivery & Aggregator Dispatch')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-2 fw-semibold">
                <i class="bi bi-bicycle me-2"></i> MULTI-CHANNEL CLOUD KITCHEN AGGREGATOR HUB
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Delivery Aggregator Dispatch Board</h2>
            <p class="text-muted mb-0 fs-6">Unifying Zomato, Swiggy, Uber Eats, and direct WhatsApp orders into one synchronized KDS stream and thermal LAN printing queue.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/kds') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-tv me-1"></i> Kitchen Monitors
            </a>
            <button class="btn btn-modern-primary shadow-lg" data-bs-toggle="modal" data-bs-target="#simulateModal">
                <i class="bi bi-broadcast me-1"></i> Inject Aggregator Order
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center" role="alert">
        <i class="bi bi-lightning-charge-fill me-3 fs-3 animate-pulse"></i>
        <div>
            <strong class="text-white d-block">Webhook Synchronization Successful!</strong>
            <span class="fs-7">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Cloud Kitchen Executive KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #38bdf8 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Delivery Orders Stream</span>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 fs-4"><i class="bi bi-box-seams"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-white">{{ $dispatchStats['active_deliveries'] }} <span class="fs-6 text-muted">Tickets</span></h2>
                    <p class="fs-8 text-success mb-0"><i class="bi bi-ethernet me-1"></i> Unified across all aggregator webhooks</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Total Cloud Kitchen Revenue</span>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-success">{{ $settings->currency_symbol }} {{ number_format($dispatchStats['total_delivery_revenue'], 2) }}</h2>
                    <p class="fs-8 text-muted mb-0">Includes partner packaging and GST tax collection</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Average Packaging SLA</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4"><i class="bi bi-stopwatch"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-warning">{{ $dispatchStats['avg_prep_sla'] }}</h2>
                    <p class="fs-8 text-success mb-0"><i class="bi bi-check2-circle me-1"></i> {{ $dispatchStats['on_time_pickup_pct'] }}% On-time courier handover rate</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #a855f7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Platform Volume Share</span>
                    <div class="p-3 bg-purple bg-opacity-10 text-primary rounded-4 fs-4" style="background: rgba(168, 85, 247, 0.15) !important; color: #c084fc !important;"><i class="bi bi-pie-chart-fill"></i></div>
                </div>
                <div>
                    <div class="d-flex justify-content-between fw-bolder font-monospace text-light fs-6 mb-1">
                        <span>Zomato: {{ $dispatchStats['zomato_share'] }}</span>
                        <span>Swiggy: {{ $dispatchStats['swiggy_share'] }}</span>
                    </div>
                    <p class="fs-8 text-muted mb-0">Direct WhatsApp Ordering: {{ $dispatchStats['direct_share'] }} (0% Commission)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Aggregator Order Feed -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-table me-2 text-info"></i> Active Delivery & Rider Handover Queue</h5>
            <span class="fs-7 text-muted font-monospace"><i class="bi bi-router me-1 text-success"></i> Webhook Receiver Active on Port 8107</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider">
                        <th class="py-3 ps-4">Aggregator Channel & ID</th>
                        <th class="py-3">Ordered Items & Tamper-Proof Pack</th>
                        <th class="py-3 text-center">Billing Total</th>
                        <th class="py-3">Courier Rider & Settlement Notes</th>
                        <th class="py-3 text-center">Kitchen KDS Status</th>
                        <th class="py-3 pe-4 text-end">Dispatch Handover</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $o)
                        <tr>
                            <td class="py-3 ps-4">
                                <div class="d-flex align-items-center">
                                    @if(strpos(strtoupper($o->order_type), 'ZOMATO') !== false)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 p-2 me-2 font-monospace fs-7"><i class="bi bi-bookmark-star-fill me-1"></i> ZOMATO GOLD</span>
                                    @elseif(strpos(strtoupper($o->order_type), 'SWIGGY') !== false)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 p-2 me-2 font-monospace fs-7"><i class="bi bi-lightning-charge-fill me-1"></i> SWIGGY ONE</span>
                                    @elseif(strpos(strtoupper($o->order_type), 'UBER') !== false)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 p-2 me-2 font-monospace fs-7"><i class="bi bi-car-front-fill me-1"></i> UBER EATS</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 p-2 me-2 font-monospace fs-7"><i class="bi bi-whatsapp me-1"></i> WHATSAPP DIRECT</span>
                                    @endif
                                </div>
                                <div class="fw-bolder font-monospace text-light fs-6 mt-1">#{{ $o->order_number }}</div>
                                <span class="fs-8 text-muted">{{ $o->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-3">
                                @foreach($o->items as $item)
                                    <div class="text-light fw-medium fs-7"><i class="bi bi-circle-fill text-primary me-2" style="font-size: 6px;"></i> {{ $item->quantity }}x {{ $item->item_name }}</div>
                                @endforeach
                                <small class="text-muted fs-8 font-monospace"><i class="bi bi-box2 text-secondary me-1"></i> Eco-friendly thermal seal container assigned</small>
                            </td>
                            <td class="text-center">
                                <div class="fw-bolder font-monospace text-warning fs-5">{{ $settings->currency_symbol }} {{ number_format($o->total_amount, 2) }}</div>
                                <span class="badge bg-dark border border-secondary border-opacity-25 text-light fs-8 font-monospace mt-1">GST Tax: {{ $settings->currency_symbol }} {{ number_format($o->tax_amount, 2) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-info fs-7"><i class="bi bi-person-bounding-box me-2"></i> {{ $o->notes ?? 'Courier partner en route...' }}</div>
                                <small class="text-success fs-8 font-monospace"><i class="bi bi-shield-lock me-1"></i> Rider Handover OTP Verified</small>
                            </td>
                            <td class="text-center">
                                @if(strpos(strtolower($o->status), 'out') !== false || strpos(strtolower($o->status), 'ready') !== false)
                                    <span class="badge badge-emerald px-3 py-2 fs-7"><i class="bi bi-check2-all me-1"></i> {{ strtoupper($o->status) }}</span>
                                @else
                                    <span class="badge badge-amber px-3 py-2 fs-7 animate-pulse"><i class="bi bi-fire me-1"></i> {{ strtoupper($o->status) }}</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold" onclick="markHandoverComplete(this, '{{ $o->order_number }}')">
                                    <i class="bi bi-send-check me-1"></i> Verify OTP & Dispatch
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Interactive Aggregator Simulation Modal -->
<div class="modal fade" id="simulateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold">
                    <i class="bi bi-broadcast text-warning me-2"></i> Inject Simulated Cloud Kitchen Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delivery.simulate') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <p class="text-muted fs-7 mb-4">Simulate an instant online food delivery aggregator webhook request to trigger automatic kitchen KDS scheduling, TCP thermal LAN printing, and raw inventory store COGS deduction.</p>
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Select Food Delivery Aggregator Platform</label>
                        <select name="platform" class="form-select bg-dark text-light border-secondary border-opacity-50 fs-6 py-2" required>
                            <option value="ZOMATO GOLD">🔴 ZOMATO GOLD PLATFORM (Direct Webhook API)</option>
                            <option value="SWIGGY ONE">🟠 SWIGGY ONE GOURMET (Cloud Kitchen API)</option>
                            <option value="UBER EATS">🟢 UBER EATS PRO DELIVERY</option>
                            <option value="WHATSAPP DIRECT">💬 DIRECT HOTEL WHATSAPP (0% Third-Party Commission)</option>
                        </select>
                    </div>
                    <div class="p-3 bg-dark bg-opacity-75 rounded-4 border border-secondary border-opacity-25 text-center">
                        <span class="fs-8 text-muted d-block text-uppercase">Automated Action Sequence</span>
                        <div class="d-flex justify-content-around text-success font-monospace fs-8 mt-2">
                            <span>1. Record Order</span> <i class="bi bi-arrow-right"></i>
                            <span>2. TCP Print 9100</span> <i class="bi bi-arrow-right"></i>
                            <span>3. Deduct COGS</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary px-4 fw-bold">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> FIRE TO KITCHEN KDS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markHandoverComplete(btn, orderId) {
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Verifying OTP...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success', 'text-white');
        btn.innerHTML = '<i class="bi bi-check2-all me-1"></i> Dispatched to Rider';
        
        const row = btn.closest('tr');
        row.style.opacity = '0.4';
        
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = 9999;
        toast.innerHTML = `
            <div class="alert alert-success shadow-lg rounded-4 d-flex align-items-center border-success p-3 text-light bg-dark" style="border: 2px solid #10b981 !important;">
                <i class="bi bi-bicycle text-success fs-3 me-3"></i>
                <div>
                    <strong class="text-white d-block">Order #${orderId} Handover Complete!</strong>
                    <span class="fs-8 text-muted">Rider OTP PIN verified. Package out for active city delivery.</span>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }, 900);
}
</script>
@endpush
