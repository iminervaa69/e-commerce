<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function index()
    {
        // For now, we'll use sample cart data
        // Later you can integrate with your actual cart system
        $cartItems = $this->getSampleCartData();
        $subtotal = $this->calculateSubtotal($cartItems);
        $shipping = 9.99;
        $tax = $subtotal * 0.08;
        $total = $subtotal + $shipping + $tax;

        return view('frontend.pages.checkout.index', compact(
            'cartItems',
            'subtotal',
            'shipping',
            'tax',
            'total'
        ));
    }

    public function success(Request $request)
    {
        // Get payment details from session or request
        $orderId = $request->get('order_id', Session::get('last_order_id', 'ORD' . time()));
        $amount = $request->get('amount', Session::get('last_amount', '0.00'));
        
        // Clear any cart data
        Session::forget(['cart', 'last_order_id', 'last_amount']);
        
        return view('frontend.pages.checkout.success', compact('orderId', 'amount'));
    }

    public function failed(Request $request)
    {
        $errorMessage = $request->get('error', 'Payment could not be processed. Please try again.');
        
        return view('frontend.pages.checkout.failed', compact('errorMessage'));
    }

    // Helper methods for sample data (remove when integrating with real cart)
    private function getSampleCartData()
    {
        return collect([
            (object) [
                'id' => 1,
                'name' => 'Sample Product',
                'price' => 99.00,
                'quantity' => 1,
                'image' => '/images/sample-product.jpg'
            ]
        ]);
    }

    private function calculateSubtotal($cartItems)
    {
        return $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}