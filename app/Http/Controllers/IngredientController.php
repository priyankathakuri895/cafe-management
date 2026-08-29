<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(Request $request): View
    {
        $ingredients = Ingredient::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->boolean('low'), fn ($q) => $q->whereColumn('stock_qty', '<=', 'reorder_level'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('ingredients.index', compact('ingredients'));
    }

    public function create(): View
    {
        return view('ingredients.form', ['ingredient' => new Ingredient(['unit' => 'kg'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Ingredient::create($this->validated($request));

        return redirect()->route('ingredients.index')->with('status', 'Ingredient added.');
    }

    public function edit(Ingredient $ingredient): View
    {
        $ingredient->load(['transactions' => fn ($q) => $q->latest('id')->limit(15)]);

        return view('ingredients.form', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $data = $this->validated($request, $ingredient);
        unset($data['stock_qty']); // stock only moves through stock transactions

        $ingredient->update($data);

        return redirect()->route('ingredients.index')->with('status', 'Ingredient updated.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $ingredient->delete();

        return redirect()->route('ingredients.index')->with('status', 'Ingredient deleted.');
    }

    private function validated(Request $request, ?Ingredient $ingredient = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:ingredients,name'.($ingredient ? ','.$ingredient->id : '')],
            'unit' => ['required', 'in:kg,g,l,ml,pcs'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
