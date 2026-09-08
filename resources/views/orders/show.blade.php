@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('subtitle', $order->created_at->format('d M Y, h:i A').' · taken by '.($order->user?->name ?? 'unknown'))

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Items</span>
                <span class="badge text-bg-{{ $order->statusColor() }} text-capitalize">{{ $order->status }}</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light"><tr><th>Item</th><th class="text-end">Price</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $line)
                        <tr>
                            <td>{{ $line->item_name }}@if($line->note)<div class="small text-muted">{{ $line->note }}</div>@endif</td>
                            <td class="text-end">@money($line->unit_price)</td>
                            <td class="text-center">{{ $line->quantity }}</td>
                            <td class="text-end">@money($line->line_total)</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">@money($order->subtotal)</td></tr>
                        <tr><td colspan="3" class="text-end">Discount</td><td class="text-end">− @money($order->discount)</td></tr>
                        <tr><td colspan="3" class="text-end">Service charge</td><td class="text-end">@money($order->service_charge)</td></tr>
                        <tr><td colspan="3" class="text-end">Tax</td><td class="text-end">@money($order->tax)</td></tr>
                        <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end">@money($order->total)</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">Details</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Table</span><span>{{ $order->table?->name ?? '—' }}</span></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Type</span><span class="text-capitalize">{{ str_replace('_', ' ', $order->order_type) }}</span></div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Customer</span>
                    <span>
                        @if($order->customer_id)
                            <a href="{{ route('customers.show', $order->customer_id) }}">{{ $order->customer_name ?? '—' }}</a>
                        @else
                            {{ $order->customer_name ?? '—' }}
                        @endif
                    </span>
                </div>
                @if($order->note)
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Note</span><span class="text-end">{{ $order->note }}</span></div>
                @endif
                @if($order->status === 'paid')
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Paid at</span><span>{{ $order->paid_at?->format('d M Y, h:i A') }}</span></div>
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Method</span><span class="text-uppercase">{{ $order->payment_method }}</span></div>
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Received</span><span>@money($order->paid_amount)</span></div>
                    <div class="d-flex justify-content-between py-1 fw-semibold"><span>Change given</span><span>@money(max(0, (float) $order->paid_amount - (float) $order->total))</span></div>
                @endif
            </div>
        </div>

        <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="btn btn-cafe w-100 mb-3">
            <i class="bi bi-printer"></i> {{ $order->status === 'open' ? 'Print bill' : 'Print receipt' }}
        </a>

        @if($order->status === 'open')
            <div class="card mb-3">
                <div class="card-header">Settle the bill</div>
                <form method="POST" action="{{ route('orders.pay', $order) }}" class="card-body">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small mb-1">Discount</label>
                        <input type="number" step="0.01" min="0" name="discount" id="payDiscount" value="{{ (float) $order->discount }}" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small mb-1">Payment method</label>
                        <select name="payment_method" id="payMethod" class="form-select form-select-sm">
                            <option value="cash">Cash</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="qrBox">
                        @if(config('cafe.bank.account_number'))
                            <label class="form-label small mb-1">Scan to pay {{ config('cafe.currency') }} {{ number_format((float) $order->total, 2) }}</label>
                            <div id="payQr" class="d-flex justify-content-center p-2 bg-white border rounded"></div>
                            <div class="small text-muted text-center mt-1">
                                {{ config('cafe.bank.account_name') }} · {{ config('cafe.bank.name') }} · {{ config('cafe.bank.account_number') }}
                            </div>
                        @else
                            <div class="alert alert-light border small mb-0">
                                Add <code>CAFE_BANK_NAME</code>, <code>CAFE_BANK_ACCOUNT_NAME</code> and
                                <code>CAFE_BANK_ACCOUNT_NUMBER</code> to <code>.env</code> to show a payment QR here.
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label small mb-1">Amount received</label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="payAmount" value="{{ (float) $order->total }}" class="form-control form-control-sm" required>
                        <div class="form-text" id="changeHint"></div>
                    </div>
                    <button class="btn btn-cafe w-100"><i class="bi bi-check2-circle"></i> Mark as paid</button>
                </form>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('pos.index') }}?order={{ $order->id }}" class="btn btn-outline-secondary flex-fill"><i class="bi bi-pencil"></i> Edit items</a>
                <form method="POST" action="{{ route('orders.cancel', $order) }}" class="flex-fill" onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button class="btn btn-outline-danger w-100">Cancel order</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@if($order->status === 'open' && config('cafe.bank.account_number'))
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    @endpush
@endif

@push('scripts')
<script>
    const total = {{ (float) $order->total }};
    const amountEl = document.getElementById('payAmount');
    if (amountEl) {
        const hint = document.getElementById('changeHint');
        const update = () => {
            const change = (parseFloat(amountEl.value) || 0) - total;
            hint.textContent = change >= 0
                ? 'Change to return: {{ config('cafe.currency') }} ' + change.toFixed(2)
                : 'Short by {{ config('cafe.currency') }} ' + Math.abs(change).toFixed(2);
        };
        amountEl.addEventListener('input', update);
        update();
    }

    function toggleQr() {
        const methodEl = document.getElementById('payMethod');
        const box = document.getElementById('qrBox');
        if (!methodEl || !box) return;

        const showQr = methodEl.value === 'online';
        box.classList.toggle('d-none', !showQr);

        const qrEl = document.getElementById('payQr');
        if (showQr && qrEl && !qrEl.dataset.rendered) {
            qrEl.dataset.rendered = '1';
            new QRCode(qrEl, {
                text: @json(
                    "Pay to: ".(config('cafe.bank.account_name') ?: config('cafe.name'))
                    ."\nBank: ".config('cafe.bank.name')
                    ."\nA/C No: ".config('cafe.bank.account_number')
                    ."\nAmount: ".config('cafe.currency')." ".number_format((float) $order->total, 2)
                    ."\nBill: ".$order->order_number
                ),
                width: 180,
                height: 180,
            });
        }
    }

    document.getElementById('payMethod')?.addEventListener('change', toggleQr);
    toggleQr();
</script>
@endpush
