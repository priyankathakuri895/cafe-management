<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $from = $request->date('from') ?? today()->startOfMonth();
        $to = $request->date('to') ?? today();

        $orders = Order::paid()->whereBetween('paid_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $summary = [
            'orders' => (clone $orders)->count(),
            'gross' => (float) (clone $orders)->sum('subtotal'),
            'discount' => (float) (clone $orders)->sum('discount'),
            'service' => (float) (clone $orders)->sum('service_charge'),
            'tax' => (float) (clone $orders)->sum('tax'),
            'net' => (float) (clone $orders)->sum('total'),
        ];

        $byDay = (clone $orders)
            ->selectRaw('DATE(paid_at) as day, COUNT(*) as orders, SUM(total) as amount')
            ->groupBy('day')->orderBy('day')->get();

        $byPayment = (clone $orders)
            ->selectRaw('payment_method, COUNT(*) as orders, SUM(total) as amount')
            ->groupBy('payment_method')->get();

        $byItem = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_PAID)
            ->whereBetween('orders.paid_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('order_items.item_name, SUM(order_items.quantity) as qty, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.item_name')
            ->orderByDesc('revenue')
            ->get();

        return view('reports.sales', compact('from', 'to', 'summary', 'byDay', 'byPayment', 'byItem'));
    }
}
