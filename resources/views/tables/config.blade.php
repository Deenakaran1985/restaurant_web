@extends('layouts.admin')

@section('title', 'Table Configuration & QR Standees')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-semibold font-monospace">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i> DINING ROOM CAPACITY & TABLE MANAGEMENT
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Table Configuration & QR Standees</h2>
            <p class="text-muted mb-0 fs-6">Manage dining room table numbering, seat capacities, floor section assignments, and print guest self-ordering tabletop QR standee displays.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/tables') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-eye-fill me-1"></i> Live Table Grid
            </a>
            <button class="btn btn-modern-primary shadow-lg" data-bs-toggle="modal" data-bs-target="#addTableModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Add Table Slot
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-3 fs-3 animate-pulse"></i>
        <div>
            <strong class="text-white d-block">Table Architecture Updated!</strong>
            <span class="fs-7">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Table Configuration KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Total Dining Tables</span>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fs-4"><i class="bi bi-table"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-white">{{ $configStats['total_tables'] }} <span class="fs-6 text-muted">Slots</span></h2>
                    <p class="fs-8 text-primary mb-0"><i class="bi bi-qr-code me-1"></i> Dedicated digital menus generated</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Total Seating Capacity</span>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4"><i class="bi bi-people-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-success">{{ $configStats['total_seating_capacity'] }} <span class="fs-6 text-muted">Guests</span></h2>
                    <p class="fs-8 text-muted mb-0">Maximum dining room guest occupancy</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Vacant Ready Tables</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4"><i class="bi bi-check2-all"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-warning">{{ $configStats['vacant_ready_slots'] }} <span class="fs-6 text-muted">Available</span></h2>
                    <p class="fs-8 text-success mb-0">Prepared for immediate waiter POS seating</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #a855f7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Dining Floor Sections</span>
                    <div class="p-3 bg-purple bg-opacity-10 text-primary rounded-4 fs-4" style="background: rgba(168, 85, 247, 0.15) !important; color: #c084fc !important;"><i class="bi bi-building"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-light">{{ $configStats['active_sections'] }} <span class="fs-6 text-muted">Zones</span></h2>
                    <p class="fs-8 text-muted mb-0">VIP Lounge, Rooftop, Garden & Main Hall</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dining Table Architecture Roster -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-gear me-2 text-primary"></i> Master Table Architecture & Seating Ledger</h5>
            <span class="fs-7 text-muted font-monospace"><i class="bi bi-phone me-1 text-success"></i> Mobile QR Self-Ordering Active on Port 8107</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider">
                        <th class="py-3 ps-4">Table Number & Floor Zone</th>
                        <th class="py-3 text-center">Seating Slot Capacity</th>
                        <th class="py-3 text-center">Current Operational State</th>
                        <th class="py-3 text-center">Digital QR Menu Endpoint</th>
                        <th class="py-3 pe-4 text-end">Configuration Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                        <tr>
                            <td class="py-3 ps-4 font-monospace">
                                <div class="fw-bolder text-white fs-5"><i class="bi bi-table text-info me-2"></i> {{ $table->table_number }}</div>
                                <span class="badge bg-dark border border-secondary border-opacity-50 text-secondary mt-1">{{ $table->section->name ?? 'Main Dining Lounge' }}</span>
                            </td>
                            <td class="text-center font-monospace">
                                <span class="fw-bolder text-warning fs-5">{{ $table->capacity }}</span> <span class="text-muted fs-7">Seats</span>
                            </td>
                            <td class="text-center">
                                @if($table->status === 'vacant')
                                    <span class="badge badge-emerald px-3 py-2 fs-7"><i class="bi bi-check-circle-fill me-1"></i> VACANT (READY)</span>
                                @elseif($table->status === 'ordered' || $table->status === 'occupied')
                                    <span class="badge badge-amber px-3 py-2 fs-7 animate-pulse"><i class="bi bi-fire me-1"></i> OCCUPIED & ORDERING</span>
                                @elseif($table->status === 'reserved')
                                    <span class="badge badge-purple px-3 py-2 fs-7" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;"><i class="bi bi-bookmark-star-fill me-1"></i> RESERVED VIP</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2 fs-7"><i class="bi bi-tools me-1"></i> MAINTENANCE / OFFLINE</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('qr.menu', $table->id) }}" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold">
                                    <i class="bi bi-qr-code me-1"></i> Open QR Portal #{{ $table->id }}
                                </a>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    <button class="btn btn-sm btn-modern-outline" onclick="showQrStandee('{{ $table->table_number }}', '{{ route('qr.menu', $table->id) }}', '{{ $table->section->name ?? 'Main Lounge' }}', '{{ $table->capacity }}')" title="View Printable Acrylic Standee">
                                        <i class="bi bi-printer-fill text-primary"></i> Standee Card
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Status
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-secondary border-opacity-50">
                                            <li>
                                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="vacant">
                                                    <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i> Mark Vacant</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="ordered">
                                                    <button type="submit" class="dropdown-item text-warning"><i class="bi bi-person-fill me-2"></i> Mark Occupied / Ordered</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="reserved">
                                                    <button type="submit" class="dropdown-item text-info"><i class="bi bi-bookmark-fill me-2"></i> Reserve for VIP</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="maintenance">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-tools me-2"></i> Offline / Maintenance</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add New Dining Table Slot Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold">
                    <i class="bi bi-plus-circle-fill text-warning me-2"></i> Create New Dining Table Slot
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <p class="text-muted fs-7 mb-4">Register a new physical or virtual table slot in the dining room database. A unique table-side QR self-ordering link will be generated immediately.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Table Number / Identifier</label>
                        <input type="text" name="table_number" class="form-control bg-dark text-white font-monospace fs-5 border-secondary border-opacity-50" placeholder="e.g. T-12 or ROOF-04" required>
                        <small class="text-muted fs-8">Must be unique across the entire hotel & resort complex.</small>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Seating Capacity (Guests)</label>
                            <input type="number" name="capacity" class="form-control bg-dark text-warning font-monospace fs-5 border-secondary border-opacity-50" value="4" min="1" max="50" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Initial Operational Status</label>
                            <select name="status" class="form-select bg-dark text-success border-secondary border-opacity-50 fs-6 py-2">
                                <option value="vacant">🟢 Vacant (Ready)</option>
                                <option value="reserved">🟣 Reserved VIP</option>
                                <option value="maintenance">🔴 Maintenance Offline</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Assign to Dining Floor Section</label>
                        <select name="section_id" class="form-select bg-dark text-light border-secondary border-opacity-50 font-monospace fs-6 py-2" required>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> SAVE & GENERATE QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Printable Acrylic QR Standee Visualizer Modal -->
