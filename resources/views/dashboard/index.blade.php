@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Today at a glance — '.now()->format('l, d M Y'))

@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100"><div class="card-body">
            <div class="label">Sales today</div>
            <div class="value">@money($stats['sales_today'])</div>
            <div class="small text-muted">{{ $stats['orders_today'] }} bills settled</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100"><div class="card-body">
            <div class="label">Average bill</div>
            <div class="value">@money($stats['average_bill'])</div>
            <div class="small text-muted">per settled order</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100"><div class="card-body">
            <div class="label">Open orders</div>
            <div class="value">{{ $stats['open_orders'] }}</div>
            <div class="small text-muted">running, not yet paid</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100"><div class="card-body">
            <div class="label">Tables free</div>
            <div class="value">{{ $stats['tables_free'] }} <span class="fs-6 text-muted">/ {{ $stats['tables_total'] }}</span></div>
            <div class="small text-muted">{{ $stats['menu_items'] }} items on the menu</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Open orders</span>
                <a href="{{ route('orders.index') }}?status=open" class="small text-decoration-none">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light"><tr><th>Order</th><th>Table</th><th>Type</th><th class="text-end">Total</th><th></th></tr></thead>
                    <tbody>
                    @forelse($openOrders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->order_number }}</td>
                            <td>{{ $order->table?->name ?? '—' }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $order->order_type) }}</td>
                            <td class="text-end">@money($order->total)</td>
                            <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No running orders right now.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Sales — last 7 days</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Day</th><th class="text-end">Sales</th></tr></thead>
                    <tbody>
                    @foreach($chart as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td class="text-end">@money($row['amount'])</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">Best sellers — last 30 days</div>
            <ul class="list-group list-group-flush">
                @forelse($topItems as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $item->item_name }}</span>
                        <span class="text-muted small">{{ (int) $item->qty }} sold &middot; @money($item->revenue)</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">No sales recorded yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Low stock</span>
                <a href="{{ route('ingredients.index') }}?low=1" class="small text-decoration-none">Inventory</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($lowStock as $ing)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $ing->name }}</span>
                        <span class="badge text-bg-danger">{{ rtrim(rtrim(number_format((float) $ing->stock_qty, 3), '0'), '.') }} {{ $ing->unit }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">Everything is above its reorder level.</li>
                @endforelse
            </ul>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Upcoming reservations</span>
                <a href="{{ route('reservations.index') }}" class="small text-decoration-none">All</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($upcoming as $r)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ $r->customer_name }}</span>
                            <span class="small text-muted">{{ $r->reserved_at->format('d M, h:i A') }}</span>
                        </div>
                        <div class="small text-muted">{{ $r->guests }} guests &middot; {{ $r->table?->name ?? 'no table yet' }}</div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">No upcoming reservations.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
