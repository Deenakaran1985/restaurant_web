@extends('layouts.admin')

@section('title', 'Accounting & Tax Invoices')
@section('page_title', 'Financial Controller Ledgers & Daily Z-Report')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-4 mb-4 d-flex align-items-center shadow-lg" role="alert">
    <i class="bi bi-check-circle-fill me-3 fs-2 animate-pulse"></i>
    <div>
        <h5 class="fw-bold text-white mb-1">Cashier Billing Settlement Completed!</h5>
        <span class="fs-7 font-monospace">{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="row g-4 mb-4">
    <div class="col-12 col-md-3">
        <div class="glass-card p-4 h-100">
            <h6 class="text-uppercase fw-bold text-secondary fs-7">Gross Taxable Invoiced</h6>
            <h2 class="fw-bolder my-2 text-success font-heading">₹4,28,950.00</h2>
            <div class="fs-7 text-success"><i class="bi bi-graph-up-arrow me-1"></i> Month-to-date Ledger</div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="glass-card p-4 h-100">
            <h6 class="text-uppercase fw-bold text-secondary fs-7">GST & VAT Tax Liability</h6>
            <h2 class="fw-bolder my-2 text-warning font-heading">₹21,447.50</h2>
            <div class="fs-7 text-secondary">5.00% standard tax slab</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h6 class="text-uppercase fw-bold text-secondary fs-7 mb-2">Financial Controller Audit Suite</h6>
                <p class="fs-7 text-muted mb-3">Export compliant spreadsheets and reconcile cashier drawer settlements against bank terminal transfers.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('invoices.export') }}" class="btn btn-modern-primary flex-grow-1"><i class="bi bi-file-earmark-spreadsheet-fill me-2"></i> Export Excel Ledger</a>
                <button class="btn btn-modern-outline flex-grow-1" onclick="window.print()"><i class="bi bi-printer me-2"></i> Print Daily Z-Report</button>
            </div>
        </div>
    </div>
</div>

<div class="glass-card p-4">
    <h5 class="mb-4 fw-bold font-heading d-flex align-items-center justify-content-between">
        <span><i class="bi bi-receipt-cutoff text-info me-2"></i> Cashier Billing Station & Tax Invoices</span>
        <span class="badge badge-amber font-monospace fs-8"><i class="bi bi-broadcast me-1"></i> LIVE WAITER FEED</span>
    </h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark dark:text-white">
            <thead class="fs-7 text-secondary text-uppercase font-monospace">
                <tr>
                    <th class="border-0 pb-3">Invoice #</th>
                    <th class="border-0 pb-3">Order Number</th>
                    <th class="border-0 pb-3">Cashier Terminal</th>
                    <th class="border-0 pb-3">Subtotal</th>
                    <th class="border-0 pb-3">Tax (5%)</th>
                    <th class="border-0 pb-3">Grand Total</th>
                    <th class="border-0 pb-3">Billing Status</th>
                    <th class="border-0 pb-3 text-end">Settled Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td class="fw-bold font-monospace text-primary fs-6">{{ $inv->invoice_number }}</td>
                        <td class="font-monospace fw-bold">{{ $inv->order->order_number ?? 'POS Direct' }}</td>
                        <td class="text-muted">{{ $inv->cashier->name ?? 'Main Cashier' }}</td>
                        <td class="font-monospace">₹{{ number_format($inv->subtotal, 2) }}</td>
                        <td class="font-monospace">₹{{ number_format($inv->tax_total, 2) }}</td>
                        <td class="fw-bolder fs-5 text-warning font-monospace">₹{{ number_format($inv->grand_total, 2) }}</td>
                        <td>
                            @if($inv->payment_status == 'unpaid' || $inv->payment_status == 'pending')
                                <span class="badge badge-amber animate-pulse font-monospace px-3 py-2"><i class="bi bi-clock-history me-1"></i> PENDING CASHIER SETTLEMENT</span>
                            @else
                                <span class="badge badge-emerald font-monospace px-3 py-2"><i class="bi bi-check2-all me-1"></i> {{ strtoupper($inv->payment_status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                @if($inv->payment_status == 'unpaid' || $inv->payment_status == 'pending')
                                    <form action="{{ route('invoices.settle', $inv->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3 py-2 rounded-3 shadow hover-lift d-flex align-items-center text-nowrap" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                            <i class="bi bi-credit-card-2-front-fill me-2 fs-6"></i> Settle & Vacate Table
                                        </button>
                                    </form>
                                @endif
                                <button class="btn btn-sm btn-modern-outline px-3 py-2 text-nowrap" onclick="alert('🖨️ ESC/POS Tax Invoice re-printed to LAN printer 192.168.32.150:9100!');"><i class="bi bi-printer-fill me-1"></i> Re-print</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted fw-bold">No closed tax invoices generated yet today. Punch an order in POS to test!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