<div class="modal fade" id="qrStandeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-warning border-opacity-50 text-light rounded-5 p-4 text-center">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-warning">
                    <i class="bi bi-qr-code text-white me-2"></i> Tabletop QR Standee Card
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-5 text-center">
                <div class="p-4 rounded-5 bg-dark d-inline-block border border-secondary border-opacity-50 shadow-lg mb-4" style="max-width: 340px;">
                    <span class="badge bg-warning text-dark font-monospace fw-bold px-3 py-1 mb-2 fs-8 text-uppercase" id="standeeSection">MAIN DINING LOUNGE</span>
                    <h1 class="fw-bolder font-monospace text-white mb-2" id="standeeTableNum">T-01</h1>
                    <p class="fs-8 text-muted mb-4">Scan below to access digital interactive menu & order directly to your table without waiting for a waiter!</p>
                    
                    <div class="p-3 bg-white rounded-4 mb-4 shadow d-inline-block">
                        <i class="bi bi-qr-code text-dark" style="font-size: 8rem;"></i>
                    </div>
                    
                    <div class="d-flex justify-content-around text-muted fs-8 font-monospace border-top border-secondary border-opacity-25 pt-3">
                        <span><i class="bi bi-people-fill text-info me-1"></i> <span id="standeeCap">4</span> Seats</span>
                        <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Instant POS Sync</span>
                    </div>
                </div>
                <p class="text-secondary fs-8 font-monospace mb-0" id="standeeLinkTxt">Link: http://192.168.32.249:8107/menu/qr/1</p>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-25 pt-3 justify-content-center">
                <a href="#" target="_blank" id="standeeOpenBtn" class="btn btn-outline-warning rounded-pill px-4 fw-bold me-2"><i class="bi bi-box-arrow-up-right me-1"></i> Test Link</a>
                <button type="button" class="btn btn-modern-primary rounded-pill px-4 fw-bold" onclick="window.print()"><i class="bi bi-printer-fill me-1"></i> PRINT ACERLYIC CARD</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showQrStandee(tableNum, linkUrl, sectionName, capacity) {
    document.getElementById('standeeTableNum').innerText = tableNum;
    document.getElementById('standeeSection').innerText = sectionName;
    document.getElementById('standeeCap').innerText = capacity;
    document.getElementById('standeeLinkTxt').innerText = 'Target: ' + linkUrl;
    document.getElementById('standeeOpenBtn').href = linkUrl;
    
    const standeeModal = new bootstrap.Modal(document.getElementById('qrStandeeModal'));
    standeeModal.show();
}
</script>
@endpush
