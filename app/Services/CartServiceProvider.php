<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the CartService as a singleton, which means the same instance is used
        // throughout the request lifecycle, ensuring cart consistency.
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share the CartService instance with all views
        view()->composer('*', function ($view) {
            // The resolve() helper retrieves the singleton instance from the container
            $view->with('cartService', resolve(CartService::class));
        });
    }
}