@extends('layouts.app')
@section('title', $staff->exists ? 'Edit staff member' : 'Add staff member')

@section('content')
<div class="row"><div class="col-lg-6">
<div class="card">
    <form method="POST" action="{{ $staff->exists ? route('staff.update', $staff) : route('staff.store') }}">
        @csrf
        @if($staff->exists) @method('PUT') @endif
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name', $staff->name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Role</label>
                <select name="role" class="form-select">
                    @foreach(\App\Models\User::roles() as $k => $v)
                        <option value="{{ $k }}" @selected(old('role', $staff->role) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <div class="form-text">Cashiers cannot reach staff management or sales reports.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Password {{ $staff->exists ? '(leave blank to keep)' : '' }}</label>
                <input type="password" name="password" class="form-control" {{ $staff->exists ? '' : 'required' }}>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Confirm password</label>
                <input type="password" name="password_confirmation" class="form-control" {{ $staff->exists ? '' : 'required' }}>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isactive" @checked(old('is_active', $staff->is_active ?? true))>
                    <label class="form-check-label" for="isactive">Allow this person to sign in</label>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
</div></div>
@endsection
