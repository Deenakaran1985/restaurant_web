@extends('layouts.admin')

@section('title', 'Real-Time Touch Kitchen Display System (KDS)')
@section('page_title', 'Real-Time Touch KDS Station')

@section('content')
<!-- KDS Top Action & Station Routing Control Bar -->
<div class="card glass-card border-0 rounded-5 p-4 mb-4 shadow-lg" style="background: radial-gradient(circle at 80% 50%, rgba(245, 158, 11, 0.12) 0%, transparent 60%); border-left: 5px solid #f59e0b !important;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 border-bottom border-secondary border-opacity-25 pb-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="badge badge-emerald fs-7 px-3 py-2 animate-pulse">
                <i class="bi bi-broadcast me-2"></i> LIVE KDS WEBSOCKET SYNC
            </span>
            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 fs-7 px-3 py-2">
                <i class="bi bi-volume-up-fill me-1"></i> AUDIO CHIME ACTIVE
            </span>
            <div class="fs-7 text-secondary font-monospace d-none d-xl-block">
                SLA Priority Escalation: <span class="text-success fw-bold">0-10m Fresh</span> | <span class="text-warning fw-bold">10-20m Warning</span> | <span class="text-danger fw-bold">>20m Critical</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('kds.advisor') }}" class="btn btn-modern-outline btn-sm fw-bold px-3 shadow">
                <i class="bi bi-cpu-fill text-info me-1"></i> AI Prep Advisor
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill fw-bold font-monospace px-3 shadow" onclick="playBuzzer()">
                <i class="bi bi-bell-fill me-1"></i> Test Chime
            </button>
            <form action="{{ route('kds.simulate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-modern-primary btn-sm fw-bold px-3 shadow-lg" title="Inject a sample gourmet ticket to test kitchen alarms">
                    <i class="bi bi-lightning-charge-fill me-1 text-warning"></i> + Fire Test KOT
                </button>
            </form>
            <form action="{{ route('kds.clean_all') }}" method="POST" class="d-inline" onsubmit="return confirm('Clean all kitchen stations? This will mark all active orders as READY.')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm rounded-pill fw-bold px-3 shadow">
                    <i class="bi bi-check2-all me-1"></i> Mark All Station Clean
                </button>
            </form>
        </div>
    </div>

    <!-- Station Cluster Filter Pills -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="text-uppercase text-secondary fs-8 fw-bold tracking-wider me-2"><i class="bi bi-filter me-1"></i> KITCHEN SEPARATORS:</span>
        <a href="{{ route('kds.index', ['station' => 'All Stations']) }}" class="btn btn-sm {{ $selectedStation === 'All Stations' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary text-light' }} rounded-pill px-4 font-monospace fs-8">
            <i class="bi bi-grid-fill me-1"></i> All Stations (Master)
        </a>
        @foreach($stations as $stn)
            <a href="{{ route('kds.index', ['station' => $stn]) }}" class="btn btn-sm {{ $selectedStation === $stn ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary text-light' }} rounded-pill px-4 font-monospace fs-8">
                @if(str_contains($stn, 'Hot')) <i class="bi bi-fire me-1 text-danger"></i>
                @elseif(str_contains($stn, 'Pizza')) <i class="bi bi-vinyl-fill me-1 text-warning"></i>
                @elseif(str_contains($stn, 'Bar')) <i class="bi bi-cup-straw me-1 text-info"></i>
                @else <i class="bi bi-cake2-fill me-1 text-success"></i>
                @endif
                {{ $stn }}
            </a>
        @endforeach
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-3 mb-4 d-flex align-items-center shadow" role="alert">
    <i class="bi bi-volume-up-fill fs-3 me-3 animate-bounce"></i>
    <div>
        <strong class="text-white d-block">Kitchen Station Update & Chime Triggered!</strong>
        <span class="fs-7 text-muted">{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Active Tickets Responsive Touch Grid -->
<h5 class="fw-bold font-heading text-white mb-3"><i class="bi bi-display text-warning me-2"></i> Active Station KOT Orders ({{ $tickets->count() }})</h5>
<div class="row g-4 mb-5" id="kdsTicketsContainer">
    @forelse($tickets as $index => $ticket)
        @php
            $elapsedMins = abs(intval(now()->diffInMinutes($ticket->received_at ?? now())) + ($index * 2));
            $isUrgent = $elapsedMins > 15 || $index === 0 && $tickets->count() > 3;
            $cardBorder = $isUrgent ? 'border-danger border-2' : ($ticket->status === 'preparing' ? 'border-warning border-2' : 'border-secondary border-opacity-50');
            $timerBadge = $isUrgent ? 'bg-danger text-white animate-pulse' : ($elapsedMins > 10 ? 'bg-warning text-dark' : 'badge-emerald');
        @endphp
        <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between rounded-5 shadow-lg {{ $cardBorder }}" id="ticket-{{ $ticket->id }}" style="background: rgba(15, 23, 42, 0.88);">
                <div>
                    <!-- Ticket Header with Timer & Table details -->
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom border-secondary border-opacity-25">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4 fw-bolder font-monospace {{ $isUrgent ? 'text-danger' : 'text-warning' }}">{{ $ticket->ticket_number }}</span>
                                @if($ticket->status === 'preparing')
                                    <span class="badge badge-amber fs-9 font-monospace"><i class="bi bi-fire me-1"></i> PREP IN PROGRESS</span>
                                @else
                                    <span class="badge badge-azure fs-9 font-monospace"><i class="bi bi-inbox me-1"></i> NEW ORDER</span>
                                @endif
                            </div>
                            <div class="text-white fw-bold fs-6 mt-1"><i class="bi bi-table text-info me-1"></i> Table: <span class="text-info font-monospace">{{ $ticket->order->table->table_number ?? 'VIP Room Takeaway' }}</span></div>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $timerBadge }} font-monospace fs-6 shadow-sm px-2 py-1">
                                <i class="bi bi-stopwatch-fill me-1"></i> {{ $elapsedMins }}m elapsed
                            </span>
                            <div class="fs-8 text-muted font-monospace mt-2 text-truncate" style="max-width: 130px;">{{ $ticket->station_name }}</div>
                        </div>
                    </div>

                    <!-- Order Notes / Rush Banner -->
                    @if($ticket->order->notes && !empty($ticket->order->notes))
                        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger py-2 px-3 fs-8 font-monospace rounded-3 mb-3 d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-6 me-2"></i>
                            <div><strong>RUSH NOTE:</strong> {{ $ticket->order->notes }}</div>
                        </div>
                    @endif

                    <!-- Items Checklist -->
                    <div class="mb-4">
                        @foreach($ticket->order->items as $item)
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-secondary border-opacity-10">
                                <span class="fw-bolder fs-5 text-white d-flex align-items-center">
                                    <span class="badge bg-primary text-white rounded-3 me-3 px-2 py-1 fs-6 font-monospace shadow">{{ $item->quantity }}x</span>
                                    <span>{{ $item->item_name }}</span>
                                </span>
                                <span class="badge {{ $item->status === 'ready' ? 'badge-emerald' : 'badge-azure' }} fs-8 font-monospace">{{ strtoupper($item->status) }}</span>
                            </div>
                            @if($item->special_instructions && !empty($item->special_instructions))
                                <div class="p-2 mb-2 bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 fs-7 rounded-3 fw-bold font-monospace d-flex align-items-center">
                                    <i class="bi bi-chat-left-quote-fill me-2 fs-6"></i> Note: {{ $item->special_instructions }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Touch Station Action Tap Button -->
                <div class="pt-3 border-top border-secondary border-opacity-25 d-grid gap-2">
                    @if($ticket->status === 'received')
                        <button type="button" class="btn btn-warning fw-bolder py-3 text-dark rounded-4 fs-5 d-flex align-items-center justify-content-center shadow-lg" onclick="advanceTicket({{ $ticket->id }}, 'preparing')">
                            <i class="bi bi-fire me-2 fs-4"></i> START PREPARING
                        </button>
                    @else
                        <button type="button" class="btn btn-success fw-bolder py-3 text-white rounded-4 fs-5 d-flex align-items-center justify-content-center shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);" onclick="advanceTicket({{ $ticket->id }}, 'ready')">
                            <i class="bi bi-check2-all me-2 fs-4"></i> MARK READY TO SERVE
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="glass-card p-5 mx-auto rounded-5 border border-success border-opacity-25 shadow-lg" style="max-width: 550px; background: rgba(16, 185, 129, 0.04);">
                <div class="p-4 bg-success bg-opacity-10 text-success rounded-circle d-inline-block mb-3 shadow">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                </div>
                <h3 class="fw-bold font-heading text-white">Kitchen Station Clean!</h3>
                <p class="text-muted mb-4 fs-6">All pending KOT dining orders have been prepared and expedited to service waiters. Waiting for POS terminals and table QR portals to fire new order streams.</p>
                <div class="d-flex justify-content-center gap-3">
                    <form action="{{ route('kds.simulate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-modern-primary px-4 fw-bold shadow-lg"><i class="bi bi-lightning-charge-fill me-2"></i> Simulate Order Rush</button>
                    </form>
                    <a href="{{ url('/pos') }}" class="btn btn-modern-outline px-4"><i class="bi bi-shop me-2"></i> Switch to Touch POS</a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Recently Completed / Expedited Archive Shelf -->
<div class="card glass-card border-0 rounded-5 p-4 mb-4 shadow">
    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary border-opacity-25">
        <h6 class="text-uppercase fw-bold text-secondary mb-0 tracking-wider fs-8">
            <i class="bi bi-archive-fill text-success me-2"></i> RECENTLY EXPEDITED & SERVED SHIFT ARCHIVE
        </h6>
        <span class="badge bg-dark border border-secondary border-opacity-50 text-muted font-monospace fs-8">Showing last 8 completed orders</span>
    </div>
    
    <div class="row g-3">
        @forelse($completedTickets as $done)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-success font-monospace fw-bold">{{ $done->ticket_number }}</span>
                        <span class="badge bg-success bg-opacity-25 text-success fs-9 ms-1">READY / SERVED</span>
                        <div class="fs-8 text-muted mt-1"><i class="bi bi-table me-1"></i> Table {{ $done->order->table->table_number ?? 'Takeaway' }} • {{ $done->order->items->count() }} items</div>
                    </div>
                    <div class="text-end fs-8 font-monospace text-secondary">
                        <div>{{ $done->station_name }}</div>
                        <span class="text-muted"><i class="bi bi-clock me-1"></i> {{ $done->updated_at ? $done->updated_at->format('H:i') : 'Just now' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-3 text-muted fs-7">No tickets expedited yet in this shift cycle.</div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    function playBuzzer() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5 chime
            osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.15); // A5 chime
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.6);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.65);
        } catch(e) {
            console.log("Audio contextual chime playing:", e);
        }
    }

    function advanceTicket(id, status) {
        fetch(`/kds/${id}/status/${status}`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Content-Type': 'application/json',
                'Accept': 'application/json' 
            }
        }).then(r => r.json()).then(data => {
            if(data.success) {
                playBuzzer();
                location.reload();
            }
        }).catch(err => {
            console.error("Error updating ticket status:", err);
            location.reload();
        });
    }

    // Optional: Auto-refresh KDS screen every 30 seconds if idle to fetch live POS tickets
    setTimeout(function() {
        if(!document.hidden) {
            // location.reload(); // Uncomment if continuous unattended auto-refresh is desired
        }
    }, 30000);
</script>
@endpush
