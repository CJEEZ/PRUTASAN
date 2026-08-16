<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the CartService as a singleton so the same instance is used throughout the request lifecycle.
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with(request()->header('x-forwarded-proto', request()->getScheme()), 'https')) {
            URL::forceScheme('https');
        }

        // Share the CartService instance with all views, making it accessible as $cartService.
        // This is necessary for the cart icon badge ({{ $cartService->getCount() }}) in the navigation bar.
        view()->share('cartService', $this->app->make(CartService::class));
    }
}