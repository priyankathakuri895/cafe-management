<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record a stock movement and keep the ingredient balance in sync.
     * $quantity is signed: positive adds stock, negative removes it.
     */
    public function record(
        Ingredient $ingredient,
        string $type,
        float $quantity,
        ?string $note = null,
        ?float $unitCost = null,
        ?int $orderId = null,
        ?int $userId = null,
    ): StockTransaction {
        return DB::transaction(function () use ($ingredient, $type, $quantity, $note, $unitCost, $orderId, $userId) {
            $ingredient->refresh();
            $after = round((float) $ingredient->stock_qty + $quantity, 3);

            $ingredient->update(['stock_qty' => $after]);

            return StockTransaction::create([
                'ingredient_id' => $ingredient->id,
                'type' => $type,
                'quantity' => round($quantity, 3),
                'stock_after' => $after,
                'unit_cost' => $unitCost,
                'order_id' => $orderId,
                'user_id' => $userId,
                'note' => $note,
            ]);
        });
    }

    /**
     * Set an ingredient to a counted physical quantity, storing the
     * difference as an adjustment rather than silently overwriting.
     */
    public function adjustToCount(Ingredient $ingredient, float $counted, ?string $note, ?int $userId = null): ?StockTransaction
    {
        $difference = round($counted - (float) $ingredient->stock_qty, 3);

        if (abs($difference) < 0.0005) {
            return null;
        }

        return $this->record($ingredient, 'adjustment', $difference, $note, null, null, $userId);
    }

    /**
     * Deduct every ingredient used by a paid order, based on menu recipes.
     * Safe to call once per order - guarded by orders.stock_deducted.
     */
    public function deductForOrder(Order $order, ?int $userId = null): void
    {
        if ($order->stock_deducted) {
            return;
        }

        $order->loadMissing('items.menuItem.recipeItems.ingredient');

        DB::transaction(function () use ($order, $userId) {
            foreach ($order->items as $line) {
                $menuItem = $line->menuItem;

                if (! $menuItem) {
                    continue;
                }

                foreach ($menuItem->recipeItems as $recipe) {
                    if (! $recipe->ingredient) {
                        continue;
                    }

                    $used = (float) $recipe->quantity * (int) $line->quantity;

                    if ($used <= 0) {
                        continue;
                    }

                    $this->record(
                        $recipe->ingredient,
                        'usage',
                        -$used,
                        'Order '.$order->order_number.' - '.$line->item_name.' x'.$line->quantity,
                        null,
                        $order->id,
                        $userId,
                    );
                }
            }

            $order->update(['stock_deducted' => true]);
        });
    }
}
