@extends('layouts.admin')

@section('title', 'Menu & COGS Recipe Mapping')
@section('page_title', 'Menu Engineering & Automated COGS Recipe Mapping')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4 bg-success bg-opacity-10 border-success border-opacity-25 text-success p-4 mb-4 d-flex align-items-center shadow-lg" role="alert">
    <i class="bi bi-patch-check-fill me-3 fs-2 animate-bounce"></i>
    <div>
        <strong class="text-white d-block fs-5">Culinary Database Synced!</strong>
        <span class="fs-7 text-muted">{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Active Menu Categories Strip -->
<div class="card glass-card border-0 rounded-5 p-4 mb-4 shadow-lg" style="background: radial-gradient(circle at 10% 50%, rgba(59, 130, 246, 0.12) 0%, transparent 60%);">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h6 class="text-uppercase fw-bold text-secondary tracking-wider mb-0 fs-8"><i class="bi bi-tags-fill text-info me-2"></i> MASTER MENU CATEGORY CLUSTERS</h6>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-info rounded-pill font-monospace px-3 shadow" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-circle-fill text-warning me-1"></i> + Add New Category
            </button>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @forelse($categories as $cat)
            <div class="badge bg-dark border border-secondary border-opacity-50 px-3 py-2 rounded-4 d-flex align-items-center fs-7 font-monospace text-white shadow-sm">
                <i class="bi {{ $cat->icon ?? 'bi-cup-hot' }} text-warning fs-6 me-2"></i>
                <span>{{ $cat->name }}</span>
            </div>
        @empty
            <span class="text-muted fs-7">No category clusters found. Click '+ Add New Category' above!</span>
        @endforelse
    </div>
</div>

<div class="glass-card p-4 rounded-5 mb-5 shadow-lg border-0" style="border-left: 5px solid #3b82f6 !important;">
    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary border-opacity-25 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold font-heading text-white"><i class="bi bi-diagram-3-fill text-primary me-2"></i> Dish to Raw Ingredient Mapping</h4>
            <p class="fs-7 text-muted mb-0">Whenever KDS marks an order as <strong>Ready/Served</strong>, the system automatically subtracts these precise raw material quantities from central warehouse ledgers.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-modern-outline btn-sm px-3 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Register a new gourmet dish category"><i class="bi bi-folder-plus text-info me-1"></i> + Add Category</button>
            <a href="{{ route('menu.engineering') }}" class="btn btn-modern-outline btn-sm px-3 fw-bold"><i class="bi bi-graph-up-arrow text-warning me-1"></i> Profit Margin Engineering</a>
            <button class="btn btn-modern-primary btn-sm px-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#newRecipeModal"><i class="bi bi-plus-circle-fill me-1"></i> + New Dish Recipe</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark dark:text-white table-dark table-borderless">
            <thead class="fs-8 text-secondary text-uppercase border-bottom border-secondary border-opacity-50">
                <tr>
                    <th class="py-3 ps-3">Dish Code & Name</th>
                    <th class="py-3">Category</th>
                    <th class="py-3">Selling Price (INR)</th>
                    <th class="py-3">Mapped Raw Ingredients (COGS Depletion)</th>
                    <th class="py-3 text-center">Target Kitchen SLA</th>
                    <th class="py-3 pe-3 text-end">Configuration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <td class="py-3 ps-3">
                            <div class="fw-bold font-heading fs-5 text-white">{{ $item->name }}</div>
                            <span class="badge bg-dark border border-secondary border-opacity-50 text-warning font-monospace fs-8">{{ $item->code }}</span>
                        </td>
                        <td>
                            <span class="badge badge-azure px-3 py-2 font-monospace fs-8">
                                <i class="bi {{ $item->category->icon ?? 'bi-tag-fill' }} me-1"></i> {{ $item->category->name ?? 'Specialty' }}
                            </span>
                        </td>
                        <td class="fw-bolder text-success font-monospace fs-5">₹{{ number_format($item->price, 2) }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($item->ingredients as $ing)
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 p-2 font-monospace fs-8">
                                        <i class="bi bi-box me-1 text-info"></i> {{ $ing->name }} (<strong>{{ $ing->pivot->quantity_needed }}</strong> {{ $ing->unit }})
                                    </span>
                                @empty
                                    <span class="badge badge-amber p-2 fs-8"><i class="bi bi-exclamation-triangle me-1"></i> No Recipe Mapped (100% Margin Estimate)</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-center"><span class="badge badge-emerald font-monospace px-3 py-2"><i class="bi bi-stopwatch me-1"></i> {{ $item->prep_time_minutes }} mins</span></td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-modern-outline rounded-pill px-3 font-monospace fs-8" onclick="openAdjustModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, {{ $item->prep_time_minutes }})" title="Adjust ingredient weights or SLA timer">
                                <i class="bi bi-sliders text-warning me-1"></i> Adjust Recipe
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Brand New Gourmet Dish Recipe Modal -->
<div class="modal fade" id="newRecipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border border-secondary border-opacity-25 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <div>
                    <h4 class="modal-title font-heading fw-bold text-white mb-0"><i class="bi bi-magic text-warning me-2"></i> Create New Gourmet Dish Recipe</h4>
                    <span class="fs-8 text-muted">Map culinary portions directly to central warehouse raw store items for automated financial loss prevention.</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('menu.store_recipe') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fs-7 fw-bold text-light">Dish Title / Gastronome Name</label>
                            <input type="text" name="name" class="form-control bg-dark text-white fs-5 border-secondary border-opacity-50" placeholder="e.g. Truffle Mushroom Cream Risotto" required autofocus>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold text-light">Dish Code (Optional)</label>
                            <input type="text" name="code" class="form-control bg-dark text-warning font-monospace fs-5 border-secondary border-opacity-50" placeholder="e.g. RIS-01 (Auto)">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold text-light">Menu Category Cluster</label>
                            <select name="category_id" class="form-select bg-dark text-info border-secondary border-opacity-50 fs-6 py-2" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold text-light">Selling Price (INR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-success fw-bold border-secondary border-opacity-50">₹</span>
                                <input type="number" step="0.01" name="price" class="form-control bg-dark text-success font-monospace fs-5 border-secondary border-opacity-50" placeholder="650.00" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold text-light">Kitchen Prep SLA Timer (Mins)</label>
                            <input type="number" name="prep_time_minutes" class="form-control bg-dark text-white font-monospace fs-5 border-secondary border-opacity-50" value="15" min="3" max="120">
                        </div>
                    </div>

                    <!-- Raw Ingredient COGS Formula Builder -->
                    <div class="p-3 bg-dark bg-opacity-75 border border-secondary border-opacity-25 rounded-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-warning mb-0"><i class="bi bi-box-seams me-2"></i> Raw Ingredient COGS Formula (Store Deduction)</h6>
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill font-monospace" onclick="addIngredientRow('newRecipeRows')"><i class="bi bi-plus-circle me-1"></i> Add Another Material</button>
                        </div>
                        <div id="newRecipeRows">
                            <div class="row g-2 align-items-center mb-2 ingredient-row">
                                <div class="col-8">
                                    <select name="ingredient_id[]" class="form-select bg-dark text-light border-secondary border-opacity-50 fs-7">
                                        <option value="" selected>-- Select Store Raw Material --</option>
                                        @foreach($inventoryItems as $inv)
                                            <option value="{{ $inv->id }}">{{ $inv->name }} (In Stock: {{ $inv->current_stock }} {{ $inv->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.001" name="quantity_needed[]" class="form-control bg-dark text-warning font-monospace fs-7 border-secondary border-opacity-50" placeholder="Qty (e.g. 0.150)">
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="this.closest('.ingredient-row').remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <div class="row g-2 align-items-center mb-2 ingredient-row">
                                <div class="col-8">
                                    <select name="ingredient_id[]" class="form-select bg-dark text-light border-secondary border-opacity-50 fs-7">
                                        <option value="" selected>-- Select Store Raw Material --</option>
                                        @foreach($inventoryItems as $inv)
                                            <option value="{{ $inv->id }}">{{ $inv->name }} (In Stock: {{ $inv->current_stock }} {{ $inv->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.001" name="quantity_needed[]" class="form-control bg-dark text-warning font-monospace fs-7 border-secondary border-opacity-50" placeholder="Qty (e.g. 0.050)">
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="this.closest('.ingredient-row').remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary px-5 fw-bold shadow-lg">
                        <i class="bi bi-check-circle-fill me-2"></i> SAVE RECIPE & SYNC COGS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Register New Menu Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-info border-opacity-50 text-light rounded-5 p-4 shadow-lg" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-info mb-0"><i class="bi bi-folder-plus text-warning me-2"></i> Register New Menu Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('menu.store_category') }}" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <p class="text-muted fs-7 mb-4">Create a new culinary category cluster. Dishes assigned to this category will be automatically grouped on the Touch POS terminal and digital self-ordering QR portal.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold text-light">Category Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-white fs-5 border-secondary border-opacity-50 font-heading" placeholder="e.g. Artisanal Wood-Fired Pizzas or Craft Cocktails" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-8">
                            <label class="form-label fs-7 fw-bold text-light">Category Visual Icon</label>
                            <select name="icon" class="form-select bg-dark text-warning border-secondary border-opacity-50 fs-6 py-2 font-monospace">
                                <option value="bi-cup-hot">☕ Beverages / Coffee (bi-cup-hot)</option>
                                <option value="bi-vinyl-fill">🍕 Artisanal Pizzas / Flatbreads (bi-vinyl-fill)</option>
                                <option value="bi-fire">🥩 Wood-Fired Grills & BBQ (bi-fire)</option>
                                <option value="bi-water">🍸 Signature Cocktails & Bar (bi-water)</option>
                                <option value="bi-basket-fill">🥗 Appetizers & Salads (bi-basket-fill)</option>
                                <option value="bi-cake2-fill">🍰 Artisan Desserts & Sweets (bi-cake2-fill)</option>
                                <option value="bi-globe-asia-australia">🦐 Exotic Gourmet Seafood (bi-globe)</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label fs-7 fw-bold text-light">POS Display Priority</label>
                            <input type="number" name="sort_order" class="form-control bg-dark text-info font-monospace fs-5 border-secondary border-opacity-50" value="1" min="1" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary rounded-pill px-4 fw-bold shadow-lg">
                        <i class="bi bi-check-lg me-1"></i> REGISTER CATEGORY
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Existing Dish Recipe Modal -->
<div class="modal fade" id="adjustRecipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border border-warning border-opacity-50 text-light rounded-5 p-4" style="backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.95);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="modal-title font-heading fw-bold text-warning mb-0"><i class="bi bi-sliders text-warning me-2"></i> Adjust Dish Recipe & SLA Timer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustRecipeForm" action="" method="POST">
                @csrf
                <div class="modal-body py-4 text-start">
                    <h5 class="fw-bold text-white mb-3" id="adjustDishName">Dish Title</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">Selling Price (INR)</label>
                            <input type="number" step="0.01" name="price" id="adjustPrice" class="form-control bg-dark text-success font-monospace fs-5 border-secondary border-opacity-50" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-bold text-light">SLA Prep Timer (Mins)</label>
                            <input type="number" name="prep_time_minutes" id="adjustPrep" class="form-control bg-dark text-warning font-monospace fs-5 border-secondary border-opacity-50" required>
                        </div>
                    </div>

                    <p class="text-muted fs-8 mb-2"><i class="bi bi-info-circle text-info me-1"></i> Add or update raw store material portion weights below:</p>
                    <div id="adjustRecipeRows">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-8">
                                <select name="ingredient_id[]" class="form-select bg-dark text-light border-secondary border-opacity-50 fs-7">
                                    <option value="" selected>-- Pick Raw Store Ingredient --</option>
                                    @foreach($inventoryItems as $inv)
                                        <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.001" name="quantity_needed[]" class="form-control bg-dark text-warning font-monospace fs-7 border-secondary border-opacity-50" placeholder="Qty per dish">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i> UPDATE FORMULA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function addIngredientRow(containerId) {
    const container = document.getElementById(containerId);
    const firstRow = container.querySelector('.ingredient-row');
    if (firstRow) {
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('select').selectedIndex = 0;
        newRow.querySelector('input').value = '';
        container.appendChild(newRow);
    }
}

function openAdjustModal(dishId, dishName, price, prepTime) {
    document.getElementById('adjustDishName').innerText = dishName;
    document.getElementById('adjustPrice').value = price;
    document.getElementById('adjustPrep').value = prepTime;
    
    document.getElementById('adjustRecipeForm').action = '/menu/' + dishId + '/adjust-recipe';
    const modal = new bootstrap.Modal(document.getElementById('adjustRecipeModal'));
    modal.show();
}
</script>
@endpush
