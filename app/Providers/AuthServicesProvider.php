<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\PaymentMethod;
use App\Policies\PaymentMethodPolicy;

class AuthServicesProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PaymentMethod::class => PaymentMethodPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define the 'access-admin' Gate
        Gate::define('access-admin', function ($user) {
            return $user->isAdmin();
        });
    }
}
