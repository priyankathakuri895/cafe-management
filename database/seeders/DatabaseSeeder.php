<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@cafe.test'],
            ['name' => 'Cafe Admin', 'password' => 'password', 'role' => User::ROLE_ADMIN, 'is_active' => true],
        );

        User::updateOrCreate(
            ['email' => 'cashier@cafe.test'],
            ['name' => 'Front Counter', 'password' => 'password', 'role' => User::ROLE_CASHIER, 'is_active' => true],
        );

        $menu = [
            'Hot Coffee' => [
                ['Espresso', 120], ['Americano', 150], ['Cappuccino', 200],
                ['Latte', 220], ['Mocha', 250], ['Hot Chocolate', 200],
            ],
            'Cold Drinks' => [
                ['Iced Latte', 240], ['Cold Brew', 260], ['Lemon Iced Tea', 180],
                ['Mango Smoothie', 280], ['Fresh Lime Soda', 150],
            ],
            'Tea' => [
                ['Milk Tea', 60], ['Black Tea', 50], ['Masala Tea', 80], ['Green Tea', 90],
            ],
            'Bakery' => [
                ['Croissant', 180], ['Chocolate Muffin', 160], ['Cheesecake Slice', 320], ['Banana Bread', 150],
            ],
            'Food' => [
                ['Club Sandwich', 380], ['Veg Burger', 320], ['French Fries', 200],
                ['Chicken Momo', 250], ['Pasta Alfredo', 420],
            ],
        ];

        $sort = 0;

        foreach ($menu as $categoryName => $items) {
            $category = Category::updateOrCreate(
                ['name' => $categoryName],
                ['sort_order' => $sort++, 'is_active' => true],
            );

            foreach ($items as [$name, $price]) {
                MenuItem::updateOrCreate(
                    ['name' => $name],
                    ['category_id' => $category->id, 'price' => $price, 'is_available' => true],
                );
            }
        }

        foreach ([
            ['T1', 2, 'Window'], ['T2', 2, 'Window'], ['T3', 4, 'Main hall'],
            ['T4', 4, 'Main hall'], ['T5', 6, 'Main hall'], ['T6', 4, 'Terrace'],
            ['T7', 8, 'Terrace'], ['Counter 1', 1, 'Bar'],
        ] as [$name, $capacity, $location]) {
            CafeTable::updateOrCreate(
                ['name' => $name],
                ['capacity' => $capacity, 'location' => $location, 'status' => CafeTable::STATUS_AVAILABLE],
            );
        }

        foreach ([
            ['Coffee Beans', 'kg', 12, 3, 1400],
            ['Milk', 'l', 40, 10, 110],
            ['Sugar', 'kg', 25, 5, 120],
            ['Chocolate Syrup', 'ml', 4000, 1000, 1.2],
            ['Tea Leaves', 'kg', 6, 2, 900],
            ['Flour', 'kg', 30, 8, 85],
            ['Butter', 'kg', 8, 2, 950],
            ['Chicken', 'kg', 10, 3, 480],
            ['Potato', 'kg', 20, 5, 70],
            ['Lemon', 'pcs', 60, 20, 15],
        ] as [$name, $unit, $qty, $reorder, $cost]) {
            Ingredient::updateOrCreate(
                ['name' => $name],
                ['unit' => $unit, 'stock_qty' => $qty, 'reorder_level' => $reorder, 'cost_per_unit' => $cost],
            );
        }

        // A couple of example recipes so stock deduction is visible right away.
        $recipes = [
            'Cappuccino' => [['Coffee Beans', 0.018], ['Milk', 0.15], ['Sugar', 0.01]],
            'Latte' => [['Coffee Beans', 0.018], ['Milk', 0.2], ['Sugar', 0.01]],
            'Milk Tea' => [['Tea Leaves', 0.005], ['Milk', 0.1], ['Sugar', 0.015]],
            'French Fries' => [['Potato', 0.25]],
        ];

        foreach ($recipes as $itemName => $rows) {
            $item = MenuItem::where('name', $itemName)->first();

            if (! $item) {
                continue;
            }

            foreach ($rows as [$ingredientName, $quantity]) {
                $ingredient = Ingredient::where('name', $ingredientName)->first();

                if ($ingredient) {
                    $item->recipeItems()->updateOrCreate(
                        ['ingredient_id' => $ingredient->id],
                        ['quantity' => $quantity],
                    );
                }
            }
        }
    }
}
