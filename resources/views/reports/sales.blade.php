@extends('layouts.app')
@section('title', 'Sales report')
@section('subtitle', $from->format('d M Y').' — '.$to->format('d M Y'))

@section('content')
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">From</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">To</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-6 d-flex gap-2">
            <button class="btn btn-sm btn-cafe">Run report</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">Print</button>
        </div>
    </form>
</div></div>

<div class="row g-3 mb-3">
    @foreach([
        ['Bills settled', $summary['orders'], false],
        ['Gross sales', $summary['gross'], true],
        ['Discounts given', $summary['discount'], true],
        ['Service charge', $summary['service'], true],
        ['Tax collected', $summary['tax'], true],
        ['Net collected', $summary['net'], true],
    ] as [$label, $value, $isMoney])
        <div class="col-6 col-lg-2">
            <div class="card stat-card h-100"><div class="card-body">
                <div class="label">{{ $label }}</div>
                <div class="value fs-5">@if($isMoney)@money($value)@else{{ $value }}@endif</div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">Day by day</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Date</th><th class="text-center">Bills</th><th class="text-end">Sales</th></tr></thead>
                    <tbody>
                    @forelse($byDay as $row)
                        <tr><td>{{ \Illuminate\Support\Carbon::parse($row->day)->format('d M Y') }}</td><td class="text-center">{{ $row->orders }}</td><td class="text-end">@money($row->amount)</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No sales in this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">By payment method</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Method</th><th class="text-center">Bills</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                    @forelse($byPayment as $row)
                        <tr><td class="text-uppercase">{{ $row->payment_method ?? '—' }}</td><td class="text-center">{{ $row->orders }}</td><td class="text-end">@money($row->amount)</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Nothing to show.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Item sales</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Item</th><th class="text-center">Quantity</th><th class="text-end">Revenue</th></tr></thead>
                    <tbody>
                    @forelse($byItem as $row)
                        <tr><td>{{ $row->item_name }}</td><td class="text-center">{{ (int) $row->qty }}</td><td class="text-end">@money($row->revenue)</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No items sold in this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
