<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = [
        'name', 'unit', 'stock_qty', 'reorder_level', 'cost_per_unit', 'supplier',
    ];

    protected $casts = [
        'stock_qty' => 'decimal:3',
        'reorder_level' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function isLow(): bool
    {
        return (float) $this->stock_qty <= (float) $this->reorder_level;
    }

    public static function units(): array
    {
        return ['kg' => 'Kilogram (kg)', 'g' => 'Gram (g)', 'l' => 'Litre (L)', 'ml' => 'Millilitre (ml)', 'pcs' => 'Pieces'];
    }
}
