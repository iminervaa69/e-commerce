<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class MergeGuestCartOnLogin
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            // Merge guest cart with user's cart
            $this->cartService->mergeGuestCartOnLogin($event->user);
            
            Log::info('Guest cart merged successfully on login', [
                'user_id' => $event->user->id,
                'user_email' => $event->user->email,
            ]);

        } catch (\Exception $e) {
            // Don't break login flow, just log the error
            Log::error('Failed to merge guest cart on login', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}