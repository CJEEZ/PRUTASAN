<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LogoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Create a fallback logo if it doesn't exist
        $logoPath = public_path('logo.png');
        
        if (!file_exists($logoPath)) {
            // Create a simple placeholder logo using GD library if available
            // For now, we'll just use an external CDN as fallback
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
