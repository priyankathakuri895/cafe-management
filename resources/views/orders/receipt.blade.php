<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->order_number }}</title>
    <style>
        body { font-family: "Courier New", monospace; font-size: 12px; color: #000; margin: 0; padding: 12px; }
        .slip { width: 300px; margin: 0 auto; }
        h1 { font-size: 15px; text-align: center; margin: 0 0 2px; }
        .center { text-align: center; }
        .muted { color: #444; font-size: 11px; }
        hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .r { text-align: right; }
        .tot td { font-weight: bold; font-size: 13px; }
        @media print { .noprint { display: none; } body { padding: 0; } }
    </style>
</head>
<body onload="window.print()">
<div class="slip">
    <h1>{{ config('cafe.name') }}</h1>
    <div class="center muted">
        {{ config('cafe.address') }}<br>
        @if(config('cafe.phone')) Tel: {{ config('cafe.phone') }}<br> @endif
    </div>
    <hr>
    <div class="muted">
        Bill: {{ $order->order_number }}<br>
        Date: {{ ($order->paid_at ?? $order->created_at)->format('d M Y, h:i A') }}<br>
        Table: {{ $order->table?->name ?? ucfirst(str_replace('_', ' ', $order->order_type)) }}<br>
        Served by: {{ $order->user?->name ?? '—' }}
        @if($order->customer_name)<br>Customer: {{ $order->customer_name }}@endif
    </div>
    <hr>
    <table>
        @foreach($order->items as $line)
            <tr>
                <td>{{ $line->item_name }}<br><span class="muted">{{ $line->quantity }} x {{ number_format((float) $line->unit_price, 2) }}</span></td>
                <td class="r">{{ number_format((float) $line->line_total, 2) }}</td>
            </tr>
        @endforeach
    </table>
    <hr>
    <table>
        <tr><td>Subtotal</td><td class="r">{{ number_format((float) $order->subtotal, 2) }}</td></tr>
        @if((float) $order->discount > 0)
            <tr><td>Discount</td><td class="r">-{{ number_format((float) $order->discount, 2) }}</td></tr>
        @endif
        @if((float) $order->service_charge > 0)
            <tr><td>Service charge</td><td class="r">{{ number_format((float) $order->service_charge, 2) }}</td></tr>
        @endif
        @if((float) $order->tax > 0)
            <tr><td>Tax</td><td class="r">{{ number_format((float) $order->tax, 2) }}</td></tr>
        @endif
        <tr class="tot"><td>TOTAL</td><td class="r">{{ config('cafe.currency') }} {{ number_format((float) $order->total, 2) }}</td></tr>
        @if($order->status === 'paid')
            <tr><td>Paid ({{ strtoupper($order->payment_method) }})</td><td class="r">{{ number_format((float) $order->paid_amount, 2) }}</td></tr>
            <tr><td>Change</td><td class="r">{{ number_format(max(0, (float) $order->paid_amount - (float) $order->total), 2) }}</td></tr>
        @endif
    </table>
    <hr>
    <div class="center muted">Thank you — please come again!</div>
    <div class="center noprint" style="margin-top:14px">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
</div>
</body>
</html>
