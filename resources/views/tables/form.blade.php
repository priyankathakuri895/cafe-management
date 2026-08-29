@extends('layouts.app')
@section('title', $table->exists ? 'Edit table' : 'Add table')

@section('content')
<div class="row"><div class="col-lg-6">
<div class="card">
    <form method="POST" action="{{ $table->exists ? route('tables.update', $table) : route('tables.store') }}">
        @csrf
        @if($table->exists) @method('PUT') @endif
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Table name / number</label>
                <input type="text" name="name" value="{{ old('name', $table->name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Seats</label>
                <input type="number" name="capacity" min="1" value="{{ old('capacity', $table->capacity ?? 4) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Location</label>
                <input type="text" name="location" value="{{ old('location', $table->location) }}" class="form-control" placeholder="Terrace, Main hall…">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    @foreach(\App\Models\CafeTable::statuses() as $k => $v)
                        <option value="{{ $k }}" @selected(old('status', $table->status) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary">Cancel</a>
            @if($table->exists)
                <form method="POST" action="{{ route('tables.destroy', $table) }}" class="ms-auto" onsubmit="return confirm('Delete this table?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger">Delete</button>
                </form>
            @endif
        </div>
    </form>
</div>
</div></div>
@endsection
