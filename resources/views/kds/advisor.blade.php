@extends('layouts.admin')

@section('title', 'AI Kitchen Production Advisor & Prep Forecast')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Ribbon -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">
                <i class="bi bi-cpu-fill me-2"></i> NEURAL CULINARY RUSH FORECASTING
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">AI Kitchen Prep Advisor & Mise-en-Place</h2>
            <p class="text-muted mb-0 fs-6">Predicting order velocity from active table floor saturation, VIP Patron CRM bookings, and live COGS inventory depletion.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/kds') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-tv-fill me-1"></i> Kitchen KDS Screen
            </a>
            <button class="btn btn-modern-primary shadow-lg" onclick="vocalizePrepCallout()">
                <i class="bi bi-volume-up-fill me-1"></i> Vocalize Prep Callout
            </button>
        </div>
    </div>

    <!-- AI Intelligence Alert Bar -->
    <div class="card glass-card border-0 rounded-5 p-4 mb-4" style="background: radial-gradient(circle at 0% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 60%); border-left: 5px solid #3b82f6 !important;">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 me-3 fs-2">
                        <i class="bi bi-fire text-warning"></i>
                    </div>
                    <div>
                        <span class="fs-8 text-muted d-block text-uppercase fw-bold">Forecasted Rush Window</span>
                        <h4 class="fw-bolder font-monospace text-white mb-0">{{ $aiInsights['rush_window'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 border-start border-secondary border-opacity-25 ps-md-4">
                <span class="fs-8 text-muted d-block text-uppercase fw-bold">Current Table Floor Load</span>
                <div class="d-flex align-items-center mt-1">
                    <h3 class="fw-bolder font-monospace text-info mb-0 me-2">{{ $aiInsights['floor_saturation_pct'] }}%</h3>
                    <span class="badge badge-emerald">Seated & Active</span>
                </div>
            </div>
            <div class="col-md-4 border-start border-secondary border-opacity-25 ps-md-4">
                <span class="fs-8 text-muted d-block text-uppercase fw-bold">Recommended Station Staffing</span>
                <div class="fw-bold text-light mt-1"><i class="bi bi-person-badge text-primary me-2"></i> {{ $aiInsights['recommended_staffing'] }}</div>
                <small class="text-success fs-8 font-monospace"><i class="bi bi-graph-up me-1"></i> AI Confidence: {{ $aiInsights['confidence_score'] }}%</small>
            </div>
        </div>
    </div>

    <!-- Prep Progress Bar -->
    <div class="card glass-card border-0 rounded-4 p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <i class="bi bi-check2-all text-success fs-4 me-3"></i>
            <div>
                <strong class="text-white">Line Station Mise-en-Place Readiness</strong>
                <span class="text-muted fs-8 d-block">Mark items as thawed and portioned into line coolers before peak dining hours.</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3" style="min-width: 280px;">
            <div class="progress flex-grow-1 bg-dark" style="height: 10px; border-radius: 6px;">
                <div class="progress-bar bg-success" id="prepProgressBar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="font-monospace fw-bold text-light" id="prepProgressTxt">1 / 5 Ready</span>
        </div>
    </div>

    <!-- Recommended Mise-en-Place Prep Catalog -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-journal-check me-2 text-primary"></i> Dynamic Kitchen Station Prep Sheet</h5>
            <span class="badge bg-warning text-dark fw-bolder font-monospace px-3 py-2">{{ $aiInsights['total_prep_portions'] }} Total Portions Recommended</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider">
                        <th class="py-3 ps-4" style="width: 40px;">Ready</th>
                        <th class="py-3">Menu Item & Culinary Station</th>
                        <th class="py-3 text-center">Recommended Portions</th>
                        <th class="py-3 text-center">Estimated Prep SLA</th>
                        <th class="py-3">Mapped Raw Ingredient & Stock</th>
                        <th class="py-3 text-center">Velocity Status</th>
                        <th class="py-3 pe-4 text-end">Line Cook Action</th>
                    </tr>
                </thead>
                <tbody id="prepTableBody">
                    @foreach($prepRecommendations as $index => $prep)
                        <tr class="prep-row" id="row-{{ $prep['id'] }}">
                            <td class="py-3 ps-4 text-center">
                                <input class="form-check-input prep-checkbox fs-5 bg-dark border-secondary border-opacity-50" type="checkbox" onchange="updatePrepReadiness()" {{ $index === 0 ? 'checked' : '' }}>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-light fs-6">{{ $prep['dish_name'] }}</div>
                                <div class="fs-8 text-info font-monospace">{{ $prep['station_assignment'] }} | Category: {{ $prep['category'] }}</div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bolder font-monospace text-warning fs-4">{{ $prep['recommended_portions'] }}</span>
                                <span class="fs-8 text-muted d-block">portions ready-in-cooler</span>
                            </td>
                            <td class="text-center font-monospace text-light">
                                <i class="bi bi-clock-history text-primary me-1"></i> {{ $prep['prep_sla_min'] }} mins
                            </td>
                            <td>
                                <div class="fw-medium text-light fs-7">{{ $prep['primary_raw_material'] }}</div>
                                <div class="fs-8 text-muted font-monospace">Current Store Stock: <strong class="text-success">{{ $prep['stock_on_hand'] }}</strong></div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $prep['badge_color'] }} px-3 py-2 fs-8 font-monospace">
                                    <i class="bi bi-lightning-fill me-1"></i> {{ $prep['status'] }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" onclick="togglePrepRow(this)">
                                    <i class="bi bi-check-lg me-1"></i> Mark Done
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePrepReadiness() {
    const checkboxes = document.querySelectorAll('.prep-checkbox');
    let checkedCount = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) {
            checkedCount++;
            cb.closest('tr').style.opacity = '0.5';
            cb.closest('tr').style.background = 'rgba(16, 185, 129, 0.05)';
        } else {
            cb.closest('tr').style.opacity = '1';
            cb.closest('tr').style.background = '';
        }
    });

    const total = checkboxes.length || 1;
    const pct = Math.round((checkedCount / total) * 100);
    
    document.getElementById('prepProgressBar').style.width = pct + '%';
    document.getElementById('prepProgressTxt').innerText = `${checkedCount} / ${total} Ready (${pct}%)`;
}

