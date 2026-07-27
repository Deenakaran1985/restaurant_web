@extends('layouts.admin')

@section('title', 'Supplier Ledgers')
@section('page_title', 'Vendor Ledgers & Supply Chain Directory')

@section('content')
<div class="glass-card p-4">
    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary border-opacity-25">
        <h5 class="mb-0 fw-bold font-heading"><i class="bi bi-truck text-info me-2"></i> Authorized Ingredient Suppliers</h5>
        <button class="btn btn-modern-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add New Supplier</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark dark:text-white">
            <thead class="fs-7 text-secondary text-uppercase">
                <tr>
                    <th class="border-0 pb-3">Supplier Name</th>
                    <th class="border-0 pb-3">Contact Person</th>
                    <th class="border-0 pb-3">Phone / Email</th>
                    <th class="border-0 pb-3">GST / VAT Number</th>
                    <th class="border-0 pb-3">Supplied Items</th>
                    <th class="border-0 pb-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $s)
                    <tr>
                        <td class="fw-bold font-heading fs-6">{{ $s->name }}</td>
                        <td>{{ $s->contact_person ?? 'N/A' }}</td>
                        <td>
                            <div class="font-monospace text-primary">{{ $s->phone }}</div>
                            <div class="fs-7 text-secondary">{{ $s->email }}</div>
                        </td>
                        <td><span class="badge bg-dark text-warning font-monospace">{{ $s->gst_vat_number }}</span></td>
                        <td><span class="badge badge-emerald">{{ $s->inventoryItems->count() }} Ingredients</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border rounded-3"><i class="bi bi-file-earmark-text me-1"></i> View Purchase Orders</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
