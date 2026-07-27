@extends('layouts.admin')

@section('title', 'Smart Waste & Spillage Logging')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill mb-2 fw-semibold font-monospace">
                <i class="bi bi-trash3-fill me-2"></i> 100% DYNAMIC MYSQL COGS LOSS PREVENTION
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Smart Waste & Spillage Action Center</h2>
            <p class="text-muted mb-0 fs-6">Tracking raw material spoilage, kitchen spillage, expired stock, and burnt dishes with real-time MySQL database persistence.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/inventory') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-box-seams-fill me-1"></i> Central Store
            </a>
            <button class="btn btn-modern-primary shadow-lg" data-bs-toggle="modal" data-bs-target="#logWasteModal">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Log Kitchen Spillage
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center shadow-lg" role="alert">
        <i class="bi bi-shield-check me-3 fs-3 animate-pulse"></i>
        <div>
            <strong class="text-white d-block">Spillage Incident Permanently Recorded in MySQL!</strong>
            <span class="fs-7">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- AI Loss Prevention Action Bar -->
    <div class="card glass-card border-0 rounded-5 p-4 mb-5 shadow-lg" style="background: radial-gradient(circle at 50% 100%, rgba(245, 158, 11, 0.15) 0%, transparent 60%); border-left: 5px solid #f59e0b !important;">
        <div class="row g-4 align-items-center">
            <div class="col-md-5">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 me-3 fs-2">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div>
                        <span class="fs-8 text-muted d-block text-uppercase fw-bold">AI Root-Cause Detection</span>
                        <h5 class="fw-bolder text-white mb-1">{{ $aiInsights['primary_root_cause'] }}</h5>
                        <p class="fs-8 text-warning mb-0"><i class="bi bi-arrow-right-circle me-1"></i> Action: {{ $aiInsights['recommended_action'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 border-start border-secondary border-opacity-25 ps-md-4">
                <span class="fs-8 text-muted d-block text-uppercase fw-bold">Waste-to-COGS Ratio</span>
                <div class="d-flex align-items-center mt-1">
                    <h4 class="fw-bolder font-monospace text-info mb-0 me-2">{{ $aiInsights['waste_cogs_percentage'] }}</h4>
                </div>
            </div>
            <div class="col-md-4 border-start border-secondary border-opacity-25 ps-md-4">
                <span class="fs-8 text-muted d-block text-uppercase fw-bold">Projected Monthly Savings</span>
                <h3 class="fw-bolder font-monospace text-success mb-0">{{ $settings->currency_symbol }} {{ number_format($aiInsights['projected_monthly_savings'], 2) }}</h3>
                <small class="text-secondary fs-8 font-monospace">Based on strict shelf FIFO and thermostat automation</small>
            </div>
        </div>
    </div>

    <!-- Executive Waste KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Recorded Spillage Events</span>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 fs-4"><i class="bi bi-clipboard2-pulse"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-white">{{ $totalEvents }} <span class="fs-6 text-muted">Incidents</span></h2>
                    <p class="fs-8 text-warning mb-0"><i class="bi bi-exclamation-circle me-1"></i> Active tracking in MySQL database</p>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f97316 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Total Raw COGS Loss Value</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4"><i class="bi bi-cart-x"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-danger">{{ $settings->currency_symbol }} {{ number_format($totalWasteValue, 2) }}</h2>
                    <p class="fs-8 text-muted mb-0">Deducted from daily gross operating margin</p>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-12">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Store Inventory Status</span>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-success">REAL-TIME SYNCED</h2>
                    <p class="fs-8 text-muted mb-0"><i class="bi bi-ethernet me-1 text-success"></i> Stock ledger balances updated immediately</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Waste Incident Roster Table -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5 shadow-lg">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-table me-2 text-danger"></i> Kitchen Waste & Spillage Incident Ledger</h5>
            <span class="fs-7 text-muted font-monospace"><i class="bi bi-database text-info me-1"></i> Live MySQL Persistent Feed</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider border-bottom border-secondary border-opacity-50">
                        <th class="py-3 ps-4">Incident Reference & Timestamp</th>
                        <th class="py-3">Raw Inventory Item & Unit Cost</th>
                        <th class="py-3 text-center">Quantity Lost & Total Fiscal Loss</th>
                        <th class="py-3">Primary Spoilage / Spillage Reason</th>
                        <th class="py-3">Responsible Culinary Station</th>
                        <th class="py-3 pe-4 text-end">Audit Verification</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wasteLogs as $log)
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <td class="py-3 ps-4 font-monospace">
                                <div class="fw-bolder text-light fs-6">#{{ $log->incident_reference }}</div>
                                <span class="fs-8 text-muted">{{ $log->created_at->format('d-M-Y @ H:i') }}</span>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-white fs-6">{{ $log->item_name }}</div>
                                <div class="fs-8 text-muted font-monospace">Unit Cost: {{ $settings->currency_symbol }} {{ number_format($log->unit_cost, 2) }} / {{ $log->unit }}</div>
                            </td>
                            <td class="text-center font-monospace">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fs-6 px-3 py-2 mb-1">{{ floatval($log->quantity) }} {{ $log->unit }}</span>
                                <div class="fw-bold text-warning fs-7">{{ $settings->currency_symbol }} {{ number_format($log->total_loss, 2) }} Loss</div>
                            </td>
                            <td>
                                <div class="fw-medium text-light fs-7"><i class="bi bi-exclamation-triangle text-warning me-2"></i> {{ $log->reason }}</div>
                                <small class="text-muted fs-8 font-monospace">Logged by: {{ $log->logged_by }}</small>
                            </td>
                            <td>
                                <div class="text-info fs-7 fw-medium">{{ $log->station }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <span class="badge badge-emerald px-3 py-2 fs-8 font-monospace">
                                    <i class="bi bi-check2-all me-1"></i> STOCK DEDUCTED
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Interactive Spillage Logging Modal -->
<div class="modal fade" id="logWasteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Record Kitchen Waste & Spillage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('waste.log') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <p class="text-muted fs-7 mb-4">Select raw store material and specify lost quantity to automatically decrement warehouse stock ledger and save permanently in MySQL database.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Select Raw Store Inventory Item</label>
                        <select name="inventory_item_id" class="form-select bg-dark text-light border-secondary border-opacity-50 font-monospace fs-6 py-2" required>
                            @foreach($inventoryItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (Current Stock: {{ $item->current_stock }} {{ $item->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Spilled / Expired Quantity</label>
                            <input type="number" step="0.001" name="quantity" class="form-control bg-dark text-light border-secondary border-opacity-50 font-monospace fs-5" value="1.000" min="0.1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Responsible Kitchen Station</label>
                            <select name="station" class="form-select bg-dark text-info border-secondary border-opacity-50 fs-7 py-2" required>
                                <option value="🍕 Pizza Oven Station (Zone A)">🍕 Pizza Oven (Zone A)</option>
                                <option value="🍳 Gourmet Line Grill (Zone B)">🍳 Gourmet Grill (Zone B)</option>
                                <option value="🥗 Cold Salad & Pantry Bar">🥗 Cold Pantry Bar</option>
                                <option value="🍷 Beverage & Bar Dispenser">🍷 Beverage Bar</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Root-Cause / Spillage Explanation</label>
                        <select name="reason" class="form-select bg-dark text-warning border-secondary border-opacity-50 fs-6 py-2" required>
                            <option value="Spillage during liquid transport / boiling">Spillage during liquid transport / boiling</option>
                            <option value="Expired date / Cooler temperature variance">Expired date / Cooler temperature variance</option>
                            <option value="Burnt during grilling or wood-fired baking">Burnt during grilling / oven baking</option>
                            <option value="Sub-standard quality crop / rejected during prep sorting">Sub-standard crop / rejected during prep sorting</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-lg">
                        <i class="bi bi-trash3-fill me-1"></i> DEDUCT STOCK & LOG LOSS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
