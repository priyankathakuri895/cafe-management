@extends('layouts.app')
@section('title', 'Orders & bills')
@section('subtitle', 'Every order taken, open or settled')

@section('content')
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Order number</label>
                <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ORD-…">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['open' => 'Open', 'paid' => 'Paid', 'cancelled' => 'Cancelled'] as $k => $v)
                        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-cafe">Filter</button>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order</th><th>Placed</th><th>Table</th><th>Type</th>
                    <th>Taken by</th><th class="text-end">Total</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                    <td>{{ $order->table?->name ?? '—' }}</td>
                    <td class="text-capitalize">{{ str_replace('_', ' ', $order->order_type) }}</td>
                    <td class="small">{{ $order->user?->name ?? '—' }}</td>
                    <td class="text-end">@money($order->total)</td>
                    <td><span class="badge text-bg-{{ $order->statusColor() }} text-capitalize">{{ $order->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No orders match this filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $orders->links() }}</div>
@endsection
