<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount(['orders as paid_orders_count' => fn ($q) => $q->where('status', 'paid')])
            ->withSum(['orders as total_spent_sum' => fn ($q) => $q->where('status', 'paid')], 'total')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('phone', 'like', '%'.$term.'%'));
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.form', ['customer' => new Customer]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = Customer::create($this->validated($request));

        return redirect()->route('customers.show', $customer)->with('status', 'Customer added.');
    }

    public function show(Customer $customer): View
    {
        $orders = $customer->orders()->with('table')->latest('id')->paginate(15);

        return view('customers.show', [
            'customer' => $customer,
            'orders' => $orders,
            'totalSpent' => $customer->totalSpent(),
            'visitCount' => $customer->visitCount(),
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('customers.show', $customer)->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer deleted.');
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:customers,phone'.($customer ? ','.$customer->id : '')],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
