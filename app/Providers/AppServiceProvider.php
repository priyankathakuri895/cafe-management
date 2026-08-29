<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // @money($value) -> "Rs. 1,234.00" using the currency in config/cafe.php
        Blade::directive('money', function (string $expression) {
            return "<?php echo config('cafe.currency').' '.number_format((float) ({$expression}), 2); ?>";
        });
    }
}
