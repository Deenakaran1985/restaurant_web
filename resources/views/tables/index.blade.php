@extends('layouts.admin')

@section('title', 'Floor Map & Table Grid')
@section('page_title', 'Interactive Dining Room & Table Turnaround Monitor')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center shadow-lg" role="alert">
    <i class="bi bi-check-circle-fill me-3 fs-3 animate-pulse"></i>
    <div>
        <strong class="text-white d-block">Table Architecture & Turnaround State Updated!</strong>
        <span class="fs-7">{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex gap-2 flex-wrap">
        <span class="badge badge-emerald fs-8 px-3 py-2"><i class="bi bi-circle-fill me-1"></i> Vacant (Ready)</span>
        <span class="badge badge-azure fs-8 px-3 py-2"><i class="bi bi-person-fill me-1"></i> Seated (Browsing)</span>
        <span class="badge badge-amber fs-8 px-3 py-2 animate-pulse"><i class="bi bi-fire me-1"></i> Ordered (KOT Fired)</span>
        <span class="badge badge-emerald fs-8 px-3 py-2 animate-pulse" style="background: rgba(16, 185, 129, 0.25); color: #34d399;"><i class="bi bi-bell-fill me-1"></i> Ready to Serve (Food Up)</span>
        <span class="badge badge-crimson fs-8 px-3 py-2"><i class="bi bi-receipt me-1"></i> Bill Requested</span>
        <span class="badge badge-purple fs-8 px-3 py-2" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;"><i class="bi bi-bookmark-star-fill me-1"></i> VIP Reserved</span>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-modern-primary btn-sm fw-bold shadow-lg px-3" data-bs-toggle="modal" data-bs-target="#addTableModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Add Table Slot
        </button>
        <a href="{{ route('tables.config') }}" class="btn btn-modern-outline btn-sm fw-bold shadow px-3"><i class="bi bi-list-columns-reverse me-1"></i> Master Roster View</a>
    </div>
</div>

<div class="row g-4 mb-5">
    @foreach($tables as $table)
        @php
            $borderColor = 'border-secondary';
            $bgIcon = 'rgba(100, 116, 139, 0.15)';
            $textIcon = 'text-secondary';
            $badgeClass = 'bg-secondary';
            $badgeLabel = strtoupper($table->status);
            
            if($table->status == 'vacant') {
                $borderColor = 'border-success';
                $bgIcon = 'rgba(16, 185, 129, 0.15)';
                $textIcon = 'text-success';
                $badgeClass = 'badge-emerald';
                $badgeLabel = 'VACANT READY';
            } elseif($table->status == 'seated') {
                $borderColor = 'border-info';
                $bgIcon = 'rgba(56, 189, 248, 0.15)';
                $textIcon = 'text-info';
                $badgeClass = 'badge-azure';
                $badgeLabel = 'SEATED GUESTS';
            } elseif($table->status == 'ordered' || $table->status == 'occupied') {
                $borderColor = 'border-warning';
                $bgIcon = 'rgba(245, 158, 11, 0.15)';
                $textIcon = 'text-warning';
                $badgeClass = 'badge-amber';
                $badgeLabel = 'ORDER FIRED';
            } elseif($table->status == 'ready_to_serve') {
                $borderColor = 'border-success border-2';
                $bgIcon = 'rgba(16, 185, 129, 0.3)';
                $textIcon = 'text-success animate-bounce';
                $badgeClass = 'badge-emerald animate-pulse';
                $badgeLabel = '🍽️ READY TO SERVE';
            } elseif($table->status == 'billed') {
                $borderColor = 'border-danger';
                $bgIcon = 'rgba(239, 68, 68, 0.15)';
                $textIcon = 'text-danger';
                $badgeClass = 'badge-crimson';
                $badgeLabel = 'BILL REQUESTED';
            } elseif($table->status == 'reserved') {
                $borderColor = 'border-purple';
                $bgIcon = 'rgba(168, 85, 247, 0.15)';
                $textIcon = 'text-warning';
                $badgeClass = 'badge-purple';
                $badgeLabel = 'RESERVED VIP';
            } else {
                $badgeLabel = 'MAINTENANCE';
            }
        @endphp

        <div class="col-6 col-md-4 col-xl-3">
            <div class="glass-card p-4 hover-lift text-center h-100 d-flex flex-column justify-content-between position-relative {{ $borderColor }} border-opacity-50 shadow" style="transition: all 0.25s ease;">
                
                <!-- Top Header Bar with Section & Turnaround Configuration Gear -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-dark text-secondary border border-secondary border-opacity-25 fs-8 font-monospace text-truncate" style="max-width: 70%;">
                        {{ $table->section->name ?? 'Main Lounge' }}
                    </span>

                    <!-- Interactive Config & Status Switch Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-light p-0 fs-5 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Table Config & Turnaround Action">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark shadow-lg border border-secondary border-opacity-50 rounded-4 p-2 font-monospace fs-8" style="min-width: 220px; z-index: 1055;">
                            <li class="px-3 py-1 text-muted fw-bold fs-9 text-uppercase">Turnaround Status Switch</li>
                            <li>
                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="vacant">
                                    <button type="submit" class="dropdown-item text-success py-2 rounded-3"><i class="bi bi-circle-fill me-2"></i> 🟢 Mark Vacant (Ready)</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="seated">
                                    <button type="submit" class="dropdown-item text-info py-2 rounded-3"><i class="bi bi-person-fill me-2"></i> 🔵 Mark Seated (Menu Sent)</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="ordered">
                                    <button type="submit" class="dropdown-item text-warning py-2 rounded-3"><i class="bi bi-fire me-2"></i> 🟡 Mark Ordered (Active KOT)</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="ready_to_serve">
                                    <button type="submit" class="dropdown-item text-success py-2 rounded-3 fw-bold"><i class="bi bi-bell-fill me-2 text-warning"></i> 🟢🍽️ Mark Ready to Serve (Food Up)</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="billed">
                                    <button type="submit" class="dropdown-item text-danger py-2 rounded-3"><i class="bi bi-receipt me-2"></i> 🔴 Bill Requested (Print POS)</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('tables.update_status', $table->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="reserved">
                                    <button type="submit" class="dropdown-item text-light py-2 rounded-3" style="color: #c084fc !important;"><i class="bi bi-bookmark-star me-2"></i> 🟣 Reserve Table for VIP</button>
                                </form>
                            </li>
                            <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                            <li class="px-3 py-1 text-muted fw-bold fs-9 text-uppercase">Table Slot Config</li>
                            <li>
                                <button type="button" class="dropdown-item text-warning py-2 rounded-3" onclick="openEditModal({{ $table->id }}, '{{ $table->table_number }}', {{ $table->capacity }}, {{ $table->section_id ?? 1 }}, '{{ $table->status }}')">
                                    <i class="bi bi-sliders me-2"></i> ⚙️ Configure Capacity & Zone
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item text-info py-2 rounded-3" onclick="showQrStandee('{{ $table->table_number }}', '{{ route('qr.menu', $table->id) }}', '{{ $table->section->name ?? 'Main Lounge' }}', '{{ $table->capacity }}')">
                                    <i class="bi bi-printer me-2"></i> 🖨️ Tabletop QR Standee
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Table Center Graphic -->
                <div class="py-3">
                    <div class="p-4 mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" 
                         style="width:84px; height:84px; background: {{ $bgIcon }}; border: 1px solid rgba(255,255,255,0.05);">
                        <i class="bi bi-people-fill fs-2 {{ $textIcon }}"></i>
                    </div>
                    <h3 class="fw-bolder font-heading mb-1 text-white tracking-wide">{{ $table->table_number }}</h3>
                    <div class="text-secondary font-monospace fs-7"><i class="bi bi-person-check text-info me-1"></i> Capacity: <strong class="text-warning">{{ $table->capacity }}</strong> Seats</div>
                </div>

                @if($table->status == 'ordered' || $table->status == 'ready_to_serve')
                    <form action="{{ route('tables.serve_invoice', $table->id) }}" method="POST" class="mt-2 mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bolder py-2 shadow-lg rounded-4 d-flex align-items-center justify-content-center animate-pulse" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bi bi-bell-fill me-2 text-warning fs-6"></i> Serve Food & Fire Invoice to Cashier
                        </button>
                    </form>
                @endif

                <!-- Footer Status Badge & POS Actions -->
                <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="badge {{ $badgeClass }} font-monospace px-2 py-1 fs-9">{{ $badgeLabel }}</span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-modern-outline px-2 py-1 fs-8 font-monospace" onclick="showQrStandee('{{ $table->table_number }}', '{{ route('qr.menu', $table->id) }}', '{{ $table->section->name ?? 'Main Lounge' }}', '{{ $table->capacity }}')" title="Open QR Standee Card">
                            <i class="bi bi-qr-code text-warning"></i>
                        </button>
                        <a href="{{ url('/pos') }}" class="btn btn-sm {{ $table->status == 'vacant' ? 'btn-modern-outline' : 'btn-modern-primary' }} px-3 py-1 fs-8 fw-bold">
                            {{ $table->status == 'vacant' ? 'Seat & Order' : 'POS Terminal' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add New Dining Table Slot Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-white">
                    <i class="bi bi-plus-circle-fill text-warning me-2"></i> Register New Dining Table Slot
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <p class="text-muted fs-7 mb-4">Create and attach a new dining table to the floor grid. A custom table-side QR self-ordering endpoint is instantly generated upon creation.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Table Number / Identifier</label>
                        <input type="text" name="table_number" class="form-control bg-dark text-white font-monospace fs-5 border-secondary border-opacity-50" placeholder="e.g. ROOF-05 or T-20" required autofocus>
                        <small class="text-muted fs-8">Example identifiers: T-14, ROOF-05, GARDEN-02, BANQ-01.</small>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Seating Capacity (Guests)</label>
                            <input type="number" name="capacity" class="form-control bg-dark text-warning font-monospace fs-5 border-secondary border-opacity-50" value="4" min="1" max="100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Initial State</label>
                            <select name="status" class="form-select bg-dark text-success border-secondary border-opacity-50 fs-6 py-2">
                                <option value="vacant" selected>🟢 Vacant (Ready)</option>
                                <option value="seated">🔵 Seated Guests</option>
                                <option value="reserved">🟣 Reserved VIP</option>
                                <option value="maintenance">🔴 Maintenance Offline</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Assign to Dining Floor Zone</label>
                        <select name="section_id" class="form-select bg-dark text-light border-secondary border-opacity-50 font-monospace fs-6 py-2" required>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                            @if(empty($sections) || count($sections) == 0)
                                <option value="1">Main Dining Lounge</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary px-4 fw-bold shadow-lg">
                        <i class="bi bi-check-lg me-1"></i> REGISTER TABLE SLOT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Configure / Edit Existing Table Slot Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-warning border-opacity-50 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-warning">
                    <i class="bi bi-sliders text-warning me-2"></i> Configure Table & Seating Capacity
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTableForm" action="" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Table Number / Identifier</label>
                        <input type="text" name="table_number" id="editTableNumber" class="form-control bg-dark text-white font-monospace fs-5 border-secondary border-opacity-50" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Seating Capacity (Guests)</label>
                            <input type="number" name="capacity" id="editCapacity" class="form-control bg-dark text-warning font-monospace fs-5 border-secondary border-opacity-50" min="1" max="100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Turnaround State</label>
                            <select name="status" id="editStatus" class="form-select bg-dark text-info border-secondary border-opacity-50 fs-6 py-2">
                                <option value="vacant">🟢 Vacant (Ready)</option>
                                <option value="seated">🔵 Seated Guests</option>
                                <option value="ordered">🟡 Ordered (Active KOT)</option>
                                <option value="billed">🔴 Bill Requested</option>
                                <option value="reserved">🟣 Reserved VIP</option>
                                <option value="maintenance">🛠️ Maintenance Offline</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Dining Floor Section</label>
                        <select name="section_id" id="editSectionId" class="form-select bg-dark text-light border-secondary border-opacity-50 font-monospace fs-6 py-2" required>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3 justify-content-between">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-3 fs-8 font-monospace" onclick="confirmDeleteTable()">
                        <i class="bi bi-trash-fill me-1"></i> Decommission Slot
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark px-4 rounded-pill fw-bold shadow-lg">
                            <i class="bi bi-save me-1"></i> UPDATE CONFIG
                        </button>
                    </div>
                </div>
            </form>
            
            <form id="deleteTableForm" action="" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>

<!-- Printable Acrylic QR Standee Visualizer Modal -->
<div class="modal fade" id="qrStandeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-info border-opacity-50 text-light rounded-5 p-4 text-center shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-info">
                    <i class="bi bi-qr-code text-white me-2"></i> Tabletop QR Standee Card
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <div class="p-4 rounded-5 bg-dark d-inline-block border border-secondary border-opacity-50 shadow-lg mb-3" style="max-width: 320px;">
                    <span class="badge bg-warning text-dark font-monospace fw-bold px-3 py-1 mb-2 fs-8 text-uppercase" id="standeeSection">MAIN DINING LOUNGE</span>
                    <h1 class="fw-bolder font-monospace text-white mb-1 display-5" id="standeeTableNum">T-01</h1>
                    <p class="fs-8 text-muted mb-3">Scan below to access digital interactive menu & order directly to your table without waiting for a waiter!</p>
                    
                    <div class="p-3 bg-white rounded-4 mb-3 shadow d-inline-block">
                        <i class="bi bi-qr-code text-dark" style="font-size: 7.5rem;"></i>
                    </div>
                    
                    <div class="d-flex justify-content-around text-muted fs-8 font-monospace border-top border-secondary border-opacity-25 pt-2">
                        <span><i class="bi bi-people-fill text-info me-1"></i> <span id="standeeCap">4</span> Seats</span>
                        <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Instant POS Sync</span>
                    </div>
                </div>
                <p class="text-secondary fs-8 font-monospace mb-0" id="standeeLinkTxt">Link: http://192.168.32.249:8107/menu/qr/1</p>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-25 pt-3 justify-content-center">
                <a href="#" target="_blank" id="standeeOpenBtn" class="btn btn-outline-info rounded-pill px-4 fw-bold me-2"><i class="bi bi-box-arrow-up-right me-1"></i> Test Guest QR Portal</a>
                <button type="button" class="btn btn-modern-primary rounded-pill px-4 fw-bold shadow-lg" onclick="window.print()"><i class="bi bi-printer-fill me-1"></i> PRINT STAND CARD</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentEditingId = null;

function openEditModal(tableId, tableNumber, capacity, sectionId, status) {
    currentEditingId = tableId;
    document.getElementById('editTableNumber').value = tableNumber;
    document.getElementById('editCapacity').value = capacity;
    document.getElementById('editSectionId').value = sectionId;
    document.getElementById('editStatus').value = status;
    
    document.getElementById('editTableForm').action = '/tables/config/' + tableId + '/update';
    document.getElementById('deleteTableForm').action = '/tables/config/' + tableId + '/delete';
    
    const editModal = new bootstrap.Modal(document.getElementById('editTableModal'));
    editModal.show();
}

function confirmDeleteTable() {
    if (confirm("Are you sure you want to permanently decommission this table slot from the dining room?")) {
        document.getElementById('deleteTableForm').submit();
    }
}

function showQrStandee(tableNum, linkUrl, sectionName, capacity) {
    document.getElementById('standeeTableNum').innerText = tableNum;
    document.getElementById('standeeSection').innerText = sectionName;
    document.getElementById('standeeCap').innerText = capacity;
    document.getElementById('standeeLinkTxt').innerText = 'Target Endpoint: ' + linkUrl;
    document.getElementById('standeeOpenBtn').href = linkUrl;
    
    const standeeModal = new bootstrap.Modal(document.getElementById('qrStandeeModal'));
    standeeModal.show();
}
</script>
@endpush
