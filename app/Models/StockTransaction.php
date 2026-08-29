<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'ingredient_id', 'type', 'quantity', 'stock_after',
        'unit_cost', 'order_id', 'user_id', 'note',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'stock_after' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function types(): array
    {
        return [
            'purchase' => 'Purchase (stock in)',
            'usage' => 'Usage (stock out)',
            'wastage' => 'Wastage (stock out)',
            'adjustment' => 'Adjustment (physical count)',
        ];
    }

    public function typeColor(): string
    {
        return match ($this->type) {
            'purchase' => 'success',
            'usage' => 'primary',
            'wastage' => 'danger',
            default => 'secondary',
        };
    }
}
