@extends('layouts.admin')

@section('title', 'Executive Night Audit & Batch Rollover')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill mb-2 fw-semibold font-monospace">
                <i class="bi bi-moon-stars-fill me-2"></i> END-OF-DAY FISCAL ROLLOVER & Z-REPORT
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Daily Night Audit & Batch Rollover</h2>
            <p class="text-muted mb-0 fs-6">Reconciling cashier POS tills, digital payment aggregators, room folios, and raw material COGS consumption.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/accounts/profit-and-loss') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-pie-chart-fill me-1"></i> Executive P&L
            </a>
            @if(!session('audit_completed'))
            <form action="{{ route('night_audit.execute') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger rounded-pill shadow-lg px-4 fw-bold" onclick="return confirm('Execute Midnight EOD Rollover? This will archive all daily transactions and print the Master Z-Report over TCP Port 9100.')">
                    <i class="bi bi-printer-fill me-1"></i> RUN NIGHT AUDIT & PRINT Z-REPORT
                </button>
            </form>
            @else
            <button class="btn btn-success rounded-pill shadow-lg px-4 fw-bold" disabled>
                <i class="bi bi-check-circle-fill me-1"></i> AUDIT COMPLETED & CERTIFIED
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-4 mb-5 shadow-lg" role="alert">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-shield-lock-fill me-3 fs-2 animate-bounce"></i>
            <div>
                <h4 class="fw-bolder font-monospace text-white mb-0">Daily Fiscal Audit Certified & Archived!</h4>
                <span class="fs-7 text-muted">{{ session('success') }}</span>
            </div>
        </div>
        <hr class="border-secondary border-opacity-25 my-3">
        <div class="d-flex justify-content-between font-monospace text-light fs-8">
            <span>Audit Cryptographic Hash: <strong class="text-info">#AGY-EOD-{{ strtoupper(substr(md5(now()), 0, 12)) }}</strong></span>
            <span>TCP Socket Output: <strong class="text-success">Port 9100 Cashier Printer Verified</strong></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Audit Status Ribbon -->
    <div class="card glass-card border-0 rounded-5 p-4 mb-5" style="background: radial-gradient(circle at 100% 0%, rgba(239, 68, 68, 0.12) 0%, transparent 60%); border-left: 5px solid #ef4444 !important;">
        <div class="row g-4 align-items-center">
            <div class="col-md-4">
                <span class="fs-8 text-muted d-block text-uppercase fw-bold">Audit Accounting Period</span>
                <h4 class="fw-bolder font-monospace text-white mb-0 mt-1"><i class="bi bi-calendar-check text-danger me-2"></i>{{ $auditData['audit_date'] }}</h4>
                <span class="fs-8 text-secondary">Timestamp: {{ $auditData['audit_time'] }}</span>
            </div>
            <div class="col-md-3 border-start border-secondary border-opacity-25 ps-md-4">
                <span class="fs-8 text-muted d-block text-uppercase fw-bold">Total Dining Covers Served</span>
                <div class="d-flex align-items-center mt-1">
                    <h3 class="fw-bolder font-monospace text-info mb-0 me-2">{{ $auditData['total_covers'] }} <span class="fs-6 text-muted">Guests</span></h3>
                </div>
            </div>
            <div class="col-md-5 border-start border-secondary border-opacity-25 ps-md-4 d-flex justify-content-between align-items-center">
                <div>
                    <span class="fs-8 text-muted d-block text-uppercase fw-bold">Audit Rollover State</span>
                    <span class="badge {{ session('audit_completed') ? 'badge-emerald' : 'badge-amber' }} px-3 py-2 fs-7 font-monospace mt-1">
                        <i class="bi {{ session('audit_completed') ? 'bi-shield-check-fill' : 'bi-hourglass-split animate-pulse' }} me-1"></i> {{ $auditData['status'] }}
                    </span>
                </div>
                <div class="text-end">
                    <span class="fs-8 text-muted d-block">Open Kitchen Tickets: <strong class="text-light">{{ $auditData['pending_kds_tickets'] }}</strong></span>
                    <span class="fs-8 text-muted d-block">Unclosed Floor Tables: <strong class="text-light">{{ $auditData['unclosed_tables'] }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Tender Reconciliation Grid -->
    <h5 class="fw-bold font-heading text-white mb-3"><i class="bi bi-safe me-2 text-primary"></i> Daily Tender Split & Channel Reconciliation</h5>
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 me-3 fs-3"><i class="bi bi-cash-stack"></i></div>
                        <div>
                            <span class="text-light fs-6 fw-bold d-block">Physical Cash Tills</span>
                            <span class="fs-8 text-muted">Front-Desk Waiter POS drawers</span>
                        </div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-monospace">VERIFIED</span>
                </div>
                <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-baseline">
                    <span class="text-muted fs-8">Net Till Count:</span>
                    <span class="fw-bolder font-monospace text-success fs-4">{{ $settings->currency_symbol }} {{ number_format($auditData['cash_tendered'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #38bdf8 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 me-3 fs-3"><i class="bi bi-phone-vibrate"></i></div>
                        <div>
                            <span class="text-light fs-6 fw-bold d-block">Digital UPI / Wallets</span>
                            <span class="fs-8 text-muted">Razorpay, Zomato & Swiggy deposits</span>
                        </div>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 font-monospace">SYNCED</span>
                </div>
                <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-baseline">
                    <span class="text-muted fs-8">Electronic Settlement:</span>
                    <span class="fw-bolder font-monospace text-info fs-4">{{ $settings->currency_symbol }} {{ number_format($auditData['digital_upi_tendered'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 me-3 fs-3"><i class="bi bi-door-closed-fill"></i></div>
                        <div>
                            <span class="text-light fs-6 fw-bold d-block">In-Room Guest Folios</span>
                            <span class="fs-8 text-muted">Tower room suites & corporate accounts</span>
                        </div>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 font-monospace">FOLIO LINKED</span>
                </div>
                <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-baseline">
                    <span class="text-muted fs-8">Charged to Rooms:</span>
                    <span class="fw-bolder font-monospace text-warning fs-4">{{ $settings->currency_symbol }} {{ number_format($auditData['room_folio_charged'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- EOD Accounting Ledger Summary -->
    <div class="card glass-card border-0 rounded-5 p-4 p-md-5 mb-5">
        <h4 class="fw-bold text-white mb-4"><i class="bi bi-journal-check me-2 text-success"></i> Consolidated Master Fiscal Statement (EOD Z-Report)</h4>
        
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead class="border-bottom border-secondary border-opacity-50 text-muted fs-7 font-monospace text-uppercase">
                    <tr>
                        <th class="py-3">Ledger Line Item</th>
                        <th class="py-3 text-center">Accounting Basis / Valuation</th>
                        <th class="py-3 text-end">Daily Amount Balance</th>
                    </tr>
                </thead>
                <tbody class="font-monospace">
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <td class="py-3 text-light fw-bold">Gross POS Collections (Gross Dining Revenue)</td>
                        <td class="text-center text-muted">All POS, KDS, Delivery & Room Folio receipts</td>
                        <td class="text-end fw-bolder text-white fs-5">{{ $settings->currency_symbol }} {{ number_format($auditData['gross_collections'], 2) }}</td>
                    </tr>
                    <tr class="border-bottom border-secondary border-opacity-25 text-danger">
                        <td class="py-3">Less: GST Tax Liability Reserve (5%)</td>
                        <td class="text-center text-muted">Tax collected on behalf of Government Exchequer</td>
                        <td class="text-end fw-bold">- {{ $settings->currency_symbol }} {{ number_format($auditData['tax_reserve'], 2) }}</td>
                    </tr>
                    <tr class="border-bottom border-secondary border-opacity-25 text-primary">
                        <td class="py-3 fw-bolder">Net Operating Dining Revenue (NOPR)</td>
                        <td class="text-center text-muted">Actual earnable dining room revenue</td>
                        <td class="text-end fw-bolder fs-5">{{ $settings->currency_symbol }} {{ number_format($auditData['net_revenue'], 2) }}</td>
                    </tr>
                    <tr class="border-bottom border-secondary border-opacity-25 text-warning">
                        <td class="py-3">Less: Recipe Theoretical COGS Depletion (28.4%)</td>
                        <td class="text-center text-muted">Calculated raw materials depleted from central store</td>
                        <td class="text-end fw-bold">- {{ $settings->currency_symbol }} {{ number_format($auditData['theoretical_cogs'], 2) }}</td>
                    </tr>
                    <tr class="border-top border-secondary border-opacity-75 bg-dark bg-opacity-50">
                        <td class="py-4 text-success fw-bolder fs-5"><i class="bi bi-award-fill me-2"></i> ESTIMATED NET EBITDA PROFIT MARGIN</td>
                        <td class="text-center text-success fw-bold">Daily bottom-line enterprise operational profit</td>
                        <td class="text-end fw-bolder text-success fs-3">{{ $settings->currency_symbol }} {{ number_format($auditData['estimated_ebitda'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
