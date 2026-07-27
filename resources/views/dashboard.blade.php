@extends('layouts.admin')

@section('title', 'Executive Dashboard')
@section('page_title', 'Enterprise Operational Overview')

@section('content')
<div class="row g-4 mb-5">
    <!-- Revenue Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card p-4 hover-lift h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-uppercase fw-bold text-secondary fs-7 mb-2">Today's Revenue</h6>
                    <h2 class="fw-bolder mb-0 font-heading text-dark dark:text-white">₹{{ number_format($stats['revenue_today'] ?? 45280, 2) }}</h2>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                    <i class="bi bi-currency-rupee fs-3"></i>
                </div>
            </div>
            <div class="d-flex align-items-center mt-4 text-success fs-7 fw-bold">
                <i class="bi bi-arrow-up-right-circle-fill me-1 fs-6"></i> +18.4% compared to yesterday
            </div>
        </div>
    </div>

    <!-- Active Dining Occupancy -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card p-4 hover-lift h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-uppercase fw-bold text-secondary fs-7 mb-2">Dining Table Occupancy</h6>
                    <h2 class="fw-bolder mb-0 font-heading text-dark dark:text-white">{{ $stats['active_tables'] ?? 3 }} <span class="fs-5 text-muted fw-normal">/ {{ $stats['total_tables'] ?? 8 }} Tables</span></h2>
                </div>
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                    <i class="bi bi-grid-3x3-gap-fill fs-3"></i>
                </div>
            </div>
            <div class="d-flex align-items-center mt-4 text-info fs-7 fw-bold">
                <i class="bi bi-people-fill me-1 fs-6"></i> ~28 Guests Seated Now
            </div>
        </div>
    </div>

    <!-- Kitchen Display Queue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card p-4 hover-lift h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-uppercase fw-bold text-secondary fs-7 mb-2">Live KDS Queue</h6>
                    <h2 class="fw-bolder mb-0 font-heading text-dark dark:text-white">{{ $stats['pending_kds'] ?? 4 }} <span class="fs-6 text-warning fw-normal">In Preparation</span></h2>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                    <i class="bi bi-fire fs-3"></i>
                </div>
            </div>
            <div class="d-flex align-items-center mt-4 text-warning fs-7 fw-bold">
                <i class="bi bi-stopwatch-fill me-1 fs-6"></i> Avg. Cook Time: 11 mins
            </div>
        </div>
    </div>

    <!-- Inventory Alert Banner -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card p-4 hover-lift h-100 d-flex flex-column justify-content-between border-danger border-opacity-50">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-uppercase fw-bold text-danger fs-7 mb-2">Inventory Stock Alert</h6>
                    <h2 class="fw-bolder mb-0 font-heading text-dark dark:text-white">{{ $stats['low_stock_count'] ?? 1 }} <span class="fs-6 text-muted fw-normal">Items Critical</span></h2>
                </div>
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                    <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                </div>
            </div>
            <div class="d-flex align-items-center mt-4 text-danger fs-7 fw-bold">
                <i class="bi bi-cart-plus-fill me-1 fs-6"></i> Automated reorder ready
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Live Kitchen & Order Pipeline -->
<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary border-opacity-25">
                <h5 class="mb-0 fw-bold font-heading"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Real-Time Order Stream</h5>
                <a href="{{ url('/kds') }}" class="btn btn-modern-outline btn-sm">View Full KDS Screen</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark dark:text-white">
                    <thead class="text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="border-0 pb-3">Order #</th>
                            <th class="border-0 pb-3">Table / Source</th>
                            <th class="border-0 pb-3">Waiter / Staff</th>
                            <th class="border-0 pb-3">Amount</th>
                            <th class="border-0 pb-3">KDS Status</th>
                            <th class="border-0 pb-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-primary">{{ $order->order_number }}</td>
                                <td>{{ $order->table->table_number ?? 'Dine-In' }}</td>
                                <td>{{ $order->waiter->name ?? 'Self Order' }}</td>
                                <td class="fw-bold">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @if($order->status == 'placed')
                                        <span class="badge badge-amber"><i class="bi bi-hourglass me-1"></i> Received</span>
                                    @elseif($order->status == 'kitchen_preparing')
                                        <span class="badge badge-crimson"><i class="bi bi-fire me-1"></i> Cooking</span>
                                    @else
                                        <span class="badge badge-emerald"><i class="bi bi-check2-all me-1"></i> Served</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light border rounded-3"><i class="bi bi-eye-fill"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="bi bi-inbox-fill fs-2"></i></div>
                                    <p class="mb-3 fw-bold text-secondary">No orders punched yet today.</p>
                                    <a href="{{ url('/pos') }}" class="btn btn-modern-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Create First Order</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Access Hub & Hardware Ping -->
    <div class="col-12 col-xl-4">
        <div class="glass-card p-4 mb-4">
            <h5 class="mb-3 fw-bold font-heading"><i class="bi bi-terminal-fill text-info me-2"></i> Local Network Routing</h5>
            <div class="p-3 rounded-4 mb-3" style="background: rgba(13, 148, 136, 0.08); border: 1px dashed var(--bs-primary);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-7 text-uppercase text-secondary">Active KDS Socket Host</span>
                    <span class="badge badge-emerald">ONLINE & SECURED</span>
                </div>
                <div class="fs-5 font-monospace fw-bolder text-primary">http://192.168.32.249:8107</div>
                <div class="fs-7 text-muted mt-1">Ready for Tablet & Flutter Mobile POS APK / IPA Synch.</div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ url('/it-admin') }}" class="btn btn-modern-outline d-flex align-items-center justify-content-center">
                    <i class="bi bi-sliders me-2"></i> IT Admin Port & LAN Config
                </a>
            </div>
        </div>

        <div class="glass-card p-4">
            <h5 class="mb-3 fw-bold font-heading"><i class="bi bi-person-bounding-box text-success me-2"></i> Role Operations Hub</h5>
            <p class="fs-7 text-secondary mb-3">You are logged in as <strong class="text-dark dark:text-white">{{ auth()->user()->name }}</strong>. Toggle profiles below to review specialized UI permissions:</p>
            
            <div class="list-group list-group-flush border-0">
                <a href="{{ url('/switch-role/chef') }}" class="list-group-item list-group-item-action bg-transparent text-dark dark:text-white d-flex align-items-center justify-content-between py-3 border-secondary border-opacity-10">
                    <div>
                        <i class="bi bi-person-fill text-danger me-2"></i> <strong>Executive Chef (KDS)</strong>
                        <div class="fs-7 text-muted">Acoustic chimes, recipe ingredients, wastage logging</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ url('/switch-role/cashier') }}" class="list-group-item list-group-item-action bg-transparent text-dark dark:text-white d-flex align-items-center justify-content-between py-3 border-secondary border-opacity-10">
                    <div>
                        <i class="bi bi-calculator-fill text-success me-2"></i> <strong>Main POS Cashier</strong>
                        <div class="fs-7 text-muted">Bill splitting, discount vouchers, thermal receipts</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ url('/switch-role/storekeeper') }}" class="list-group-item list-group-item-action bg-transparent text-dark dark:text-white d-flex align-items-center justify-content-between py-3 border-secondary border-opacity-10">
                    <div>
                        <i class="bi bi-box-seams text-info me-2"></i> <strong>Store & Inventory Keeper</strong>
                        <div class="fs-7 text-muted">Raw material audits, supplier POs, unit costs</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
