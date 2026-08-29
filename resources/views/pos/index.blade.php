@extends('layouts.app')
@section('title', $order ? 'Edit order '.$order->order_number : 'New order')
@section('subtitle', 'Tap items to build the bill')

@push('styles')
<style>
    .pos-item { text-align: left; border: 1px solid #e6e3e0; border-radius: .55rem; background: #fff; padding: .6rem .7rem; width: 100%; height: 100%; transition: .12s; }
    .pos-item:hover { border-color: #b5651d; box-shadow: 0 2px 8px rgba(181,101,29,.15); }
    .pos-item .n { font-size: .87rem; font-weight: 600; line-height: 1.2; display: block; }
    .pos-item .p { font-size: .8rem; color: #b5651d; }
    .cart-panel { position: sticky; top: 1rem; }
    .cart-lines { max-height: 46vh; overflow-y: auto; }
    .qty-btn { width: 26px; height: 26px; line-height: 1; padding: 0; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('pos.store') }}" id="posForm">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order?->id }}">
    <input type="hidden" name="action" id="posAction" value="save">

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <input type="search" id="itemSearch" class="form-control form-control-sm" style="max-width:230px" placeholder="Search the menu…">
                        <div class="d-flex flex-wrap gap-1" id="catTabs">
                            <button type="button" class="btn btn-sm btn-cafe" data-cat="all">All</button>
                            @foreach($categories as $cat)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2" id="itemGrid">
                        @foreach($categories as $cat)
                            @foreach($cat->menuItems as $mi)
                                <div class="col pos-cell" data-cat="{{ $cat->id }}" data-name="{{ strtolower($mi->name) }}">
                                    <button type="button" class="pos-item"
                                            onclick="addItem({{ $mi->id }}, {{ json_encode($mi->name) }}, {{ (float) $mi->price }})">
                                        <span class="n">{{ $mi->name }}</span>
                                        <span class="p">@money($mi->price)</span>
                                        <span class="d-block text-muted" style="font-size:.72rem">{{ $cat->name }}</span>
                                    </button>
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    @if($categories->isEmpty())
                        <p class="text-muted text-center py-5 mb-0">
                            No menu items yet. <a href="{{ route('menu.create') }}">Add your first item</a>.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card cart-panel">
                <div class="card-header">Current order</div>
                <div class="card-body pb-2">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small mb-1">Order type</label>
                            <select name="order_type" id="orderType" class="form-select form-select-sm" onchange="toggleTable()">
                                @foreach(['dine_in' => 'Dine in', 'takeaway' => 'Takeaway', 'delivery' => 'Delivery'] as $k => $v)
                                    <option value="{{ $k }}" @selected(old('order_type', $order?->order_type ?? 'dine_in') === $k)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6" id="tableWrap">
                            <label class="form-label small mb-1">Table</label>
                            <select name="cafe_table_id" class="form-select form-select-sm">
                                <option value="">Choose…</option>
                                @foreach($tables as $t)
                                    <option value="{{ $t->id }}" @selected(old('cafe_table_id', $order?->cafe_table_id ?? request('table')) == $t->id)>
                                        {{ $t->name }} ({{ $t->capacity }}p) — {{ ucfirst(str_replace('_',' ', $t->status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-7">
                            <label class="form-label small mb-1">Customer (optional)</label>
                            <input type="text" name="customer_name" class="form-control form-control-sm" value="{{ old('customer_name', $order?->customer_name) }}">
                        </div>
                        <div class="col-5">
                            <label class="form-label small mb-1">Phone</label>
                            <input type="text" name="customer_phone" class="form-control form-control-sm" value="{{ old('customer_phone', $order?->customer?->phone) }}">
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="cart-lines" id="cartLines"></div>
                    <p class="text-muted small text-center py-3 mb-0" id="cartEmpty">No items added yet.</p>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between small"><span>Subtotal</span><span id="sumSubtotal">—</span></div>
                    <div class="d-flex justify-content-between align-items-center small my-1">
                        <span>Discount</span>
                        <input type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', $order?->discount ?? 0) }}"
                               class="form-control form-control-sm text-end" style="width:110px" oninput="renderCart()">
                    </div>
                    <div class="d-flex justify-content-between small"><span>Service charge ({{ config('cafe.service_charge_rate') }}%)</span><span id="sumService">—</span></div>
                    <div class="d-flex justify-content-between small"><span>Tax ({{ config('cafe.tax_rate') }}%)</span><span id="sumTax">—</span></div>
                    <div class="d-flex justify-content-between fw-semibold fs-5 mt-2 pt-2 border-top"><span>Total</span><span id="sumTotal">—</span></div>

                    <div class="mt-2">
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="Kitchen note (optional)" value="{{ old('note', $order?->note) }}">
                    </div>
                </div>
                <div class="card-footer bg-white d-grid gap-2">
                    <button type="submit" class="btn btn-cafe" onclick="document.getElementById('posAction').value='pay'">
                        <i class="bi bi-cash-coin"></i> Save &amp; take payment
                    </button>
                    <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('posAction').value='save'">
                        Save order (keep open)
                    </button>
                    <button type="button" class="btn btn-link btn-sm text-danger" onclick="cart=[];renderCart()">Clear items</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const CURRENCY = @json(config('cafe.currency'));
    const SERVICE_RATE = {{ (float) config('cafe.service_charge_rate') }};
    const TAX_RATE = {{ (float) config('cafe.tax_rate') }};

    let cart = @json($cartItems);

    const money = (n) => CURRENCY + ' ' + Number(n).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

    function addItem(id, name, price) {
        const line = cart.find(l => l.id === id);
        line ? line.qty++ : cart.push({id, name, price, qty: 1});
        renderCart();
    }

    function changeQty(id, delta) {
        const line = cart.find(l => l.id === id);
        if (!line) return;
        line.qty += delta;
        if (line.qty < 1) cart = cart.filter(l => l.id !== id);
        renderCart();
    }

    function renderCart() {
        const box = document.getElementById('cartLines');
        box.innerHTML = '';
        document.getElementById('cartEmpty').style.display = cart.length ? 'none' : 'block';

        let subtotal = 0;

        cart.forEach((l, i) => {
            const lineTotal = l.price * l.qty;
            subtotal += lineTotal;

            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-2 py-1 border-bottom';
            row.innerHTML =
                '<div class="flex-grow-1"><div class="small fw-semibold">' + l.name + '</div>' +
                '<div class="text-muted" style="font-size:.75rem">' + money(l.price) + ' each</div></div>' +
                '<div class="btn-group btn-group-sm">' +
                '<button type="button" class="btn btn-outline-secondary qty-btn" onclick="changeQty(' + l.id + ',-1)">−</button>' +
                '<span class="btn btn-light qty-btn disabled">' + l.qty + '</span>' +
                '<button type="button" class="btn btn-outline-secondary qty-btn" onclick="changeQty(' + l.id + ',1)">+</button>' +
                '</div>' +
                '<div class="text-end small" style="width:80px">' + money(lineTotal) + '</div>' +
                '<input type="hidden" name="items[' + i + '][menu_item_id]" value="' + l.id + '">' +
                '<input type="hidden" name="items[' + i + '][quantity]" value="' + l.qty + '">';
            box.appendChild(row);
        });

        let discount = parseFloat(document.getElementById('discount').value) || 0;
        discount = Math.min(discount, subtotal);
        const base = subtotal - discount;
        const service = Math.round(base * SERVICE_RATE) / 100;
        const tax = Math.round((base + service) * TAX_RATE) / 100;

        document.getElementById('sumSubtotal').textContent = money(subtotal);
        document.getElementById('sumService').textContent = money(service);
        document.getElementById('sumTax').textContent = money(tax);
        document.getElementById('sumTotal').textContent = money(base + service + tax);
    }

    function toggleTable() {
        const dineIn = document.getElementById('orderType').value === 'dine_in';
        document.getElementById('tableWrap').style.display = dineIn ? '' : 'none';
    }

    document.getElementById('itemSearch').addEventListener('input', filterItems);
    document.querySelectorAll('#catTabs button').forEach(btn => btn.addEventListener('click', () => {
        document.querySelectorAll('#catTabs button').forEach(b => { b.className = 'btn btn-sm btn-outline-secondary'; });
        btn.className = 'btn btn-sm btn-cafe';
        filterItems();
    }));

    function filterItems() {
        const term = document.getElementById('itemSearch').value.toLowerCase().trim();
        const activeBtn = document.querySelector('#catTabs .btn-cafe');
        const cat = activeBtn ? activeBtn.dataset.cat : 'all';

        document.querySelectorAll('.pos-cell').forEach(cell => {
            const matchCat = cat === 'all' || cell.dataset.cat === cat;
            const matchTerm = !term || cell.dataset.name.includes(term);
            cell.style.display = (matchCat && matchTerm) ? '' : 'none';
        });
    }

    document.getElementById('posForm').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Add at least one item before saving the order.');
        }
    });

    toggleTable();
    renderCart();
</script>
@endpush
