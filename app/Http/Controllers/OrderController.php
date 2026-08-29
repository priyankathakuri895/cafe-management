<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Order;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function index(Request $request): View
    {
        $orders = Order::with(['table', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($q) => $q->where('order_number', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'table', 'user']);

        return view('orders.show', compact('order'));
    }

    public function receipt(Order $order): View
    {
        $order->load(['items', 'table', 'user']);

        return view('orders.receipt', compact('order'));
    }

    public function pay(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_OPEN) {
            return back()->with('error', 'This order is already closed.');
        }

        $data = $request->validate([
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,online'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $order->recalculate((float) ($data['discount'] ?? 0));

        if ((float) $data['paid_amount'] + 0.001 < (float) $order->total) {
            return back()->with('error', 'Amount received is less than the bill total.');
        }

        $order->update([
            'status' => Order::STATUS_PAID,
            'payment_method' => $data['payment_method'],
            'paid_amount' => $data['paid_amount'],
            'paid_at' => now(),
        ]);

        $this->stock->deductForOrder($order, $request->user()->id);
        $this->freeTable($order);

        return redirect()->route('orders.show', $order)->with('status', 'Payment recorded. Bill settled.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_PAID) {
            return back()->with('error', 'A paid order cannot be cancelled.');
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);
        $this->freeTable($order);

        return redirect()->route('orders.index')->with('status', 'Order cancelled.');
    }

    private function freeTable(Order $order): void
    {
        if (! $order->cafe_table_id) {
            return;
        }

        $stillOpen = Order::where('cafe_table_id', $order->cafe_table_id)
            ->where('status', Order::STATUS_OPEN)
            ->exists();

        if (! $stillOpen) {
            CafeTable::where('id', $order->cafe_table_id)
                ->update(['status' => CafeTable::STATUS_AVAILABLE]);
        }
    }
}
