<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Get the path the user should be redirected to when they are authenticated.
     */
    public function handle(Request $request, $next, ...$guards)
    {
        $this->redirectTo($request);
        return $next($request);
    }

    protected function redirectTo(Request $request): ?string
    {
        return route('catalog.index');
    }
}
