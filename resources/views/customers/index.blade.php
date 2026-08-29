@extends('layouts.app')
@section('title', 'Customers')
@section('subtitle', 'Regulars and their billing history')

@section('content')
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name or phone…">
        </div>
        <div class="col-md-8 d-flex gap-2">
            <button class="btn btn-sm btn-cafe">Filter</button>
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <a href="{{ route('customers.create') }}" class="btn btn-sm btn-cafe ms-auto"><i class="bi bi-plus-lg"></i> New customer</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Phone</th><th>Email</th><th class="text-end">Visits</th><th class="text-end">Total spent</th><th></th></tr></thead>
            <tbody>
            @forelse($customers as $c)
                <tr>
                    <td class="fw-semibold"><a href="{{ route('customers.show', $c) }}" class="text-decoration-none">{{ $c->name }}</a></td>
                    <td>{{ $c->phone ?? '—' }}</td>
                    <td class="small text-muted">{{ $c->email ?? '—' }}</td>
                    <td class="text-end">{{ $c->paid_orders_count }}</td>
                    <td class="text-end">@money($c->total_spent_sum ?? 0)</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('customers.show', $c) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No customers yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
