@extends('layouts.app')
@section('title', $item->exists ? 'Edit menu item' : 'New menu item')

@section('content')
<div class="row"><div class="col-lg-7">
<div class="card">
    <form method="POST" action="{{ $item->exists ? route('menu.update', $item) : route('menu.store') }}" enctype="multipart/form-data">
        @csrf
        @if($item->exists) @method('PUT') @endif
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Name</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Code (optional)</label>
                    <input type="text" name="code" value="{{ old('code', $item->code) }}" class="form-control" placeholder="e.g. CAP01">
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Choose…</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('category_id', $item->category_id) == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Price ({{ config('cafe.currency') }})</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', (float) $item->price) }}" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $item->description) }}</textarea>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Photo (optional)</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                    @if($item->image_path)
                        <div class="mt-2"><img src="{{ asset('storage/'.$item->image_path) }}" style="height:70px;border-radius:.35rem"></div>
                    @endif
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_available" value="1" class="form-check-input" id="avail" @checked(old('is_available', $item->is_available ?? true))>
                        <label class="form-check-label" for="avail">Available for sale</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save</button>
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Cancel</a>
            @if($item->exists)
                <a href="{{ route('recipes.edit', $item) }}" class="btn btn-outline-secondary ms-auto">Edit recipe</a>
            @endif
        </div>
    </form>
</div>
</div></div>
@endsection
