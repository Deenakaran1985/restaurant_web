@extends('layouts.admin')

@section('title', 'Menu Engineering & Recipe COGS Analytics')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill mb-2 fw-semibold">
                <i class="bi bi-graph-up-arrow me-2"></i> BOSTON CONSULTING GROUP (BCG) MARGIN MATRIX
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Menu Profit Engineering & Recipe COGS</h2>
            <p class="text-muted mb-0 fs-6">Automated recipe ingredient cost accounting, gross margin optimization, and dish popularity classification.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ url('/menu') }}" class="btn btn-modern-outline me-2">
                <i class="bi bi-arrow-left me-1"></i> Menu Catalog
            </a>
            <button class="btn btn-modern-primary" data-bs-toggle="modal" data-bs-target="#simulatorModal">
                <i class="bi bi-sliders me-1"></i> Live Price Simulator
            </button>
        </div>
    </div>

    <!-- Executive Portfolio Health KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Overall Food Cost %</span>
                    <div class="p-3 bg-{{ $portfolioStats['overall_food_cost_pct'] <= 30 ? 'success' : 'warning' }} bg-opacity-10 text-{{ $portfolioStats['overall_food_cost_pct'] <= 30 ? 'success' : 'warning' }} rounded-4 fs-4">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder font-heading mb-1 text-light">{{ $portfolioStats['overall_food_cost_pct'] }}%</h3>
                    <p class="fs-8 text-{{ $portfolioStats['overall_food_cost_pct'] <= 30 ? 'success' : 'warning' }} mb-0 fw-medium">
                        <i class="bi bi-shield-check me-1"></i> Target Benchmark: &le; 30.0% COGS
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Average Gross Margin</span>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 fs-4">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder font-heading mb-1 text-light">{{ $portfolioStats['overall_margin_pct'] }}%</h3>
                    <p class="fs-8 text-muted mb-0">After subtracting raw inventory ingredient store costs</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Stars & Plowhorses</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder font-heading mb-1 text-warning">{{ $portfolioStats['stars'] }} Stars / {{ $portfolioStats['plowhorses'] }} Plows</h3>
                    <p class="fs-8 text-muted mb-0">Highest order volume across kitchen KOT tickets</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Puzzlers & Dogs</span>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 fs-4">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder font-heading mb-1 text-light">{{ $portfolioStats['puzzlers'] }} Puzzlers / {{ $portfolioStats['dogs'] }} Dogs</h3>
                    <p class="fs-8 text-danger mb-0">Candidates for repricing or table waiter upsells</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BCG Menu Matrix & Recipe Ingredient Cost Breakdown Table -->
    <div class="card glass-card border-0 rounded-5 overflow-hidden p-0 mb-5">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-table me-2 text-info"></i> Recipe COGS & Profitability Ledger</h5>
            <span class="fs-7 text-muted font-monospace">Tax Deduction Mode: Net of {{ $settings->default_tax_rate }}% GST/VAT</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead style="background: rgba(255,255,255,0.03);">
                    <tr class="text-muted fs-8 text-uppercase tracking-wider">
                        <th class="py-3 ps-4">Dish Details & Category</th>
                        <th class="py-3 text-center">Menu Price</th>
                        <th class="py-3 text-center">Raw Recipe COGS</th>
                        <th class="py-3 text-center">Gross Margin (₹ & %)</th>
                        <th class="py-3 text-center">Sales Vol</th>
                        <th class="py-3 text-center">BCG Matrix Class</th>
                        <th class="py-3 pe-4">Recommended Strategic Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analyzedItems as $item)
                        <tr>
                            <td class="py-3 ps-4">
                                <div class="fw-bold text-light fs-6">{{ $item['name'] }}</div>
                                <div class="fs-8 text-muted font-monospace">{{ $item['category'] }} | Code: {{ $item['code'] }} | {{ $item['ingredients_count'] ?? 0 }} Raw Ingredients Mapped</div>
                            </td>
                            <td class="text-center fw-bolder font-monospace text-light">
                                {{ $settings->currency_symbol }} {{ number_format($item['price'], 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-dark bg-opacity-75 text-{{ $item['food_cost_pct'] > 32 ? 'danger' : 'success' }} font-monospace p-2 border border-secondary border-opacity-25 fs-7">
                                    {{ $settings->currency_symbol }} {{ number_format($item['recipe_cost'], 2) }} ({{ $item['food_cost_pct'] }}%)
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-warning font-monospace fs-6">{{ $settings->currency_symbol }} {{ number_format($item['gross_profit'], 2) }}</div>
                                <div class="fs-8 text-muted">{{ $item['margin_pct'] }}% Profit</div>
                            </td>
                            <td class="text-center font-monospace fw-bold text-info fs-6">
                                {{ $item['sold_qty'] }} <span class="fs-8 fw-normal text-muted">sold</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $item['badge_style'] }} px-3 py-2 fs-7">
                                    <i class="bi {{ $item['icon'] }} me-1"></i> {{ $item['bcg_class'] }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="p-2 rounded-3 bg-dark bg-opacity-50 text-light fs-8 border border-secondary border-opacity-25">
                                    <i class="bi bi-lightning-charge text-warning me-1"></i> {{ $item['strategy'] }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Interactive Recipe Margin Simulator Modal -->
<div class="modal fade" id="simulatorModal" tabindex="-1" aria-labelledby="simulatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold" id="simulatorModalLabel">
                    <i class="bi bi-sliders me-2 text-warning"></i> Real-Time Price & Recipe Margin Simulator
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted fs-7 mb-4">Adjust selling prices or simulate vendor raw ingredient price hikes to instantly see impact on dish profit margin percentages without editing live database records.</p>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fs-7 fw-bold text-info">Simulated Dish Selling Price (₹)</label>
                        <input type="range" class="form-range" id="simPrice" min="150" max="1500" step="10" value="550" oninput="updateSimulation()">
                        <div class="d-flex justify-content-between font-monospace text-warning fw-bold fs-5">
                            <span>₹150</span>
                            <span id="simPriceVal">₹ 550</span>
                            <span>₹1,500</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-7 fw-bold text-danger">Raw Ingredient Store Cost (COGS ₹)</label>
                        <input type="range" class="form-range" id="simCost" min="20" max="600" step="5" value="140" oninput="updateSimulation()">
                        <div class="d-flex justify-content-between font-monospace text-light fw-bold fs-5">
                            <span>₹20</span>
                            <span id="simCostVal">₹ 140</span>
                            <span>₹600</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 text-center">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <span class="text-muted fs-8 d-block text-uppercase">Projected Food Cost %</span>
                            <h3 class="fw-bolder font-monospace mt-1 mb-0 text-info" id="projCogsPct">25.5%</h3>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted fs-8 d-block text-uppercase">Gross Profit (₹ / Dish)</span>
                            <h3 class="fw-bolder font-monospace mt-1 mb-0 text-warning" id="projProfit">₹ 410.00</h3>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted fs-8 d-block text-uppercase">Projected Margin %</span>
                            <h3 class="fw-bolder font-monospace mt-1 mb-0 text-success" id="projMarginPct">74.5%</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close Simulator</button>
                <a href="{{ url('/menu') }}" class="btn btn-modern-primary">Apply Prices to Menu Catalog</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateSimulation() {
    const price = parseFloat(document.getElementById('simPrice').value);
    const cost = parseFloat(document.getElementById('simCost').value);
    
    document.getElementById('simPriceVal').innerText = '₹ ' + price;
    document.getElementById('simCostVal').innerText = '₹ ' + cost;

    const profit = price - cost;
    const cogsPct = (price > 0) ? (cost / price) * 100 : 0;
    const marginPct = (price > 0) ? (profit / price) * 100 : 0;

    document.getElementById('projCogsPct').innerText = cogsPct.toFixed(1) + '%';
    document.getElementById('projProfit').innerText = '₹ ' + profit.toFixed(2);
    document.getElementById('projMarginPct').innerText = marginPct.toFixed(1) + '%';

    const cogsEl = document.getElementById('projCogsPct');
    if (cogsPct > 30.0) {
        cogsEl.classList.remove('text-info', 'text-success');
        cogsEl.classList.add('text-danger');
    } else {
        cogsEl.classList.remove('text-danger');
        cogsEl.classList.add('text-success');
    }
}
</script>
@endpush
