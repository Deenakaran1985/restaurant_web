@extends('layouts.admin')

@section('title', 'Executive Financial P&L Statement')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Ribbon -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-2 fw-semibold">
                <i class="bi bi-shield-check me-2"></i> ENTERPRISE FISCAL CONTROLLER SUITE
            </span>
            <h2 class="fw-bold font-heading mb-1 text-light">Profit & Loss Statement (P&L)</h2>
            <p class="text-muted mb-0 fs-6">Live accounting reconciliation linking Touch POS revenue, automated recipe store COGS deduction, and EBITDA margins.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('accounts.verify_lifecycle') }}" class="btn btn-modern-primary me-2 shadow-lg">
                <i class="bi bi-cpu-fill me-1"></i> End-to-End Lifecycle Verification
            </a>
            <a href="{{ route('accounts.invoices') }}" class="btn btn-modern-outline">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Tax Ledgers
            </a>
        </div>
    </div>

    <!-- 4 High-Impact Fiscal KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Gross Dining Revenue</span>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 fs-4"><i class="bi bi-wallet2"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-white">{{ $settings->currency_symbol }} {{ number_format($plReport['gross_revenue'], 2) }}</h2>
                    <p class="fs-8 text-success mb-0"><i class="bi bi-arrow-up-right me-1"></i> Across {{ $plReport['total_orders'] }} POS & QR Orders</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Actual Recipe COGS</span>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 fs-4"><i class="bi bi-box-seams-fill"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-warning">{{ $settings->currency_symbol }} {{ number_format($plReport['actual_cogs'], 2) }}</h2>
                    <p class="fs-8 text-{{ $plReport['food_cost_pct'] <= 30 ? 'success' : 'warning' }} mb-0 fw-medium">
                        {{ $plReport['food_cost_pct'] }}% Food Cost Percentage (Target &le; 30%)
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Net Operating EBITDA</span>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 fs-4"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-success">{{ $settings->currency_symbol }} {{ number_format($plReport['net_operating_profit'], 2) }}</h2>
                    <p class="fs-8 text-muted mb-0">{{ $plReport['net_margin_pct'] }}% Net Executive Margin</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card glass-card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-7 text-uppercase fw-semibold">Warehouse Asset Valuation</span>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 fs-4"><i class="bi bi-building"></i></div>
                </div>
                <div>
                    <h2 class="fw-bolder font-monospace mb-1 text-info">{{ $settings->currency_symbol }} {{ number_format($plReport['inventory_asset_valuation'], 2) }}</h2>
                    <p class="fs-8 text-muted mb-0">Central store raw ingredient assets on hand</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Structured Financial Income Statement -->
        <div class="col-lg-8">
            <div class="card glass-card border-0 rounded-5 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <h4 class="fw-bold font-heading mb-0 text-white"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i> Consolidated P&L Statement</h4>
                    <span class="badge bg-dark border border-secondary border-opacity-25 font-monospace text-light px-3 py-2">PERIOD: MTD ACTUAL</span>
                </div>

                <div class="py-2">
                    <!-- Operating Revenue Section -->
                    <div class="d-flex justify-content-between align-items-center py-2 text-light fw-bold fs-5 border-bottom border-secondary border-opacity-10">
                        <span>1. Gross Dining & Beverage Revenue</span>
                        <span class="font-monospace text-success">{{ $settings->currency_symbol }} {{ number_format($plReport['gross_revenue'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 text-muted ps-4 fs-7">
                        <span><i class="bi bi-circle-fill text-success me-2" style="font-size: 6px;"></i> Dine-In & QR Table Orders ({{ $plReport['total_dishes_sold'] }} portions served)</span>
                        <span class="font-monospace">{{ $settings->currency_symbol }} {{ number_format($plReport['gross_revenue'], 2) }}</span>
                    </div>

                    <!-- Cost of Goods Sold Section -->
                    <div class="d-flex justify-content-between align-items-center py-3 text-light fw-bold fs-5 border-bottom border-secondary border-opacity-10 mt-3">
                        <span>2. Cost of Goods Sold (Recipe Store Depletion)</span>
                        <span class="font-monospace text-warning">- {{ $settings->currency_symbol }} {{ number_format($plReport['actual_cogs'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 text-muted ps-4 fs-7">
                        <span><i class="bi bi-circle-fill text-warning me-2" style="font-size: 6px;"></i> Raw Material Inventory Consumption ({{ $plReport['food_cost_pct'] }}% of Revenue)</span>
                        <span class="font-monospace text-warning">- {{ $settings->currency_symbol }} {{ number_format($plReport['actual_cogs'], 2) }}</span>
                    </div>

                    <!-- Gross Profit Subtotal -->
                    <div class="p-3 bg-dark bg-opacity-50 rounded-4 my-3 border border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5 text-light">GROSS MARGIN PROFIT</span>
                        <div class="text-end">
                            <span class="fw-bolder font-monospace fs-4 text-white">{{ $settings->currency_symbol }} {{ number_format($plReport['gross_profit'], 2) }}</span>
                            <span class="badge badge-emerald ms-2">{{ $plReport['gross_margin_pct'] }}% Margin</span>
                        </div>
                    </div>

                    <!-- Operating Expenses -->
                    <div class="d-flex justify-content-between align-items-center py-2 text-light fw-bold fs-5 border-bottom border-secondary border-opacity-10 mt-3">
                        <span>3. Operating Overheads & Labor Estimation</span>
                        <span class="font-monospace text-danger">- {{ $settings->currency_symbol }} {{ number_format($plReport['total_overhead'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 text-muted ps-4 fs-7">
                        <span><i class="bi bi-circle-fill text-info me-2" style="font-size: 6px;"></i> Kitchen & Hall Staff Compensation (Est. 18.0%)</span>
                        <span class="font-monospace">- {{ $settings->currency_symbol }} {{ number_format($plReport['labor_cost'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 text-muted ps-4 fs-7">
                        <span><i class="bi bi-circle-fill text-info me-2" style="font-size: 6px;"></i> Rent, Utilities & Software Licensing (Est. 7.0%)</span>
                        <span class="font-monospace">- {{ $settings->currency_symbol }} {{ number_format($plReport['utilities_cost'], 2) }}</span>
                    </div>

                    <!-- Grand Net Profit EBITDA -->
                    <div class="p-4 rounded-4 mt-4 text-center d-flex align-items-center justify-content-between shadow-lg" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%); border: 1px solid #10b981;">
                        <div class="text-start">
                            <h4 class="fw-bolder font-heading text-white mb-1">NET EXECUTIVE EBITDA PROFIT</h4>
                            <p class="text-success mb-0 fs-7">Verified bottom-line profitability after raw ingredients and enterprise overheads</p>
                        </div>
                        <div class="text-end">
                            <div class="fw-bolder font-monospace fs-2 text-success">{{ $settings->currency_symbol }} {{ number_format($plReport['net_operating_profit'], 2) }}</div>
                            <span class="badge bg-success text-white fw-bold px-3 py-2 fs-6 mt-1">{{ $plReport['net_margin_pct'] }}% NET MARGIN</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Split & Tax Liabilities -->
        <div class="col-lg-4">
            <div class="card glass-card border-0 rounded-5 p-4 mb-4">
                <h5 class="fw-bold font-heading mb-3 text-white"><i class="bi bi-pie-chart me-2 text-info"></i> Revenue by Menu Category</h5>
                @foreach($plReport['category_breakdown'] as $cat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-light fs-7">{{ $cat->category_name }}</span>
                            <span class="font-monospace fw-bold text-info fs-7">{{ $settings->currency_symbol }} {{ number_format($cat->category_revenue, 2) }}</span>
                        </div>
                        <div class="progress bg-dark" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($plReport['gross_revenue'] > 0) ? ($cat->category_revenue / $plReport['gross_revenue']) * 100 : 50 }}%"></div>
                        </div>
                        <small class="text-muted fs-8">{{ $cat->items_sold }} individual portions sold</small>
                    </div>
                @endforeach
            </div>

            <!-- GST / VAT Fiscal Liabilities -->
            <div class="card glass-card border-0 rounded-5 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 me-3 fs-3">
                        <i class="bi bi-bank"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold font-heading mb-0 text-white">GST / VAT Tax Reserve</h5>
                        <small class="text-muted fs-8">Fiscal government liability collection</small>
                    </div>
                </div>
                <div class="p-3 bg-dark bg-opacity-50 rounded-4 border border-secondary border-opacity-25 mb-3 text-center">
                    <span class="fs-8 text-muted d-block text-uppercase">Total Tax Collected ({{ $settings->default_tax_rate }}% Rate)</span>
                    <h3 class="fw-bolder font-monospace text-danger mt-1 mb-0">{{ $settings->currency_symbol }} {{ number_format($plReport['tax_collected'], 2) }}</h3>
                </div>
                <div class="fs-8 text-muted text-center font-monospace">
                    Tax Registration Number:<br>
                    <strong class="text-white">{{ $settings->gst_vat_number }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
