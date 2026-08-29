@extends('layouts.app')
@section('title', 'Recipe — '.$item->name)
@section('subtitle', 'Ingredients consumed each time one is sold. Stock is deducted automatically when the bill is paid.')

@section('content')
<div class="row"><div class="col-lg-8">
<div class="card">
    <form method="POST" action="{{ route('recipes.update', $item) }}">
        @csrf @method('PUT')
        <div class="card-body">
            <table class="table align-middle" id="recipeTable">
                <thead class="table-light"><tr><th>Ingredient</th><th style="width:190px">Quantity per item</th><th style="width:60px"></th></tr></thead>
                <tbody>
                @foreach($item->recipeItems as $i => $row)
                    <tr>
                        <td>
                            <select name="rows[{{ $i }}][ingredient_id]" class="form-select form-select-sm">
                                <option value="">— remove —</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" @selected($row->ingredient_id == $ing->id)>{{ $ing->name }} ({{ $ing->unit }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.001" min="0" name="rows[{{ $i }}][quantity]" value="{{ (float) $row->quantity }}" class="form-control form-control-sm"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRow()"><i class="bi bi-plus-lg"></i> Add ingredient</button>
            @if($ingredients->isEmpty())
                <p class="text-muted small mt-3 mb-0">No ingredients defined yet — <a href="{{ route('ingredients.create') }}">add some first</a>.</p>
            @endif
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-cafe">Save recipe</button>
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Back to menu</a>
        </div>
    </form>
</div>
</div></div>
@endsection

@push('scripts')
<script>
    let rowIndex = {{ $item->recipeItems->count() }};
    const options = `@foreach($ingredients as $ing)<option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>@endforeach`;

    function addRow() {
        const tbody = document.querySelector('#recipeTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML =
            '<td><select name="rows[' + rowIndex + '][ingredient_id]" class="form-select form-select-sm"><option value="">Choose…</option>' + options + '</select></td>' +
            '<td><input type="number" step="0.001" min="0" name="rows[' + rowIndex + '][quantity]" class="form-control form-control-sm" placeholder="0.000"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest(\'tr\').remove()"><i class="bi bi-x"></i></button></td>';
        tbody.appendChild(tr);
        rowIndex++;
    }
</script>
@endpush
