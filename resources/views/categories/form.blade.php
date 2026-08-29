@extends('layouts.app')
@section('title', $category->exists ? 'Edit category' : 'New category')

@section('content')
<div class="row"><div class="col-lg-6">
<div class="card">
    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Description</label>
                <input type="text" name="description" value="{{ old('description', $category->description) }}" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-control" min="0">
                <div class="form-text">Lower numbers show first in the POS.</div>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', $category->is_active ?? true))>
                <label class="form-check-label" for="active">Show this category in the POS</label>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
</div></div>
@endsection
