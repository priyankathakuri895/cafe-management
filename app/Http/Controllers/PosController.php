<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::active()
            ->with(['menuItems' => fn ($q) => $q->available()->orderBy('name')])
            ->orderBy('sort_order')->orderBy('name')
            ->get()
            ->filter(fn ($c) => $c->menuItems->isNotEmpty())
            ->values();

        $tables = CafeTable::where('status', '!=', CafeTable::STATUS_OUT)->orderBy('name')->get();

        $order = null;

        if ($request->filled('order')) {
            $order = Order::with(['items', 'customer'])
                ->where('status', Order::STATUS_OPEN)
                ->find($request->integer('order'));
        }

        $cartItems = $order
            ? $order->items->map(fn ($i) => [
                'id' => $i->menu_item_id,
                'name' => $i->item_name,
                'price' => (float) $i->unit_price,
                'qty' => $i->quantity,
            ])->values()
            : collect();

        return view('pos.index', compact('categories', 'tables', 'order', 'cartItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_id' => ['nullable', 'exists:orders,id'],
            'cafe_table_id' => ['nullable', 'exists:cafe_tables,id'],
            'order_type' => ['required', 'in:dine_in,takeaway,delivery'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
            'action' => ['required', 'in:save,pay'],
        ], [
            'items.required' => 'Add at least one item to the order.',
        ]);

        if ($data['order_type'] === 'dine_in' && empty($data['cafe_table_id'])) {
            return back()->withInput()->with('error', 'Choose a table for a dine-in order.');
        }

        $menuItems = MenuItem::whereIn('id', collect($data['items'])->pluck('menu_item_id'))->get()->keyBy('id');

        $customer = null;
        if (! empty($data['customer_phone'])) {
            $customer = Customer::firstOrCreate(
                ['phone' => $data['customer_phone']],
                ['name' => $data['customer_name'] ?? $data['customer_phone']]
            );
            if (! empty($data['customer_name']) && $customer->name !== $data['customer_name']) {
                $customer->update(['name' => $data['customer_name']]);
            }
        }

        $order = DB::transaction(function () use ($data, $menuItems, $request, $customer) {
            $order = $data['order_id']
                ? Order::where('status', Order::STATUS_OPEN)->findOrFail($data['order_id'])
                : new Order(['order_number' => Order::nextOrderNumber(), 'user_id' => $request->user()->id]);

            $order->fill([
                'cafe_table_id' => $data['order_type'] === 'dine_in' ? $data['cafe_table_id'] : null,
                'order_type' => $data['order_type'],
                'customer_id' => $customer?->id,
                'customer_name' => $data['customer_name'] ?? null,
                'note' => $data['note'] ?? null,
                'status' => Order::STATUS_OPEN,
            ]);
            $order->save();

            $order->items()->delete();

            foreach ($data['items'] as $line) {
                $menuItem = $menuItems[$line['menu_item_id']];
                $qty = (int) $line['quantity'];

                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'unit_price' => $menuItem->price,
                    'quantity' => $qty,
                    'line_total' => round((float) $menuItem->price * $qty, 2),
                    'note' => $line['note'] ?? null,
                ]);
            }

            $order->recalculate((float) ($data['discount'] ?? 0));

            if ($order->cafe_table_id) {
                CafeTable::where('id', $order->cafe_table_id)
                    ->update(['status' => CafeTable::STATUS_OCCUPIED]);
            }

            return $order;
        });

        if ($data['action'] === 'pay') {
            return redirect()->route('orders.show', $order)->with('status', 'Order saved. Take the payment below.');
        }

        return redirect()->route('orders.show', $order)->with('status', 'Order '.$order->order_number.' saved.');
    }
}