function togglePrepRow(btn) {
    const row = btn.closest('tr');
    const cb = row.querySelector('.prep-checkbox');
    cb.checked = !cb.checked;
    
    if (cb.checked) {
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success', 'text-white');
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Portioned';
    } else {
        btn.classList.remove('btn-success', 'text-white');
        btn.classList.add('btn-outline-success');
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Mark Done';
    }
    
    updatePrepReadiness();
}

function vocalizePrepCallout() {
    if (!('speechSynthesis' in window)) {
        alert('Your browser does not support HTML5 speech vocalization.');
        return;
    }
    
    const msg = new SpeechSynthesisUtterance();
    msg.text = "Attention kitchen line brigade: AI Prep Advisor recommends staging thirty woodfired pizza dough balls and twenty gourmet burgers in line station coolers prior to the nineteen thirty peak dinner rush. All line cooks confirm station readiness.";
    msg.rate = 0.95;
    msg.pitch = 1.0;
    window.speechSynthesis.speak(msg);
    
    // Show visual acoustic indicator
    const div = document.createElement('div');
    div.style.position = 'fixed';
    div.style.top = '20px';
    div.style.right = '20px';
    div.style.zIndex = 9999;
    div.innerHTML = `
        <div class="alert alert-info shadow-lg rounded-4 d-flex align-items-center border-info p-3 text-light bg-dark">
            <i class="bi bi-volume-up-fill text-info fs-3 me-3 animate-pulse"></i>
            <div>
                <strong class="text-white d-block">Vocalizing AI Kitchen Callout...</strong>
                <span class="fs-8 text-muted">Acoustic broadcast sent to kitchen station audio speakers.</span>
            </div>
        </div>
    `;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 6000);
}

// Initial calculation on page load
document.addEventListener('DOMContentLoaded', updatePrepReadiness);
</script>
@endpush
