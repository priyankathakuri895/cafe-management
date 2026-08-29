@extends('layouts.app')
@section('title', 'Menu categories')
@section('subtitle', 'Group the menu so the POS stays quick to use')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('categories.create') }}" class="btn btn-cafe btn-sm"><i class="bi bi-plus-lg"></i> New category</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Description</th><th class="text-center">Items</th><th class="text-center">Order</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td class="small text-muted">{{ $category->description ?? '—' }}</td>
                    <td class="text-center">{{ $category->menu_items_count }}</td>
                    <td class="text-center">{{ $category->sort_order }}</td>
                    <td><span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No categories yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
