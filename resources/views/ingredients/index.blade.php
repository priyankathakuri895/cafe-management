@extends('layouts.app')
@section('title', 'Ingredients')
@section('subtitle', 'Raw stock the kitchen and bar draw from')

@section('content')
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3 d-flex align-items-center pt-3">
            <div class="form-check">
                <input type="checkbox" name="low" value="1" class="form-check-input" id="low" @checked(request('low'))>
                <label class="form-check-label small" for="low">Only low stock</label>
            </div>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button class="btn btn-sm btn-cafe">Filter</button>
            <a href="{{ route('ingredients.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <a href="{{ route('ingredients.create') }}" class="btn btn-sm btn-cafe ms-auto"><i class="bi bi-plus-lg"></i> New ingredient</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Ingredient</th><th>Unit</th><th class="text-end">In stock</th><th class="text-end">Reorder at</th><th class="text-end">Cost / unit</th><th>Supplier</th><th></th></tr></thead>
            <tbody>
            @forelse($ingredients as $ing)
                <tr class="{{ $ing->isLow() ? 'table-warning' : '' }}">
                    <td class="fw-semibold">{{ $ing->name }} @if($ing->isLow())<i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="At or below reorder level"></i>@endif</td>
                    <td>{{ $ing->unit }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format((float) $ing->stock_qty, 3), '0'), '.') }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format((float) $ing->reorder_level, 3), '0'), '.') }}</td>
                    <td class="text-end">@money($ing->cost_per_unit)</td>
                    <td class="small text-muted">{{ $ing->supplier ?? '—' }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('stock.create') }}?ingredient={{ $ing->id }}" class="btn btn-sm btn-outline-secondary">Stock in/out</a>
                        <a href="{{ route('ingredients.edit', $ing) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No ingredients yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $ingredients->links() }}</div>
@endsection
