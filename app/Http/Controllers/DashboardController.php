<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();

        $paidToday = Order::paid()->whereDate('paid_at', $today);

        $stats = [
            'sales_today' => (float) (clone $paidToday)->sum('total'),
            'orders_today' => (clone $paidToday)->count(),
            'open_orders' => Order::where('status', Order::STATUS_OPEN)->count(),
            'tables_free' => CafeTable::where('status', CafeTable::STATUS_AVAILABLE)->count(),
            'tables_total' => CafeTable::count(),
            'menu_items' => MenuItem::count(),
        ];

        $stats['average_bill'] = $stats['orders_today'] > 0
            ? $stats['sales_today'] / $stats['orders_today']
            : 0;

        $salesByDay = Order::paid()
            ->whereDate('paid_at', '>=', $today->copy()->subDays(6))
            ->selectRaw('DATE(paid_at) as day, SUM(total) as amount')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('amount', 'day');

        $chart = collect(range(6, 0))->map(function ($back) use ($today, $salesByDay) {
            $date = $today->copy()->subDays($back);

            return [
                'label' => $date->format('D'),
                'amount' => (float) ($salesByDay[$date->toDateString()] ?? 0),
            ];
        });

        $topItems = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_PAID)
            ->whereDate('orders.paid_at', '>=', $today->copy()->subDays(29))
            ->selectRaw('order_items.item_name, SUM(order_items.quantity) as qty, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.item_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $lowStock = Ingredient::whereColumn('stock_qty', '<=', 'reorder_level')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $upcoming = Reservation::with('table')
            ->where('status', 'booked')
            ->where('reserved_at', '>=', now()->startOfDay())
            ->orderBy('reserved_at')
            ->limit(6)
            ->get();

        $openOrders = Order::with('table')
            ->where('status', Order::STATUS_OPEN)
            ->latest('id')
            ->limit(6)
            ->get();

        return view('dashboard.index', compact('stats', 'chart', 'topItems', 'lowStock', 'upcoming', 'openOrders'));
    }
}
