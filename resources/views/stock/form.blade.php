@extends('layouts.app')
@section('title', 'Record stock movement')

@section('content')
<div class="row"><div class="col-lg-6">
<div class="card">
    <form method="POST" action="{{ route('stock.store') }}">
        @csrf
        <div class="card-body row g-3">
            <div class="col-12">
                <label class="form-label small fw-semibold">Ingredient</label>
                <select name="ingredient_id" class="form-select" required>
                    <option value="">Choose…</option>
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}" @selected(request('ingredient') == $ing->id || old('ingredient_id') == $ing->id)>
                            {{ $ing->name }} — {{ rtrim(rtrim(number_format((float) $ing->stock_qty, 3), '0'), '.') }} {{ $ing->unit }} on hand
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Movement type</label>
                <select name="type" id="type" class="form-select" onchange="hint()">
                    <option value="purchase">Purchase — stock in</option>
                    <option value="wastage">Wastage — stock out</option>
                    <option value="adjustment">Adjustment — set to counted quantity</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Quantity</label>
                <input type="number" step="0.001" min="0" name="quantity" class="form-control" required>
                <div class="form-text" id="qtyHint">Quantity being added to stock.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Unit cost (purchases)</label>
                <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Note</label>
                <input type="text" name="note" class="form-control" placeholder="Supplier bill number, reason for wastage…">
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Record</button>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
</div></div>
@endsection

@push('scripts')
<script>
    function hint() {
        const map = {
            purchase: 'Quantity being added to stock.',
            wastage: 'Quantity thrown away or spoiled — it is removed from stock.',
            adjustment: 'The quantity you physically counted. The difference is stored as an adjustment.',
        };
        document.getElementById('qtyHint').textContent = map[document.getElementById('type').value];
    }
    hint();
</script>
@endpush
