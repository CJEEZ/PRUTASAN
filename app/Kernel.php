<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... existing groups ...

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // ... other middleware ...
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Add session persistence for guest cart logic
            \App\Http\Middleware\EncryptCookies::class, // Already typically here
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Already typically here
        ],

        'api' => [
            // ... other middleware ...
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string>
     */
    protected $routeMiddleware = [
        // ... existing middleware ...
        'admin' => \App\Http\Middleware\AdminMiddleware::class, // Add this
    ];
}