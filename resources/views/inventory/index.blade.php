@extends('layouts.admin')

@section('title', 'Stores & Raw Material Inventory')
@section('page_title', 'Central Kitchen Warehouse & Raw Materials')

@section('content')
<div class="row g-4 mb-4">
    <!-- Quick Stock Health -->
    <div class="col-12 col-md-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <h6 class="text-uppercase fw-bold text-secondary fs-7">Total Raw Material Valuation</h6>
            <h2 class="fw-bolder my-2 text-primary font-heading">₹1,84,320.00</h2>
            <div class="fs-7 text-muted">Audited across {{ $stores->count() }} kitchen stores</div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-uppercase fw-bold text-secondary fs-7 mb-0">Quick Storekeeper Actions</h6>
                <button class="btn btn-sm btn-outline-danger fw-bold"><i class="bi bi-trash3-fill me-1"></i> Log Wastage / Spillage</button>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-modern-primary flex-grow-1 py-3"><i class="bi bi-download me-2"></i> Receive Supplier Shipment (PO)</button>
                <button class="btn btn-modern-outline flex-grow-1 py-3"><i class="bi bi-clipboard2-check me-2"></i> Conduct Shelf Stock Audit</button>
            </div>
        </div>
    </div>
</div>

<div class="glass-card p-4">
    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary border-opacity-25">
        <h5 class="mb-0 fw-bold font-heading"><i class="bi bi-box-seams-fill text-success me-2"></i> Real-time Stock Levels</h5>
        <input type="text" class="form-control form-control-sm bg-transparent border-secondary text-dark dark:text-white" placeholder="Filter ingredient SKU or name..." style="max-width: 250px;">
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark dark:text-white">
            <thead class="fs-7 text-secondary text-uppercase">
                <tr>
                    <th class="border-0 pb-3">Ingredient Name & SKU</th>
                    <th class="border-0 pb-3">Store Location</th>
                    <th class="border-0 pb-3">Current Stock</th>
                    <th class="border-0 pb-3">Min Alert Threshold</th>
                    <th class="border-0 pb-3">Acquisition Cost</th>
                    <th class="border-0 pb-3">Preferred Supplier</th>
                    <th class="border-0 pb-3 text-end">Stock Adjust</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            <div class="fw-bold fs-6">{{ $item->name }}</div>
                            <span class="badge bg-dark text-warning font-monospace fs-7">{{ $item->sku }}</span>
                        </td>
                        <td><span class="badge badge-azure">{{ $item->store->name ?? 'Main Store' }}</span></td>
                        <td>
                            <span class="fs-5 fw-bolder {{ $item->current_stock <= $item->min_alert_stock ? 'text-danger' : 'text-success' }}">
                                {{ $item->current_stock }} {{ $item->unit }}
                            </span>
                        </td>
                        <td><span class="badge badge-amber">{{ $item->min_alert_stock }} {{ $item->unit }}</span></td>
                        <td class="fw-bold">₹{{ number_format($item->unit_cost, 2) }} / {{ $item->unit }}</td>
                        <td class="text-muted">{{ $item->supplier->name ?? 'Direct Farm' }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border rounded-3"><i class="bi bi-plus-slash-minus"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
