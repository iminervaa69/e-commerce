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
        Log::info('=== XENDIT WEBHOOK RECEIVED ===', [
            'timestamp' => now()->toDateTimeString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'raw_content' => $request->getContent(),
            'parsed_payload' => $request->all(),
        ]);

        try {
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('Invalid webhook signature', [
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all()
                ]);

                if (!app()->environment(['local', 'development'])) {
                    return response()->json(['message' => 'Invalid signature'], 401);
                }
                Log::warning('Signature check bypassed for development');
            }

            $payload = $request->all();

            $eventType = $this->determineEventType($payload);

            if (!$eventType) {
                Log::warning('No event type found in webhook payload', ['payload' => $payload]);
                return response()->json(['message' => 'No event type found'], 400);
            }

            Log::info('Processing webhook event', [
                'event_type' => $eventType,
                'handler' => $this->getHandlerName($eventType)
            ]);

            return $this->routeWebhookEvent($eventType, $payload);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    private function determineEventType($payload)
    {
        // Primary: Check for event_type (Xendit's standard)
        if (isset($payload['event_type'])) {
            return $payload['event_type'];
        }

        // Fallback: Check for event field
        if (isset($payload['event'])) {
            return $payload['event'];
        }

        // Fallback: Determine from payload structure and status
        if (isset($payload['status'])) {
            $status = strtolower($payload['status']);

            // Map status to event type
            switch ($status) {
                case 'paid':
                case 'captured':
                case 'succeeded':
                    return 'payment.paid';
                case 'failed':
                    return 'payment.failed';
                case 'expired':
                    return 'payment.expired';
                case 'pending':
                    return 'payment.pending';
                default:
                    Log::warning('Unknown status for event mapping', ['status' => $status]);
                    return null;
            }
        }

        return null;
    }

    private function routeWebhookEvent($eventType, $payload)
    {
        switch ($eventType) {
            case 'payment.paid':
            case 'payment.succeeded':
            case 'payment.captured':
                return $this->handlePaymentSuccess($payload);

            case 'payment.failed':
            case 'payment.expired':
                return $this->handlePaymentFailure($payload);

            case 'payment.pending':
                return $this->handlePaymentPending($payload);

            case 'payment.awaiting_capture':
                return $this->handlePaymentAwaitingCapture($payload);

            // E-wallet specific events
            case 'ewallet.payment.paid':
            case 'ewallet.payment.succeeded':
                return $this->handleEwalletSuccess($payload);

            case 'ewallet.payment.failed':
            case 'ewallet.payment.expired':
                return $this->handleEwalletFailure($payload);

            default:
                Log::info('Unhandled webhook event', [
                    'event_type' => $eventType,
                    'payload_keys' => array_keys($payload)
                ]);
                return response()->json(['message' => 'Event type not handled'], 200);
        }
    }

    private function verifyWebhookSignature(Request $request)
    {
        $webhookToken = config('xendivel.webhook_verification_token') ?? config('xendivel.webhook_verification_token');

        if (!$webhookToken) {
            Log::warning('Webhook token not configured');
            return app()->environment(['local', 'development']);
        }

        // Try different header formats that Xendit might use
        $receivedSignature = $request->header('X-CALLBACK-TOKEN')
                        ?? $request->header('x-callback-token')
                        ?? $request->header('Authorization');

        if (!$receivedSignature) {
            Log::warning('No callback token found in request headers', [
                'available_headers' => array_keys($request->headers->all())
            ]);
            return false;
        }

        // Remove 'Bearer ' prefix if present
        $receivedSignature = str_replace('Bearer ', '', $receivedSignature);

        $isValid = hash_equals($webhookToken, $receivedSignature);

        if (!$isValid) {
            Log::warning('Webhook token mismatch', [
                'expected_prefix' => substr($webhookToken, 0, 8) . '...',
                'received_prefix' => substr($receivedSignature, 0, 8) . '...'
            ]);
        }

        return $isValid;
    }

    private function findTransaction($payload)
    {
        Log::info('=== SEARCHING FOR TRANSACTION ===', [
            'payload_keys' => array_keys($payload),
            'search_fields' => [
                'external_id' => $payload['external_id'] ?? 'not_set',
                'id' => $payload['id'] ?? 'not_set',
                'reference_id' => $payload['reference_id'] ?? 'not_set'
            ]
        ]);

        // Method 1: Search by external_id (most reliable)
        if (isset($payload['external_id']) && !empty($payload['external_id'])) {
            $transaction = Transaction::where('reference_id', $payload['external_id'])->first();
            if ($transaction) {
                Log::info('✅ Transaction found by external_id', [
                    'transaction_id' => $transaction->id,
                    'reference_id' => $transaction->reference_id,
                    'status' => $transaction->status
                ]);
                return $transaction;
            }
        }

        // Method 2: Search by xendit_id (standardized field)
        if (isset($payload['id']) && !empty($payload['id'])) {
            $transaction = Transaction::where('xendit_id', $payload['id'])->first();
            if ($transaction) {
                Log::info('✅ Transaction found by xendit_id', [
                    'transaction_id' => $transaction->id,
                    'xendit_id' => $payload['id']
                ]);
                return $transaction;
            }
        }

        // Method 3: Search by reference_id field
        if (isset($payload['reference_id']) && !empty($payload['reference_id'])) {
            $transaction = Transaction::where('reference_id', $payload['reference_id'])->first();
            if ($transaction) {
                Log::info('✅ Transaction found by reference_id', [
                    'transaction_id' => $transaction->id
                ]);
                return $transaction;
            }
        }

        // Method 4: Search by metadata
        if (isset($payload['metadata']['transaction_id'])) {
            $transaction = Transaction::find($payload['metadata']['transaction_id']);
            if ($transaction) {
                Log::info('✅ Transaction found by metadata', [
                    'transaction_id' => $transaction->id
                ]);
                return $transaction;
            }
        }

        Log::error('❌ TRANSACTION NOT FOUND', [
            'searched_external_id' => $payload['external_id'] ?? null,
            'searched_id' => $payload['id'] ?? null,
            'searched_reference_id' => $payload['reference_id'] ?? null,
            'recent_transactions' => Transaction::latest()->take(5)->get(['id', 'reference_id', 'status'])->toArray()
        ]);

        return null;
    }

    private function handlePaymentSuccess($payload)
    {
        DB::beginTransaction();

        try {
            $transaction = $this->findTransaction($payload);

            if (!$transaction) {
                Log::warning('Transaction not found for successful payment', [
                    'payload_keys' => array_keys($payload),
                    'external_id' => $payload['external_id'] ?? null
                ]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Check if already processed
            if (in_array($transaction->status, ['completed', 'paid', 'succeeded'])) {
                Log::info('Transaction already processed', [
                    'transaction_id' => $transaction->id,
                    'current_status' => $transaction->status
                ]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Verify payment amount
            $expectedAmount = $transaction->total_amount;
            $receivedAmount = $payload['amount'] ?? $payload['paid_amount'] ?? 0;

            if (abs($receivedAmount - $expectedAmount) > 1) {
                Log::error('Payment amount mismatch', [
                    'transaction_id' => $transaction->id,
                    'expected' => $expectedAmount,
                    'received' => $receivedAmount
                ]);
                return response()->json(['message' => 'Amount mismatch'], 400);
            }

            // NEW: Get cart items from checkout session for this transaction
            $cartItems = $this->getCartItemsFromTransaction($transaction);
            
            if ($cartItems->isEmpty()) {
                Log::error('No cart items found for transaction', [
                    'transaction_id' => $transaction->id
                ]);
                return response()->json(['message' => 'Cart items not found'], 400);
            }

            // NEW: Validate and reserve inventory
            $reservationResult = $this->reserveInventory($cartItems);
            if (!$reservationResult['success']) {
                Log::error('Inventory reservation failed in webhook', [
                    'transaction_id' => $transaction->id,
                    'reason' => $reservationResult['message']
                ]);
                
                // Mark transaction as failed due to inventory issues
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => 'Inventory not available: ' . $reservationResult['message'],
                    'webhook_data' => $payload
                ]);
                
                DB::commit();
                return response()->json(['message' => 'Inventory not available'], 400);
            }

            // NEW: Create orders from cart items
            $orders = $this->createOrdersFromCart($cartItems, $transaction);

            // Update transaction
            $updateData = [
                'status' => 'completed',
                'paid_at' => now(),
                'paid_amount' => $receivedAmount,
                'webhook_data' => $payload
            ];

            if (empty($transaction->xendit_id) || ($payload['id'] ?? null) !== $transaction->xendit_id) {
                $updateData['xendit_id'] = $payload['id'] ?? null;
            }

            $transaction->update($updateData);

            // Update orders to confirmed
            foreach ($orders as $order) {
                $order->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now()
                ]);
            }

            // NEW: Commit inventory deduction
            $this->commitInventoryDeduction($transaction);

            // NEW: Clear user cart
            if ($transaction->user_id) {
                $this->clearUserCart($transaction->user_id);
            }

            // Clear checkout session
            session()->forget('checkout_items');

            // Send notifications
            try {
                $this->sendPaymentConfirmation($transaction);
            } catch (\Exception $e) {
                Log::warning('Payment confirmation email failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();

            Log::info('Payment success fully processed', [
                'transaction_id' => $transaction->id,
                'amount' => $receivedAmount,
                'orders_count' => $orders->count()
            ]);

            return response()->json(['message' => 'Success processed'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing payment success', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
                'transaction_id' => $transaction->id ?? null
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
                Log::warning('Transaction not found for failed payment', [
                    'external_id' => $payload['external_id'] ?? null
                ]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Check if already processed
            if (in_array($transaction->status, ['failed', 'expired', 'cancelled'])) {
                Log::info('Transaction already marked as failed', [
                    'transaction_id' => $transaction->id,
                    'status' => $transaction->status
                ]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            $failureReason = $payload['failure_reason']
                        ?? $payload['failure_code']
                        ?? $payload['status']
                        ?? 'Payment failed';

            // Prepare update data with debug logging
            $updateData = [
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $failureReason,
                'webhook_data' => $payload
            ];

            // Only update xendit_id if it's not already set or if payload has a different value
            if (empty($transaction->xendit_id) || ($payload['id'] ?? null) !== $transaction->xendit_id) {
                $updateData['xendit_id'] = $payload['id'] ?? null;
            }

            // DEBUG: Log what we're trying to update
            Log::info('About to update failed transaction', [
                'transaction_id' => $transaction->id,
                'update_data' => $updateData,
                'current_values' => [
                    'status' => $transaction->status,
                    'failed_at' => $transaction->failed_at,
                    'failure_reason' => $transaction->failure_reason,
                    'webhook_data' => $transaction->webhook_data ? 'exists' : 'null',
                ]
            ]);

            // Update transaction
            $updateResult = $transaction->update($updateData);

            // DEBUG: Log update result
            Log::info('Failed transaction update result', [
                'transaction_id' => $transaction->id,
                'update_successful' => $updateResult,
                'after_update_values' => [
                    'status' => $transaction->fresh()->status,
                    'failed_at' => $transaction->fresh()->failed_at,
                    'failure_reason' => $transaction->fresh()->failure_reason,
                    'webhook_data' => $transaction->fresh()->webhook_data ? 'exists' : 'null',
                ]
            ]);

            // Update orders
            $orders = $transaction->orders;
            foreach ($orders as $order) {
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $failureReason
                ]);
            }

            $this->releaseReservedInventory($transaction);

            DB::commit();

            Log::info('Payment failure processed', [
                'transaction_id' => $transaction->id,
                'reason' => $failureReason
            ]);

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

    private function getHandlerName($eventType)
    {
        $handlers = [
            'payment.paid' => 'handlePaymentSuccess',
            'payment.succeeded' => 'handlePaymentSuccess',
            'payment.failed' => 'handlePaymentFailure',
            'payment.expired' => 'handlePaymentFailure',
            'payment.pending' => 'handlePaymentPending',
            'payment.awaiting_capture' => 'handlePaymentAwaitingCapture',
            'ewallet.payment.paid' => 'handleEwalletSuccess',
            'ewallet.payment.failed' => 'handleEwalletFailure',
            'ewallet.payment.expired' => 'handleEwalletFailure'
        ];

        return $handlers[$eventType] ?? 'unhandled';
    }

    private function handlePaymentPending($payload)
    {
        try {
            $transaction = $this->findTransaction($payload);

            if (!$transaction) {
                Log::warning('Transaction not found for pending payment', ['payload' => $payload]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $transaction->update([
                'status' => 'pending',
                'xendit_id' => $payload['id'] ?? null, // Standardized field
                'xendit_response' => $payload
            ]);

            Log::info('Payment pending processed', ['transaction_id' => $transaction->id]);
            return response()->json(['message' => 'Pending processed'], 200);

        } catch (\Exception $e) {
            Log::error('Error processing payment pending', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw $e;
        }
    }

    private function handlePaymentAwaitingCapture($payload)
    {
        try {
            $transaction = $this->findTransaction($payload);

            if (!$transaction) {
                Log::warning('Transaction not found for awaiting capture payment', ['payload' => $payload]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $transaction->update([
                'status' => 'awaiting_capture',
                'xendit_id' => $payload['id'] ?? null, // Standardized field
                'xendit_response' => $payload
            ]);

            Log::info('Payment awaiting capture processed', ['transaction_id' => $transaction->id]);
            return response()->json(['message' => 'Awaiting capture processed'], 200);

        } catch (\Exception $e) {
            Log::error('Error processing payment awaiting capture', [
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

    private function sendPaymentConfirmation($transaction)
    {
        try {
            // Send email notification - Replace with your actual implementation
            if ($transaction->user && $transaction->customer_email) {
                // Example using Laravel Mail
                Mail::to($transaction->customer_email)->send(new PaymentConfirmationMail($transaction));

                // Or using Laravel notifications:
                // $transaction->user->notify(new PaymentConfirmedNotification($transaction));
            }

            Log::info('Payment confirmation sent', [
                'transaction_id' => $transaction->id,
                'email' => $transaction->customer_email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendPaymentFailureNotification($transaction)
    {
        try {
            // Send failure notification
            if ($transaction->user && $transaction->customer_email) {
                // Example using Laravel Mail
                Mail::to($transaction->customer_email)->send(new PaymentFailureMail($transaction));
            }

            Log::info('Payment failure notification sent', [
                'transaction_id' => $transaction->id,
                'email' => $transaction->customer_email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment failure notification', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function commitInventoryDeduction($transaction)
    {
        try {
            // Get orders from transaction with proper relationship loading
            $orders = $transaction->orders()->with(['orderItems.productVariant'])->get();

            if ($orders->isEmpty()) {
                Log::warning('No orders found for inventory deduction', [
                    'transaction_id' => $transaction->id
                ]);
                return;
            }

            foreach ($orders as $order) {
                $orderItems = $order->orderItems;

                if ($orderItems->isEmpty()) {
                    Log::warning('No order items found', ['order_id' => $order->id]);
                    continue;
                }

                foreach ($orderItems as $orderItem) {
                    $variant = $orderItem->productVariant;

                    if (!$variant) {
                        Log::error('Product variant not found for order item', [
                            'order_item_id' => $orderItem->id,
                            'product_variant_id' => $orderItem->product_variant_id
                        ]);
                        continue;
                    }

                    // Get current stock to verify
                    $currentStock = $variant->stock;
                    $requiredQuantity = $orderItem->quantity;

                    if ($currentStock >= $requiredQuantity) {
                        // Perform the stock deduction
                        $variant->decrement('stock', $requiredQuantity);

                        // Refresh the model to get updated stock
                        $variant->refresh();

                        Log::info('Stock deducted successfully', [
                            'variant_id' => $variant->id,
                            'product_name' => $variant->product->name ?? 'Unknown',
                            'quantity_deducted' => $requiredQuantity,
                            'stock_before' => $currentStock,
                            'stock_after' => $variant->stock
                        ]);

                        // Update sold count if column exists
                        if ($variant->hasAttribute('sold_count')) {
                            $variant->increment('sold_count', $requiredQuantity);
                            Log::info('Sold count updated', [
                                'variant_id' => $variant->id,
                                'sold_count_increase' => $requiredQuantity
                            ]);
                        }
                    } else {
                        Log::error('Insufficient stock for deduction', [
                            'variant_id' => $variant->id,
                            'product_name' => $variant->product->name ?? 'Unknown',
                            'required_quantity' => $requiredQuantity,
                            'available_stock' => $currentStock,
                            'order_id' => $order->id,
                            'transaction_id' => $transaction->id
                        ]);

                        // Don't fail the entire process, but flag the issue
                        // You might want to send an alert to administrators
                    }
                }
            }

            Log::info('Inventory deduction completed', [
                'transaction_id' => $transaction->id,
                'orders_processed' => $orders->count(),
                'total_items_processed' => $orders->sum(function($order) {
                    return $order->orderItems->count();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to commit inventory deduction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Don't throw exception to avoid breaking the webhook
            // But you might want to send an alert
        }
    }

// FIXED: releaseReservedInventory method
    private function releaseReservedInventory($transaction)
    {
        try {
            $orders = $transaction->orders()->with(['orderItems.productVariant'])->get();

            if ($orders->isEmpty()) {
                Log::warning('No orders found for inventory release', [
                    'transaction_id' => $transaction->id
                ]);
                return;
            }

            foreach ($orders as $order) {
                $orderItems = $order->orderItems;

                foreach ($orderItems as $orderItem) {
                    $variant = $orderItem->productVariant;

                    if (!$variant) {
                        Log::warning('Product variant not found for inventory release', [
                            'order_item_id' => $orderItem->id
                        ]);
                        continue;
                    }

                    // If you have a reserved_stock column, release it here:
                    // $variant->decrement('reserved_stock', $orderItem->quantity);

                    Log::info('Reserved inventory released', [
                        'variant_id' => $variant->id,
                        'product_name' => $variant->product->name ?? 'Unknown',
                        'quantity_released' => $orderItem->quantity,
                        'current_stock' => $variant->stock
                    ]);
                }
            }

            Log::info('Inventory release completed', [
                'transaction_id' => $transaction->id,
                'orders_processed' => $orders->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to release reserved inventory', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getCartItemsFromTransaction($transaction)
    {
        try {
            // Option 1: If you store cart_item_ids in transaction metadata
            if ($transaction->metadata && isset($transaction->metadata['cart_item_ids'])) {
                $cartItemIds = $transaction->metadata['cart_item_ids'];
                return CartItem::whereIn('id', $cartItemIds)->with(['productVariant.product.store'])->get();
            }

            // Option 2: Get from user's current cart (less reliable)
            if ($transaction->user_id) {
                return CartItem::where('user_id', $transaction->user_id)
                    ->with(['productVariant.product.store'])
                    ->get();
            }

            // Option 3: Reconstruct from existing orders if any exist
            $existingOrders = $transaction->orders()->with(['orderItems.productVariant'])->get();
            if ($existingOrders->isNotEmpty()) {
                // Convert order items back to cart items structure for consistency
                $cartItems = collect();
                foreach ($existingOrders as $order) {
                    foreach ($order->orderItems as $orderItem) {
                        // Create a pseudo cart item for processing
                        $cartItems->push((object)[
                            'id' => 'order_item_' . $orderItem->id,
                            'product_variant_id' => $orderItem->product_variant_id,
                            'quantity' => $orderItem->quantity,
                            'price_when_added' => $orderItem->unit_price,
                            'productVariant' => $orderItem->productVariant
                        ]);
                    }
                }
                return $cartItems;
            }

            Log::warning('Unable to retrieve cart items for transaction', [
                'transaction_id' => $transaction->id
            ]);
            
            return collect();

        } catch (\Exception $e) {
            Log::error('Error retrieving cart items from transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    private function reserveInventory($cartItems)
    {
        try {
            foreach ($cartItems as $item) {
                $variant = $item->productVariant;

                if ($variant->stock < $item->quantity) {
                    return ['success' => false, 'message' => "Insufficient stock for {$variant->product->name}"];
                }

                $variant->refresh();
                if ($variant->stock < $item->quantity) {
                    return ['success' => false, 'message' => "Stock changed during checkout for {$variant->product->name}"];
                }
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to reserve inventory'];
        }
    }

    private function createOrdersFromCart($cartItems, $transaction)
    {
        $itemsByStore = $cartItems->groupBy(function($item) {
            return $item->productVariant->product->store_id ?? 0;
        });

        $orders = collect();

        foreach ($itemsByStore as $storeId => $storeItems) {
            $storeSubtotal = $storeItems->sum(function($item) {
                return $item->quantity * $item->price_when_added;
            });

            $storeShipping = 5000;
            $storeTax = $storeSubtotal * 0.11;
            $storeTotal = $storeSubtotal + $storeShipping + $storeTax;

            $order = Order::create([
                'transaction_id' => $transaction->id,
                'store_id' => $storeId,
                'order_number' => 'ORD_' . time() . '_' . Str::random(6),
                'subtotal' => $storeSubtotal,
                'shipping_cost' => $storeShipping,
                'tax_amount' => $storeTax,
                'total_amount' => $storeTotal,
                'status' => 'pending'
            ]);

            foreach ($storeItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price_when_added,
                    'total_price' => $cartItem->quantity * $cartItem->price_when_added
                ]);
            }

            $orders->push($order);
        }

        Log::info('Orders created for transaction', [
            'transaction_id' => $transaction->id,
            'order_count' => $orders->count(),
            'order_ids' => $orders->pluck('id')->toArray()
        ]);

        return $orders;
    }

    private function clearCart($user = null, $sessionId = null)
    {
        $query = CartItem::query();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        $query->delete();
    }

// FIXED: clearUserCart with better error handling
    private function clearUserCart($userId)
    {
        if (!$userId) {
            Log::warning('No user ID provided for cart clearing');
            return;
        }

        try {
            $deletedCount = CartItem::where('user_id', $userId)->delete();

            Log::info('User cart cleared successfully', [
                'user_id' => $userId,
                'items_deleted' => $deletedCount
            ]);

            // Clear any checkout session data as well
            if (session()->has('checkout_items')) {
                session()->forget('checkout_items');
                Log::info('Checkout session cleared', ['user_id' => $userId]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to clear user cart', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
