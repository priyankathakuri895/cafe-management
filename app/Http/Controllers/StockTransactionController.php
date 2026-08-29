<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockTransaction;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function index(Request $request): View
    {
        $transactions = StockTransaction::with(['ingredient', 'user'])
            ->when($request->filled('ingredient'), fn ($q) => $q->where('ingredient_id', $request->integer('ingredient')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $ingredients = Ingredient::orderBy('name')->get();

        return view('stock.index', compact('transactions', 'ingredients'));
    }

    public function create(): View
    {
        return view('stock.form', ['ingredients' => Ingredient::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'type' => ['required', 'in:purchase,wastage,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);
        $qty = (float) $data['quantity'];
        $userId = $request->user()->id;

        if ($data['type'] === 'adjustment') {
            $result = $this->stock->adjustToCount($ingredient, $qty, $data['note'] ?? 'Physical count', $userId);

            if (! $result) {
                return back()->with('error', 'The counted quantity matches the recorded stock, nothing to adjust.');
            }
        } elseif ($data['type'] === 'wastage') {
            if ($qty > (float) $ingredient->stock_qty) {
                return back()->withInput()->with('error', 'Wastage cannot be more than the stock on hand.');
            }

            $this->stock->record($ingredient, 'wastage', -$qty, $data['note'] ?? null, null, null, $userId);
        } else {
            $this->stock->record($ingredient, 'purchase', $qty, $data['note'] ?? null, $data['unit_cost'] ?? null, null, $userId);
        }

        return redirect()->route('stock.index')->with('status', 'Stock movement recorded.');
    }
}
