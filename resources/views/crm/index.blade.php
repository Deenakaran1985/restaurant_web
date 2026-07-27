@extends('layouts.admin')

@section('title', 'Patron CRM & VIP Loyalty Rewards')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-purple bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-semibold" style="background: rgba(168, 85, 247, 0.15) !important; color: #c084fc !important;">
                <i class="bi bi-person-hearts me-2"></i> ENTERPRISE GUEST RELATIONSHIP MANAGEMENT
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Patron CRM & VIP Loyalty Directory</h2>
            <p class="text-muted mb-0 fs-6">Manage high-value guest dining history, reward loyalty point wallets, and track critical table dietary allergies.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            <a href="{{ url('/pos') }}" class="btn btn-modern-outline shadow">
                <i class="bi bi-shop me-1"></i> Open Touch POS
            </a>
            <button type="button" class="btn btn-modern-primary shadow-lg fw-bold px-4" data-bs-toggle="modal" data-bs-target="#newPatronModal">
                <i class="bi bi-person-plus-fill me-1"></i> Enroll VIP Guest
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-4 mb-4 d-flex align-items-center shadow-lg" role="alert">
        <i class="bi bi-gift-fill me-3 fs-3 animate-pulse"></i>
        <div>
            <strong class="text-white d-block fs-5">VIP Patron Loyalty Action Recorded!</strong>
            <span class="fs-7 text-muted">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Executive CRM Portfolio KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #38bdf8 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Active VIP Patrons</span>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 fs-4"><i class="bi bi-people-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-white">{{ $crmStats['total_patrons'] }}</h2>
                    <p class="fs-8 text-success mb-0"><i class="bi bi-shield-check me-1"></i> Verified across POS & Table QR stands</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Circulating VIP Points</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4"><i class="bi bi-star-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-warning">{{ number_format($crmStats['total_points_in_circulation']) }} <span class="fs-6 text-muted">pts</span></h2>
                    <p class="fs-8 text-muted mb-0">Redeemable against future dining bills (1 pt = ₹1)</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Platinum & Gold Tier</span>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4"><i class="bi bi-trophy-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-success">{{ $crmStats['platinum_count'] }} Platinum / {{ $crmStats['gold_count'] }} Gold</h2>
                    <p class="fs-8 text-muted mb-0">Highest spending lifetime dining tiers</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #a855f7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Cumulative Lifetime Spend</span>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fs-4"><i class="bi bi-wallet2"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-info">{{ $settings->currency_symbol }} {{ number_format($crmStats['total_lifetime_spend'], 2) }}</h2>
                    <p class="fs-8 text-muted mb-0">Total revenue generated by loyalty program</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Patron Directory Table -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5 shadow-lg">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-table me-2 text-info"></i> VIP Guest Loyalty Ledger</h5>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-info rounded-pill px-3 font-monospace" data-bs-toggle="modal" data-bs-target="#newPatronModal">
                    <i class="bi bi-person-plus-fill text-warning me-1"></i> + Enroll Guest
                </button>
                <div class="input-group input-group-sm w-auto">
                    <span class="input-group-text bg-dark border-secondary border-opacity-25 text-light"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-dark text-light border-secondary border-opacity-25" placeholder="Filter by phone or name...">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider border-bottom border-secondary border-opacity-50">
                        <th class="py-3 ps-4">Patron Name & Contact</th>
                        <th class="py-3 text-center">VIP Dining Tier</th>
                        <th class="py-3 text-center">Loyalty Points Balance</th>
                        <th class="py-3 text-center">Lifetime Spend</th>
                        <th class="py-3">Culinary Favorites & Dietary Notes</th>
                        <th class="py-3 pe-4 text-end">Action / Awards</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patrons as $p)
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <td class="py-3 ps-4">
                                <div class="fw-bold text-light fs-5 d-flex align-items-center">
                                    {{ $p->name }}
                                    @if($p->tier === 'platinum_vip') <i class="bi bi-patch-check-fill text-warning ms-2" title="Platinum Verified"></i> @endif
                                </div>
                                <div class="fs-8 text-muted font-monospace"><i class="bi bi-telephone me-1 text-secondary"></i> {{ $p->phone }} | {{ $p->email ?? 'No email' }}</div>
                            </td>
                            <td class="text-center">
                                @if($p->tier === 'platinum_vip')
                                    <span class="badge badge-purple px-3 py-2 fs-7 font-monospace" style="background: rgba(168, 85, 247, 0.2); color: #c084fc;"><i class="bi bi-gem me-1 text-warning"></i> PLATINUM VIP</span>
                                @elseif($p->tier === 'gold')
                                    <span class="badge badge-amber px-3 py-2 fs-7 font-monospace"><i class="bi bi-award-fill me-1"></i> GOLD TIER</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 px-3 py-2 fs-8 font-monospace"><i class="bi bi-person-fill me-1"></i> SILVER MEMBER</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-bolder font-monospace text-warning fs-4">{{ number_format($p->loyalty_points) }}</span>
                                <span class="fs-8 text-muted d-block font-monospace">≈ {{ $settings->currency_symbol }} {{ number_format($p->loyalty_points, 2) }} credit</span>
                            </td>
                            <td class="text-center fw-bolder font-monospace text-info fs-5">
                                {{ $settings->currency_symbol }} {{ number_format($p->lifetime_spend, 2) }}
                            </td>
                            <td>
                                <div class="fs-7 text-light fw-medium mb-1"><i class="bi bi-heart-fill text-danger me-1"></i> Favorites: <strong class="text-warning">{{ $p->favorite_dish_category ?? 'Universal Menu' }}</strong></div>
                                @if($p->dietary_notes && !empty($p->dietary_notes))
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fs-8 p-2 font-monospace">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> ALLERGY/NOTE: {{ $p->dietary_notes }}
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-modern-outline rounded-pill px-3 font-monospace fs-8" data-bs-toggle="modal" data-bs-target="#bonusModal-{{ $p->id }}" title="Award Bonus VIP Loyalty Points">
                                    <i class="bi bi-gift-fill text-warning me-1"></i> Award Bonus
                                </button>
                            </td>
                        </tr>

                        <!-- Award Bonus Modal for specific patron -->
                        <div class="modal fade" id="bonusModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content glass-card border border-warning border-opacity-50 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
                                    <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                                        <h5 class="modal-title font-heading fw-bold text-warning mb-0">
                                            <i class="bi bi-star-fill text-warning me-2"></i> Award Bonus Points: {{ $p->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('crm.award_bonus', $p->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body py-4 text-start">
                                            <div class="mb-3">
                                                <label class="form-label fs-7 fw-bold text-muted">Current Point Wallet Balance</label>
                                                <input type="text" class="form-control bg-dark text-warning fw-bolder font-monospace border-secondary border-opacity-50 fs-5" value="{{ $p->loyalty_points }} Points" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fs-7 fw-bold text-light">Bonus Loyalty Points to Deposit</label>
                                                <input type="number" name="bonus_points" class="form-control bg-dark text-success font-monospace fs-4 border-secondary border-opacity-50" value="200" required min="10" max="5000">
                                                <small class="text-muted fs-8">1 point equals ₹1.00 instant discount credit towards Touch POS dining bills.</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fs-7 fw-bold text-light">Reason / Event Trigger</label>
                                                <select name="reason" class="form-select bg-dark text-info border-secondary border-opacity-50 fs-6 py-2" required>
                                                    <option value="Birthday Anniversary Dining Bonus">🎉 Birthday & Anniversary Celebration</option>
                                                    <option value="Executive Chef Dining Courtesy">👨‍🍳 Executive Chef Table Courtesy</option>
                                                    <option value="Service Goodwill Compensation">🤝 Service Goodwill Compensation</option>
                                                    <option value="Seasonal Promotional Campaign">🌟 Seasonal Holiday Campaign</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-modern-primary px-5 fw-bold shadow-lg">
                                                <i class="bi bi-check2-circle me-1"></i> DEPOSIT VIP POINTS
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Enroll Brand New VIP Guest Modal -->
<div class="modal fade" id="newPatronModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border border-purple border-opacity-50 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95); border-left: 5px solid #a855f7 !important;">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <div>
                    <h4 class="modal-title font-heading fw-bold text-white mb-0"><i class="bi bi-person-plus-fill text-warning me-2"></i> Enroll New VIP Guest Profile</h4>
                    <span class="fs-8 text-muted">Register loyalty membership, assign VIP tier points wallet, and record critical table dietary requirements.</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('crm.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fs-7 fw-bold text-light">Full Patron Name / Title</label>
                            <input type="text" name="name" class="form-control bg-dark text-white fs-5 border-secondary border-opacity-50 font-heading" placeholder="e.g. Lord Alistair Sterling or Dr. Meera Rao" required autofocus>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fs-7 fw-bold text-light">Mobile Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-dark text-warning font-monospace fs-5 border-secondary border-opacity-50" placeholder="+91-9876599999" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-bold text-light">Email Address (Optional)</label>
                            <input type="email" name="email" class="form-control bg-dark text-light font-monospace fs-6 border-secondary border-opacity-50" placeholder="guest@vip-portal.com">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-7 fw-bold text-light">Loyalty Tier</label>
                            <select name="tier" class="form-select bg-dark text-info border-secondary border-opacity-50 fs-6 py-2 font-monospace" required>
                                <option value="silver">🥈 Silver Member</option>
                                <option value="gold">🏆 Gold Tier</option>
                                <option value="platinum_vip" selected>💎 Platinum VIP</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-7 fw-bold text-light">Welcome Points Deposit</label>
                            <input type="number" name="loyalty_points" class="form-control bg-dark text-success font-monospace fs-5 border-secondary border-opacity-50" value="500" min="0" max="10000">
                        </div>
                    </div>

                    <div class="p-3 bg-dark bg-opacity-75 border border-secondary border-opacity-25 rounded-4 mb-3">
                        <h6 class="fw-bold text-warning mb-3"><i class="bi bi-heart-pulse-fill text-danger me-2"></i> Culinary Preferences & Dietary Restrictions</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-8 fw-bold text-secondary text-uppercase">Favorite Cuisine / Dishes</label>
                                <input type="text" name="favorite_dish_category" class="form-control bg-dark text-light fs-6 border-secondary border-opacity-50" placeholder="e.g. Wood-Fired Artisanal Pizzas or Truffle Risotto" value="Artisanal Grills & Cocktails">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-8 fw-bold text-danger text-uppercase"><i class="bi bi-exclamation-triangle-fill me-1"></i> Allergies & Dietary Notes</label>
                                <input type="text" name="dietary_notes" class="form-control bg-dark text-danger font-monospace fs-6 border-secondary border-opacity-50" placeholder="e.g. Severe Peanut Allergy, Gluten-Free only, Halal">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary rounded-pill px-5 fw-bold shadow-lg">
                        <i class="bi bi-check-circle-fill me-2"></i> ENROLL VIP PATRON & ISSUE POINTS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
