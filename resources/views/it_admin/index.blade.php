@extends('layouts.admin')

@section('title', 'IT Admin Network Portal')
@section('page_title', 'Network Terminals, Firewalls & Hardware Printing')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                <h5 class="mb-0 fw-bold font-heading"><i class="bi bi-shield-check text-danger me-2"></i> Authorized IP Terminal Whitelisting</h5>
                <button class="btn btn-modern-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Register IP</button>
            </div>
            <p class="fs-7 text-secondary mb-4">
                To prevent unauthorized network device snooping over port <code>8107</code>, only approved IP addresses are permitted to obtain Sanctum API tokens and stream KDS sockets.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark dark:text-white">
                    <thead class="fs-7 text-secondary text-uppercase">
                        <tr>
                            <th class="border-0">Device / Name</th>
                            <th class="border-0">IP Address</th>
                            <th class="border-0">Type</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terminals as $terminal)
                            <tr>
                                <td class="fw-bold">{{ $terminal->terminal_name }}</td>
                                <td><span class="badge bg-dark text-warning font-monospace fs-7">{{ $terminal->ip_address }}</span></td>
                                <td>{{ strtoupper($terminal->terminal_type) }}</td>
                                <td><span class="badge badge-emerald"><i class="bi bi-wifi me-1"></i> ONLINE</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>Central Master POS Gateway</td>
                                <td><span class="badge bg-dark text-warning font-monospace fs-7">192.168.32.249:8107</span></td>
                                <td>ADMIN_PC</td>
                                <td><span class="badge badge-emerald">ACTIVE</span></td>
                                <td class="text-end"><button class="btn btn-sm btn-light border"><i class="bi bi-gear"></i></button></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Thermal ESC/POS Network Printing Socket Hub -->
    <div class="col-12 col-lg-6">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                <h5 class="mb-0 fw-bold font-heading"><i class="bi bi-printer-fill text-info me-2"></i> ESC/POS Network Thermal Printers</h5>
                <button class="btn btn-modern-outline btn-sm"><i class="bi bi-search me-1"></i> Discover LAN Printers</button>
            </div>
            <p class="fs-7 text-secondary mb-4">
                Automate kitchen order tickets (KOT) and billing receipts directly over TCP sockets (default port <code>9100</code>) for instant zero-driver printing.
            </p>

            <div class="d-flex flex-column gap-3">
                <div class="p-3 rounded-4 d-flex align-items-center justify-content-between border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 me-3 fs-3">
                            <i class="bi bi-printer-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold font-heading">Main Cashier Billing Printer (80mm)</h6>
                            <span class="badge bg-secondary text-white font-monospace">192.168.32.150:9100</span>
                            <span class="badge badge-emerald ms-2">CONNECTED</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-modern-outline" onclick="testLanPrinter('cashier', '192.168.32.150')"><i class="bi bi-broadcast me-1"></i> Test Print</button>
                </div>

                <div class="p-3 rounded-4 d-flex align-items-center justify-content-between border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 me-3 fs-3">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold font-heading">Kitchen KOT Buzzer Printer (80mm)</h6>
                            <span class="badge bg-secondary text-white font-monospace">192.168.32.151:9100</span>
                            <span class="badge badge-amber ms-2">READY</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-modern-outline" onclick="testLanPrinter('kot', '192.168.32.151')"><i class="bi bi-broadcast me-1"></i> Test Print</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testLanPrinter(type, ip) {
    fetch("{{ route('printer.test', [], false) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ type: type, ip: ip })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message + '\n\n[Note: If physical printer is offline, receipt bytes are captured cleanly in storage/logs/thermal_receipts.log for testing.]');
    })
    .catch(err => console.error(err));
}
</script>
@endpush
