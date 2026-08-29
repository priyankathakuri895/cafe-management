@extends('layouts.app')
@section('title', $ingredient->exists ? 'Edit ingredient' : 'New ingredient')

@section('content')
<div class="row g-3">
<div class="col-lg-6">
<div class="card">
    <form method="POST" action="{{ $ingredient->exists ? route('ingredients.update', $ingredient) : route('ingredients.store') }}">
        @csrf
        @if($ingredient->exists) @method('PUT') @endif
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name', $ingredient->name) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    @foreach(\App\Models\Ingredient::units() as $k => $v)
                        <option value="{{ $k }}" @selected(old('unit', $ingredient->unit) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            @unless($ingredient->exists)
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Opening stock</label>
                    <input type="number" step="0.001" min="0" name="stock_qty" value="{{ old('stock_qty', 0) }}" class="form-control">
                </div>
            @endunless
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Reorder level</label>
                <input type="number" step="0.001" min="0" name="reorder_level" value="{{ old('reorder_level', (float) $ingredient->reorder_level) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Cost per unit</label>
                <input type="number" step="0.01" min="0" name="cost_per_unit" value="{{ old('cost_per_unit', (float) $ingredient->cost_per_unit) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Supplier</label>
                <input type="text" name="supplier" value="{{ old('supplier', $ingredient->supplier) }}" class="form-control">
            </div>
            @if($ingredient->exists)
                <div class="col-12">
                    <div class="alert alert-light border small mb-0">
                        Stock on hand is <strong>{{ rtrim(rtrim(number_format((float) $ingredient->stock_qty, 3), '0'), '.') }} {{ $ingredient->unit }}</strong>.
                        It only changes through <a href="{{ route('stock.create') }}?ingredient={{ $ingredient->id }}">stock movements</a>, so every change stays on record.
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            @if($ingredient->exists)
                <form method="POST" action="{{ route('ingredients.destroy', $ingredient) }}" class="ms-auto" onsubmit="return confirm('Delete this ingredient and its history?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger">Delete</button>
                </form>
            @endif
        </div>
    </form>
</div>
</div>

@if($ingredient->exists)
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">Recent movements</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Date</th><th>Type</th><th class="text-end">Qty</th><th class="text-end">Balance</th></tr></thead>
                <tbody>
                @forelse($ingredient->transactions as $t)
                    <tr>
                        <td class="small">{{ $t->created_at->format('d M, h:i A') }}</td>
                        <td><span class="badge text-bg-{{ $t->typeColor() }}">{{ ucfirst($t->type) }}</span></td>
                        <td class="text-end">{{ (float) $t->quantity > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format((float) $t->quantity, 3), '0'), '.') }}</td>
                        <td class="text-end">{{ rtrim(rtrim(number_format((float) $t->stock_after, 3), '0'), '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No movements recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
</div>
@endsection
