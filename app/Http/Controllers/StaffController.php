<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::orderBy('name')->paginate(15);

        return view('staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('staff.form', ['staff' => new User(['role' => User::ROLE_CASHIER, 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,cashier'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        User::create($data);

        return redirect()->route('staff.index')->with('status', 'Staff member added.');
    }

    public function edit(User $staff): View
    {
        return view('staff.form', compact('staff'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$staff->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,cashier'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        if ($staff->id === $request->user()->id && (! $data['is_active'] || $data['role'] !== User::ROLE_ADMIN)) {
            return back()->with('error', 'You cannot remove your own admin access.');
        }

        $staff->update($data);

        return redirect()->route('staff.index')->with('status', 'Staff member updated.');
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        if ($staff->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('status', 'Staff member removed.');
    }
}
