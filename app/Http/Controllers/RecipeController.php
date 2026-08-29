<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function edit(MenuItem $menu): View
    {
        $menu->load('recipeItems.ingredient');

        return view('recipes.edit', [
            'item' => $menu,
            'ingredients' => Ingredient::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $data = $request->validate([
            'rows' => ['nullable', 'array'],
            'rows.*.ingredient_id' => ['nullable', 'exists:ingredients,id'],
            'rows.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $rows = collect($data['rows'] ?? [])
            ->filter(fn ($row) => ! empty($row['ingredient_id']) && (float) ($row['quantity'] ?? 0) > 0)
            ->unique('ingredient_id');

        $menu->recipeItems()->delete();

        foreach ($rows as $row) {
            $menu->recipeItems()->create([
                'ingredient_id' => $row['ingredient_id'],
                'quantity' => $row['quantity'],
            ]);
        }

        return redirect()->route('menu.index')->with('status', 'Recipe saved for '.$menu->name.'.');
    }
}
