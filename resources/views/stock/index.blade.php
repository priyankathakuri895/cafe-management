@extends('layouts.app')
@section('title', 'Stock movements')
@section('subtitle', 'Every purchase, usage, wastage and correction')

@section('content')
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small mb-1">Ingredient</label>
            <select name="ingredient" class="form-select form-select-sm">
                <option value="">All ingredients</option>
                @foreach($ingredients as $ing)
                    <option value="{{ $ing->id }}" @selected(request('ingredient') == $ing->id)>{{ $ing->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Type</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">All types</option>
                @foreach(\App\Models\StockTransaction::types() as $k => $v)
                    <option value="{{ $k }}" @selected(request('type') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button class="btn btn-sm btn-cafe">Filter</button>
            <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <a href="{{ route('stock.create') }}" class="btn btn-sm btn-cafe ms-auto"><i class="bi bi-plus-lg"></i> Record movement</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Date</th><th>Ingredient</th><th>Type</th><th class="text-end">Quantity</th><th class="text-end">Balance after</th><th>By</th><th>Note</th></tr></thead>
            <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td class="small text-muted">{{ $t->created_at->format('d M Y, h:i A') }}</td>
                    <td class="fw-semibold">{{ $t->ingredient?->name }}</td>
                    <td><span class="badge text-bg-{{ $t->typeColor() }}">{{ ucfirst($t->type) }}</span></td>
                    <td class="text-end">{{ (float) $t->quantity > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format((float) $t->quantity, 3), '0'), '.') }} {{ $t->ingredient?->unit }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format((float) $t->stock_after, 3), '0'), '.') }}</td>
                    <td class="small">{{ $t->user?->name ?? 'system' }}</td>
                    <td class="small text-muted">{{ $t->note ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">Nothing recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $transactions->links() }}</div>
@endsection
