@extends('layouts.app')
@section('title', $reservation->exists ? 'Edit reservation' : 'New reservation')

@section('content')
<div class="row"><div class="col-lg-7">
<div class="card">
    <form method="POST" action="{{ $reservation->exists ? route('reservations.update', $reservation) : route('reservations.store') }}">
        @csrf
        @if($reservation->exists) @method('PUT') @endif
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Customer name</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $reservation->phone) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Date &amp; time</label>
                <input type="datetime-local" name="reserved_at" class="form-control" required
                       value="{{ old('reserved_at', $reservation->reserved_at?->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Guests</label>
                <input type="number" name="guests" min="1" value="{{ old('guests', $reservation->guests ?? 2) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Table</label>
                <select name="cafe_table_id" class="form-select">
                    <option value="">Not assigned yet</option>
                    @foreach($tables as $t)
                        <option value="{{ $t->id }}" @selected(old('cafe_table_id', $reservation->cafe_table_id) == $t->id)>{{ $t->name }} ({{ $t->capacity }} seats)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    @foreach(\App\Models\Reservation::statuses() as $k => $v)
                        <option value="{{ $k }}" @selected(old('status', $reservation->status) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Notes</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes', $reservation->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
</div></div>
@endsection
