@extends('layouts.app')
@section('title', $customer->exists ? 'Edit customer' : 'New customer')

@section('content')
<div class="row g-3">
<div class="col-lg-6">
<div class="card">
    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if($customer->exists) @method('PUT') @endif
        <div class="card-body row g-3">
            <div class="col-12">
                <label class="form-label small fw-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Notes</label>
                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $customer->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ $customer->exists ? route('customers.show', $customer) : route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
            @if($customer->exists)
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="ms-auto" onsubmit="return confirm('Delete this customer? Their past orders stay on record.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger">Delete</button>
                </form>
            @endif
        </div>
    </form>
</div>
</div>
</div>
@endsection
