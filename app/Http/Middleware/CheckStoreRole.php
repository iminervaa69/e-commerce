<?php
// app/Http/Middleware/CheckStoreRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // If no specific role required, just check if user has any store
        if (!$role) {
            if ($user->stores()->count() === 0) {
                abort(403, 'Access denied. You must be associated with a store.');
            }
            return $next($request);
        }

        // Check if user has the specific role in any store
        if (!$user->hasStoreRole($role)) {
            abort(403, "Access denied. Required role: {$role}");
        }

        return $next($request);
    }
}
