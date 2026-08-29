@extends('layouts.app')
@section('title', 'Tables')
@section('subtitle', 'Floor status at a glance')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('tables.create') }}" class="btn btn-cafe btn-sm"><i class="bi bi-plus-lg"></i> Add table</a>
</div>

<div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
@forelse($tables as $table)
    @php($open = $table->orders->first())
    <div class="col">
        <div class="card h-100 border-{{ $table->statusColor() }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold fs-5">{{ $table->name }}</div>
                        <div class="small text-muted">{{ $table->capacity }} seats @if($table->location) · {{ $table->location }} @endif</div>
                    </div>
                    <span class="badge text-bg-{{ $table->statusColor() }}">{{ \App\Models\CafeTable::statuses()[$table->status] ?? $table->status }}</span>
                </div>

                @if($open)
                    <div class="mt-3 small">
                        <div class="text-muted">Running bill</div>
                        <div class="fw-semibold">{{ $open->order_number }} — @money($open->total)</div>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white d-flex gap-1">
                @if($open)
                    <a href="{{ route('orders.show', $open) }}" class="btn btn-sm btn-cafe flex-fill">Open bill</a>
                @else
                    <a href="{{ route('pos.index') }}?table={{ $table->id }}" class="btn btn-sm btn-outline-secondary flex-fill">New order</a>
                @endif
                <a href="{{ route('tables.edit', $table) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">No tables set up yet.</div></div></div>
@endforelse
</div>
@endsection
