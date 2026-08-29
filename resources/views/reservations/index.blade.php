@extends('layouts.app')
@section('title', 'Reservations')
@section('subtitle', 'Bookings taken by phone or in person')

@section('content')
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach(\App\Models\Reservation::statuses() as $k => $v)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 d-flex gap-2">
            <button class="btn btn-sm btn-cafe">Filter</button>
            <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <a href="{{ route('reservations.create') }}" class="btn btn-sm btn-cafe ms-auto"><i class="bi bi-plus-lg"></i> New reservation</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>When</th><th>Customer</th><th>Phone</th><th class="text-center">Guests</th><th>Table</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($reservations as $r)
                <tr>
                    <td>{{ $r->reserved_at->format('d M Y, h:i A') }}</td>
                    <td class="fw-semibold">{{ $r->customer_name }}</td>
                    <td>{{ $r->phone ?? '—' }}</td>
                    <td class="text-center">{{ $r->guests }}</td>
                    <td>{{ $r->table?->name ?? '—' }}</td>
                    <td><span class="badge text-bg-{{ $r->statusColor() }}">{{ \App\Models\Reservation::statuses()[$r->status] ?? $r->status }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('reservations.edit', $r) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('reservations.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Delete this reservation?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No reservations found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $reservations->links() }}</div>
@endsection
