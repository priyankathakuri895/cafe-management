@extends('layouts.app')
@section('title', 'Menu items')
@section('subtitle', 'What the cafe sells, and what each item costs in stock')

@section('content')
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Item name…">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Category</label>
            <select name="category" class="form-select form-select-sm">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button class="btn btn-sm btn-cafe">Filter</button>
            <a href="{{ route('menu.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <a href="{{ route('menu.create') }}" class="btn btn-sm btn-cafe ms-auto"><i class="bi bi-plus-lg"></i> New item</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th></th><th>Item</th><th>Category</th><th class="text-end">Price</th><th>Available</th><th></th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td style="width:52px">
                        @if($item->image_path)
                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:.35rem">
                        @else
                            <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted" style="width:40px;height:40px;border-radius:.35rem"><i class="bi bi-cup-hot"></i></span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->name }}</div>
                        @if($item->description)<div class="small text-muted">{{ Str::limit($item->description, 60) }}</div>@endif
                    </td>
                    <td>{{ $item->category?->name }}</td>
                    <td class="text-end">@money($item->price)</td>
                    <td><span class="badge text-bg-{{ $item->is_available ? 'success' : 'secondary' }}">{{ $item->is_available ? 'Yes' : 'No' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('recipes.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Ingredients used per sale"><i class="bi bi-box-seam"></i> Recipe</a>
                        <a href="{{ route('menu.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('menu.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Delete this item?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No menu items yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
