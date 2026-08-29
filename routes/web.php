<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CafeTableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockTransactionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Point of sale
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [PosController::class, 'store'])->name('pos.store');

    // Orders and bills
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Tables and reservations
    Route::resource('tables', CafeTableController::class)->except('show');
    Route::resource('reservations', ReservationController::class)->except('show');

    // Menu
    Route::resource('menu', MenuItemController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');

    // Inventory
    Route::resource('ingredients', IngredientController::class)->except('show');
    Route::get('stock', [StockTransactionController::class, 'index'])->name('stock.index');
    Route::get('stock/create', [StockTransactionController::class, 'create'])->name('stock.create');
    Route::post('stock', [StockTransactionController::class, 'store'])->name('stock.store');
    Route::get('menu/{menu}/recipe', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('menu/{menu}/recipe', [RecipeController::class, 'update'])->name('recipes.update');

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::resource('staff', StaffController::class)->except('show');
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    });
});
