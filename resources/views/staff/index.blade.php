@extends('layouts.app')
@section('title', 'Staff')
@section('subtitle', 'Who can sign in, and what they can reach')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('staff.create') }}" class="btn btn-cafe btn-sm"><i class="bi bi-plus-lg"></i> Add staff</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($staff as $person)
                <tr>
                    <td class="fw-semibold">{{ $person->name }}</td>
                    <td>{{ $person->email }}</td>
                    <td>{{ $person->phone ?? '—' }}</td>
                    <td><span class="badge text-bg-{{ $person->isAdmin() ? 'dark' : 'secondary' }}">{{ \App\Models\User::roles()[$person->role] ?? $person->role }}</span></td>
                    <td><span class="badge text-bg-{{ $person->is_active ? 'success' : 'secondary' }}">{{ $person->is_active ? 'Active' : 'Disabled' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('staff.edit', $person) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @if($person->id !== auth()->id())
                            <form method="POST" action="{{ route('staff.destroy', $person) }}" class="d-inline" onsubmit="return confirm('Remove this staff member?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $staff->links() }}</div>
@endsection
