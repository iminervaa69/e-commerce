<?php

namespace App\Http\Controllers\User;

use App\Models\BillingInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class BillingInformationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $billingInfo = $user->billingInformation()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('frontend.pages.billing.index', compact('billingInfo'));
    }

    /**
     * Get billing information for AJAX requests (used in checkout components) billing_address_id
     */
    public function getBillingInfo(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'billing_info' => []
            ]);
        }

        $billingInfo = $user->billingInformation()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($billing) {
                return [
                    'id' => $billing->id,
                    'first_name' => $billing->first_name,
                    'last_name' => $billing->last_name,
                    'full_name' => $billing->full_name,
                    'email' => $billing->email,
                    'phone' => $billing->phone,
                    'is_default' => $billing->is_default
                ];
            });

        return response()->json([
            'success' => true,
            'billing_info' => $billingInfo
        ]);
    }

    /**
     * Store new billing information
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/'],
            'is_default' => 'boolean'
        ], [
            'first_name.regex' => 'First name can only contain letters and spaces',
            'last_name.regex' => 'Last name can only contain letters and spaces',
            'phone.regex' => 'Phone number must be a valid Indonesian number',
        ]);

        try {
            DB::beginTransaction();

            // Normalize phone number
            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                $user->billingInformation()->update(['is_default' => false]);
            }

            // If this is the user's first billing info, make it default
            if ($user->billingInformation()->count() === 0) {
                $validated['is_default'] = true;
            }

            $billingInfo = $user->billingInformation()->create($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Billing information saved successfully',
                    'billing_info' => [
                        'id' => $billingInfo->id,
                        'first_name' => $billingInfo->first_name,
                        'last_name' => $billingInfo->last_name,
                        'full_name' => $billingInfo->full_name,
                        'email' => $billingInfo->email,
                        'phone' => $billingInfo->phone,
                        'is_default' => $billingInfo->is_default
                    ]
                ]);
            }

            return redirect()->route('billing.index')->with('success', 'Billing information saved successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Billing information creation error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save billing information'
                ], 500);
            }

            return back()->with('error', 'Failed to save billing information')->withInput();
        }
    }

    /**
     * Show single billing information for editing
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $billingInfo = $user->billingInformation()->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'billing_info' => [
                    'id' => $billingInfo->id,
                    'first_name' => $billingInfo->first_name,
                    'last_name' => $billingInfo->last_name,
                    'full_name' => $billingInfo->full_name,
                    'email' => $billingInfo->email,
                    'phone' => $billingInfo->phone,
                    'is_default' => $billingInfo->is_default
                ]
            ]);
        }

        return view('billing.show', compact('billingInfo'));
    }


    /**
     * Update existing billing information
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $billingInfo = $user->billingInformation()->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/'],
            'is_default' => 'boolean'
        ], [
            'first_name.regex' => 'First name can only contain letters and spaces',
            'last_name.regex' => 'Last name can only contain letters and spaces',
            'phone.regex' => 'Phone number must be a valid Indonesian number',
        ]);

        try {
            DB::beginTransaction();

            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                $user->billingInformation()->where('id', '!=', $billingInfo->id)->update(['is_default' => false]);
            }

            $billingInfo->update($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Billing information updated successfully',
                    'billing_info' => [
                        'id' => $billingInfo->id,
                        'first_name' => $billingInfo->first_name,
                        'last_name' => $billingInfo->last_name,
                        'full_name' => $billingInfo->full_name,
                        'email' => $billingInfo->email,
                        'phone' => $billingInfo->phone,
                        'is_default' => $billingInfo->is_default
                    ]
                ]);
            }

            return redirect()->route('billing.index')->with('success', 'Billing information updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Billing information update error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update billing information'
                ], 500);
            }

            return back()->with('error', 'Failed to update billing information')->withInput();
        }
    }

    /**
     * Delete billing information
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $billingInfo = $user->billingInformation()->findOrFail($id);

        // Prevent deletion if it's the only billing information
        if ($user->billingInformation()->count() === 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your only billing information'
                ], 400);
            }

            return back()->with('error', 'Cannot delete your only billing information');
        }

        try {
            DB::beginTransaction();

            $wasDefault = $billingInfo->is_default;
            $billingInfo->delete();

            // If deleted billing info was default, make another one default
            if ($wasDefault) {
                $user->billingInformation()->first()?->update(['is_default' => true]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Billing information deleted successfully'
                ]);
            }

            return redirect()->route('billing.index')->with('success', 'Billing information deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Billing information deletion error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete billing information'
                ], 500);
            }

            return back()->with('error', 'Failed to delete billing information');
        }
    }

    /**
     * Set billing information as default
     */
    public function setDefault(Request $request, $id)
    {
        $user = Auth::user();
        $billingInfo = $user->billingInformation()->findOrFail($id);

        try {
            DB::beginTransaction();

            // Unset all other defaults
            $user->billingInformation()->update(['is_default' => false]);
            
            // Set this as default
            $billingInfo->update(['is_default' => true]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Default billing information updated'
                ]);
            }

            return redirect()->route('billing.index')->with('success', 'Default billing information updated');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Set default billing information error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update default billing information'
                ], 500);
            }

            return back()->with('error', 'Failed to update default billing information');
        }
    }

    /**
     * Normalize Indonesian phone number
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove any whitespace
        $phone = preg_replace('/\s+/', '', $phone);
        
        // Convert to +62 format
        if (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) === '62') {
            $phone = '+' . $phone;
        } elseif (substr($phone, 0, 3) !== '+62') {
            $phone = '+62' . $phone;
        }
        
        return $phone;
    }
}