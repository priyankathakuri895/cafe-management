@extends('layouts.guest')
@section('title', 'Sign in')

@section('content')
    <div class="text-center mb-4">
        <div class="display-6"><i class="bi bi-cup-hot-fill text-warning"></i></div>
        <h1 class="h5 mb-1">{{ config('cafe.name') }}</h1>
        <p class="text-muted small mb-0">Sign in to the management system</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
            <label class="form-check-label small" for="remember">Keep me signed in</label>
        </div>
        <button class="btn btn-cafe w-100">Sign in</button>
    </form>
@endsection
