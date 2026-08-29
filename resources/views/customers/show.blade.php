@extends('layouts.app')
@section('title', $customer->name)
@section('subtitle', $customer->phone ?? 'No phone on file')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card stat-card"><div class="card-body">
            <div class="label">Total spent</div>
            <div class="value">@money($totalSpent)</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card"><div class="card-body">
            <div class="label">Paid visits</div>
            <div class="value">{{ $visitCount }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card"><div class="card-body">
            <div class="label">Average bill</div>
            <div class="value">@money($visitCount > 0 ? $totalSpent / $visitCount : 0)</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Order history</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Order</th><th>Placed</th><th>Table</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->order_number }}</td>
                            <td class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $order->table?->name ?? '—' }}</td>
                            <td class="text-end">@money($order->total)</td>
                            <td><span class="badge text-bg-{{ $order->statusColor() }} text-capitalize">{{ $order->status }}</span></td>
                            <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No orders yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $orders->links() }}</div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Details</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Phone</span><span>{{ $customer->phone ?? '—' }}</span></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Email</span><span>{{ $customer->email ?? '—' }}</span></div>
                @if($customer->notes)
                    <div class="py-1"><span class="text-muted d-block">Notes</span>{{ $customer->notes }}</div>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary flex-fill">Edit</a>
            <a href="{{ route('pos.index') }}" class="btn btn-cafe flex-fill"><i class="bi bi-plus-lg"></i> New order</a>
        </div>
    </div>
</div>
@endsection
