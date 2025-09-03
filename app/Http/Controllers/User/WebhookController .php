<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\CartItem;

class WebhookController extends Controller
{
    public function xenditWebhook(Request $request)
    {
        try {
            // Log the webhook payload for debugging
            Log::info('Xendit webhook received', ['payload' => $request->all()]);

            // Verify webhook signature
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('Invalid webhook signature', ['headers' => $request->headers->all()]);
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            $payload = $request->all();
            $eventType = $payload['event'] ?? null;

            // Handle different webhook events
            switch ($eventType) {
                case 'payment.paid':
                case 'payment.succeeded':
                    return $this->handlePaymentSuccess($payload);

                case 'payment.failed':
                case 'payment.expired':
                    return $this->handlePaymentFailure($payload);

                case 'ewallet.payment.paid':
                    return $this->handleEwalletSuccess($payload);

                case 'ewallet.payment.failed':
                case 'ewallet.payment.expired':
                    return $this->handleEwalletFailure($payload);

                default:
                    Log::info('Unhandled webhook event', ['event' => $eventType]);
                    return response()->json(['message' => 'Event type not handled'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    private function verifyWebhookSignature(Request $request)
    {
        $webhookToken = config('xendivel.webhook_verification_token');

        if (!$webhookToken) {
            Log::warning('Webhook token not configured');
            return false;
        }

        // Get the signature from headers
        $receivedSignature = $request->header('x-callback-token');

        // Simple token verification (you might want to use HMAC for better security)
        return hash_equals($webhookToken, $receivedSignature);
    }

    private function handlePaymentSuccess($payload)
    {
        DB::beginTransaction();

        try {
            // Find transaction by Xendit ID or reference ID
            $transaction = $this->findTransaction($payload);

            if (!$transaction) {
                Log::warning('Transaction not found for successful payment', ['payload' => $payload]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Prevent duplicate processing
            if ($transaction->status === 'completed') {
                Log::info('Transaction already completed', ['transaction_id' => $transaction->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Update transaction status
            $transaction->update([
                'status' => 'completed',
                'paid_at' => now(),
                'xendit_response' => $payload
            ]);

            // Update associated orders
            $orders = $transaction->orders;
            foreach ($orders as $order) {
                $order->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now()
                ]);
            }

            // Commit inventory deduction (if using reserved stock system)
            $this->commitInventoryDeduction($transaction);

            // Clear user's cart (for e-wallet payments that didn't clear cart immediately)
            $this->clearUserCart($transaction->user_id);

            // Send confirmation email/notification
            $this->sendPaymentConfirmation($transaction);

            DB::commit();
            Log::info('Payment success processed', ['transaction_id' => $transaction->id]);

            return response()->json(['message' => 'Success processed'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing payment success', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw $e;
        }
    }

    private function handlePaymentFailure($payload)
    {
        DB::beginTransaction();

        try {
            $transaction = $this->findTransaction($payload);

            if (!$transaction) {
                Log::warning('Transaction not found for failed payment', ['payload' => $payload]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Prevent duplicate processing
            if (in_array($transaction->status, ['failed', 'expired', 'cancelled'])) {
                Log::info('Transaction already marked as failed/expired', ['transaction_id' => $transaction->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Update transaction status
            $failureReason = $payload['failure_reason'] ?? $payload['status'] ?? 'Payment failed';

            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $failureReason,
                'xendit_response' => $payload
            ]);

            // Update associated orders
            $orders = $transaction->orders;
            foreach ($orders as $order) {
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $failureReason
                ]);
            }

            // Release reserved inventory
            $this->releaseReservedInventory($transaction);

            // Send failure notification
            $this->sendPaymentFailureNotification($transaction);

            DB::commit();
            Log::info('Payment failure processed', ['transaction_id' => $transaction->id]);

            return response()->json(['message' => 'Failure processed'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing payment failure', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw $e;
        }
    }

    private function handleEwalletSuccess($payload)
    {
        // E-wallet payments use similar logic but different payload structure
        return $this->handlePaymentSuccess($payload);
    }

    private function handleEwalletFailure($payload)
    {
        // E-wallet failures use similar logic but different payload structure
        return $this->handlePaymentFailure($payload);
    }

    private function findTransaction($payload)
    {
        // Try to find by Xendit ID first
        if (isset($payload['id'])) {
            $transaction = Transaction::where('xendit_id', $payload['id'])->first();
            if ($transaction) {
                return $transaction;
            }
        }

        // Try to find by reference ID
        if (isset($payload['reference_id'])) {
            return Transaction::where('reference_id', $payload['reference_id'])->first();
        }

        // For e-wallet, try external_id
        if (isset($payload['external_id'])) {
            return Transaction::where('reference_id', $payload['external_id'])->first();
        }

        return null;
    }

    private function commitInventoryDeduction($transaction)
    {
        // Get cart items from orders
        $orders = $transaction->orders;

        foreach ($orders as $order) {
            foreach ($order->orderItems as $orderItem) {
                $variant = $orderItem->productVariant;

                if ($variant) {
                    // If using reserved_stock system
                    if ($variant->hasAttribute('reserved_stock')) {
                        $variant->decrement('reserved_stock', $orderItem->quantity);
                    }

                    // Ensure stock doesn't go negative
                    if ($variant->stock > 0) {
                        $variant->decrement('stock', min($orderItem->quantity, $variant->stock));
                    }
                }
            }
        }
    }

    private function releaseReservedInventory($transaction)
    {
        $orders = $transaction->orders;

        foreach ($orders as $order) {
            foreach ($order->orderItems as $orderItem) {
                $variant = $orderItem->productVariant;

                if ($variant && $variant->hasAttribute('reserved_stock')) {
                    // Release reserved stock back to available stock
                    $variant->increment('stock', $orderItem->quantity);
                    $variant->decrement('reserved_stock', $orderItem->quantity);
                }
            }
        }
    }

    private function clearUserCart($userId)
    {
        if ($userId) {
            CartItem::where('user_id', $userId)->delete();
        }
    }

    private function sendPaymentConfirmation($transaction)
    {
        // Implement email/SMS notification logic here
        // You might use Laravel's notification system

        try {
            // Example:
            // $transaction->user->notify(new PaymentConfirmedNotification($transaction));

            Log::info('Payment confirmation sent', ['transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendPaymentFailureNotification($transaction)
    {
        // Implement failure notification logic
        try {
            // $transaction->user->notify(new PaymentFailedNotification($transaction));

            Log::info('Payment failure notification sent', ['transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment failure notification', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
