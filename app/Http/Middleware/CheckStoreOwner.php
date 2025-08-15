<?php
// app/Http/Middleware/CheckStoreOwner.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $storeId = $request->route('store') ?? $request->input('store_id');

        if (!$storeId) {
            abort(404, 'Store not found');
        }

        $user = auth()->user();

        // Check if user is owner/admin of this specific store
        $hasAccess = $user->stores()
            ->where('stores.id', $storeId)
            ->whereIn('store_user_roles.role', ['owner', 'admin'])
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Access denied. You must be an owner or admin of this store.');
        }

        return $next($request);
    }
}
