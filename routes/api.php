<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Xendit webhook - no CSRF protection needed in API routes
Route::post('xendit/webhook', [WebhookController::class, 'xenditWebhook'])
    ->name('api.xendit.webhook');
