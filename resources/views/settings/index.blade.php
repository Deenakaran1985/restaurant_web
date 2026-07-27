@extends('layouts.admin')

@section('title', 'Hotel Profile & KDS Routing Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">
                <i class="bi bi-sliders me-2"></i> ENTERPRISE HOTEL & RESTAURANT CONFIGURATION
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Hotel Settings & KDS Mode Routing</h2>
            <p class="text-muted mb-0 fs-6">Configure branch identity, GST/VAT tax policies, and switch between interactive KDS monitors or direct thermal printer order slips.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge {{ $settings->kds_routing_mode === 'thermal_printer_only' ? 'badge-amber' : 'badge-emerald' }} px-3 py-2 fs-7">
                <i class="bi {{ $settings->kds_routing_mode === 'thermal_printer_only' ? 'bi-printer-fill' : 'bi-display-fill' }} me-1"></i>
                Current Mode: {{ $settings->kds_routing_mode === 'thermal_printer_only' ? 'Direct Thermal Printer Receipt' : 'Interactive KDS Display Screen' }}
            </span>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
        <div class="fw-medium">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Left Column: KDS Operational Mode Switch (High Importance) -->
            <div class="col-lg-7">
                <div class="card glass-card border-0 rounded-5 p-4 mb-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 me-3 fs-3">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold font-heading mb-1">Kitchen Order Routing & KDS Optional Mode</h4>
                            <p class="text-muted mb-0 fs-7">Select how Kitchen Order Tickets (KOT) are handled upon Waiter and Cashier order firing.</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-light fw-bold mb-3 d-block">Choose Active KDS Operational Strategy:</label>
                        
                        <!-- Option 1: Direct Thermal Printing Only (KDS Optional/Disabled) -->
                        <div class="form-check custom-radio-card mb-3 p-3 rounded-4 border {{ $settings->kds_routing_mode === 'thermal_printer_only' ? 'border-warning bg-warning bg-opacity-10' : 'border-secondary border-opacity-25' }}">
                            <input class="form-check-input ms-0 me-3 mt-2" type="radio" name="kds_routing_mode" id="modePrinter" value="thermal_printer_only" {{ $settings->kds_routing_mode === 'thermal_printer_only' ? 'checked' : '' }} onchange="toggleCardHighlight(this)">
                            <label class="form-check-label w-100 ps-4" for="modePrinter" style="cursor: pointer;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-light fs-5"><i class="bi bi-printer-fill text-warning me-2"></i> Direct Thermal Kitchen Receipt Printer (KDS Optional)</span>
                                    <span class="badge bg-warning text-dark fw-bold">HIGH SPEED & RECOMMENDED</span>
                                </div>
                                <p class="text-muted fs-7 mb-2">
                                    Bypasses digital screen interaction in the kitchen. When Waiters or Cashiers click "FIRE KOT", an 80mm ESC/POS order slip is instantly dispatched over LAN TCP socket (Port 9100) directly to the physical kitchen receipt printer.
                                </p>
                                <div class="p-2 rounded-3 bg-dark bg-opacity-50 text-light font-monospace fs-8 border border-secondary border-opacity-25">
                                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Zero-touch Kitchen Workflow | Automatic COGS Deduction Activated
                                </div>
                            </label>
                        </div>

                        <!-- Option 2: Interactive KDS Touchscreen Monitor -->
                        <div class="form-check custom-radio-card mb-3 p-3 rounded-4 border {{ $settings->kds_routing_mode === 'screen_interactive' ? 'border-success bg-success bg-opacity-10' : 'border-secondary border-opacity-25' }}">
                            <input class="form-check-input ms-0 me-3 mt-2" type="radio" name="kds_routing_mode" id="modeScreen" value="screen_interactive" {{ $settings->kds_routing_mode === 'screen_interactive' ? 'checked' : '' }} onchange="toggleCardHighlight(this)">
                            <label class="form-check-label w-100 ps-4" for="modeScreen" style="cursor: pointer;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-light fs-5"><i class="bi bi-display-fill text-success me-2"></i> Interactive KDS Touch Monitor & Acoustic Buzzer</span>
                                    <span class="badge bg-success bg-opacity-25 text-success">DIGITAL WORKSPACE</span>
                                </div>
                                <p class="text-muted fs-7 mb-2">
                                    Routes incoming orders to kitchen wall-mounted iPad/Android tablets and desktop web screens. Requires kitchen staff to manually touch "Start Preparing" and "Mark Ready" to advance SLA clocks.
                                </p>
                                <div class="p-2 rounded-3 bg-dark bg-opacity-50 text-light font-monospace fs-8 border border-secondary border-opacity-25">
                                    <i class="bi bi-broadcast text-success me-1"></i> Live Color-Coded SLA Timers (< 10m Green | 10-20m Amber | > 20m Red)
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                        <div class="form-check form-switch mb-0 d-flex align-items-center justify-content-between ps-0">
                            <div>
                                <label class="form-check-label text-light fw-bold mb-1 d-block" for="autoDeduct">Automated Recipe Raw Material COGS Deduction upon KOT Print</label>
                                <p class="text-muted fs-8 mb-0">Automatically subtracts ingredient grammage from central stores as soon as order thermal slips are printed.</p>
                            </div>
                            <input class="form-check-input ms-3 fs-3 mt-0" type="checkbox" role="switch" id="autoDeduct" name="auto_deduct_inventory_on_print" {{ $settings->auto_deduct_inventory_on_print ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <!-- Hardware Printer Sockets -->
                <div class="card glass-card border-0 rounded-5 p-4">
                    <h5 class="fw-bold font-heading mb-3"><i class="bi bi-ethernet me-2 text-info"></i> Network LAN Socket Address Mapping</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fs-7 text-muted fw-bold">Kitchen Order Printer IP (ESC/POS LAN)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-light"><i class="bi bi-printer"></i></span>
                                <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 font-monospace" name="kitchen_printer_ip" value="{{ $settings->kitchen_printer_ip }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 text-muted fw-bold">TCP Socket Port (Default 9100)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-light"><i class="bi bi-hash"></i></span>
                                <input type="number" class="form-control bg-dark text-light border-secondary border-opacity-25 font-monospace" name="kitchen_printer_port" value="{{ $settings->kitchen_printer_port }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fs-7 text-muted fw-bold">Main Cashier Front-Desk Billing Printer IP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-light"><i class="bi bi-receipt-cutoff"></i></span>
                                <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 font-monospace" name="cashier_printer_ip" value="{{ $settings->cashier_printer_ip }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Hotel & Branch Profile -->
            <div class="col-lg-5">
                <div class="card glass-card border-0 rounded-5 p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 me-3 fs-3">
                                <i class="bi bi-buildings-fill"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold font-heading mb-1">Hotel Identity & Tax Policies</h4>
                                <p class="text-muted mb-0 fs-7">Global credentials embedded on thermal customer bills and accounting ledgers.</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-7 text-muted fw-bold">Hotel & Restaurant Establishment Name</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 fw-bold" name="hotel_name" value="{{ $settings->hotel_name }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fs-7 text-muted fw-bold">Branch / Unit Code</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 font-monospace" name="branch_code" value="{{ $settings->branch_code }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-7 text-muted fw-bold">Currency Symbol</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 fw-bold text-center" name="currency_symbol" value="{{ $settings->currency_symbol }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-7 text-muted fw-bold">GSTIN / VAT Tax Registration Number</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 font-monospace text-uppercase" name="gst_vat_number" value="{{ $settings->gst_vat_number }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-7 text-muted fw-bold">Default GST / VAT Dining Tax Percentage (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" class="form-control bg-dark text-light border-secondary border-opacity-25 fw-bold" name="default_tax_rate" value="{{ str_replace('.00', '.0', $settings->default_tax_rate) }}" required>
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-light">%</span>
                            </div>
                            <small class="text-muted fs-8">Applied automatically to all dine-in tables, takeaway packages, and mobile POS carts.</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fs-7 text-muted fw-bold">Contact Telephone</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25 font-monospace" name="contact_phone" value="{{ $settings->contact_phone }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-7 text-muted fw-bold">Management Email</label>
                                <input type="email" class="form-control bg-dark text-light border-secondary border-opacity-25" name="contact_email" value="{{ $settings->contact_email }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top border-secondary border-opacity-25">
                        <button type="submit" class="btn btn-modern-primary w-100 py-3 fw-bold fs-5 shadow-lg d-flex align-items-center justify-content-center">
                            <i class="bi bi-check2-circle me-2 fs-4"></i> SAVE & DEPLOY HOTEL CONFIGURATION
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleCardHighlight(radio) {
    document.querySelectorAll('.custom-radio-card').forEach(card => {
        card.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10', 'border-success', 'bg-success');
        card.classList.add('border-secondary', 'border-opacity-25');
    });

    const parentCard = radio.closest('.custom-radio-card');
    parentCard.classList.remove('border-secondary', 'border-opacity-25');
    if (radio.value === 'thermal_printer_only') {
        parentCard.classList.add('border-warning', 'bg-warning', 'bg-opacity-10');
    } else {
        parentCard.classList.add('border-success', 'bg-success', 'bg-opacity-10');
    }
}
</script>
@endpush
