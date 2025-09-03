<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;

class AddressController extends Controller
{
    /**
     * Display user's addresses (for dedicated address management page)
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('frontend.pages.address.index', compact('addresses'));
    }

    /**
     * Get addresses for AJAX requests (used in checkout components) shipping_address_id
     */
    public function getAddresses(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'addresses' => []
            ]);
        }

        $addresses = $user->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'label' => $address->label,
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'full_address' => $address->getFullAddressAttribute(),
                    'is_default' => $address->is_default,
                    'formatted' => $address->getFormattedAddressAttribute()
                ];
            });

        return response()->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }

    /**
     * Store a new address
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
            'label' => 'required|string|max:50|in:Home,Office,Other',
            'recipient_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/'],
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100', 
            'district' => 'required|string|max:100',
            'postal_code' => 'required|string|regex:/^[0-9]{5}$/',
            'street_address' => 'required|string|min:10|max:255',
            'address_notes' => 'nullable|string|max:255',
            'is_default' => 'boolean'
        ], [
            'recipient_name.regex' => 'Recipient name can only contain letters and spaces',
            'phone.regex' => 'Phone number must be a valid Indonesian number',
            'postal_code.regex' => 'Postal code must be 5 digits',
            'street_address.min' => 'Street address must be at least 10 characters'
        ]);

        try {
            DB::beginTransaction();

            // Normalize phone number
            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                $user->addresses()->update(['is_default' => false]);
            }

            // If this is the user's first address, make it default
            if ($user->addresses()->count() === 0) {
                $validated['is_default'] = true;
            }

            $address = $user->addresses()->create($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address saved successfully',
                    'address' => [
                        'id' => $address->id,
                        'label' => $address->label,
                        'recipient_name' => $address->recipient_name,
                        'phone' => $address->phone,
                        'full_address' => $address->getFullAddressAttribute(),
                        'is_default' => $address->is_default,
                        'formatted' => $address->getFormattedAddressAttribute()
                    ]
                ]);
            }

            return redirect()->route('addresses.index')->with('success', 'Address saved successfully');

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
            Log::error('Address creation error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save address'
                ], 500);
            }

            return back()->with('error', 'Failed to save address')->withInput();
        }
    }

    /**
     * Show single address for editing
     */
    public function show($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        return response()->json([
            'success' => true,
            'address' => $address
        ]);
    }

    /**
     * Update an existing address
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:50|in:Home,Office,Other',
            'recipient_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/'],
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100', 
            'postal_code' => 'required|string|regex:/^[0-9]{5}$/',
            'street_address' => 'required|string|min:10|max:255',
            'address_notes' => 'nullable|string|max:255',
            'is_default' => 'boolean'
        ], [
            'recipient_name.regex' => 'Recipient name can only contain letters and spaces',
            'phone.regex' => 'Phone number must be a valid Indonesian number',
            'postal_code.regex' => 'Postal code must be 5 digits',
            'street_address.min' => 'Street address must be at least 10 characters'
        ]);

        try {
            DB::beginTransaction();

            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address updated successfully',
                    'address' => [
                        'id' => $address->id,
                        'label' => $address->label,
                        'recipient_name' => $address->recipient_name,
                        'phone' => $address->phone,
                        'full_address' => $address->getFullAddressAttribute(),
                        'is_default' => $address->is_default,
                        'formatted' => $address->getFormattedAddressAttribute()
                    ]
                ]);
            }

            return redirect()->route('addresses.index')->with('success', 'Address updated successfully');

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
            Log::error('Address update error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update address'
                ], 500);
            }

            return back()->with('error', 'Failed to update address')->withInput();
        }
    }

    /**
     * Delete an address
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        // Prevent deletion if it's the only address
        if ($user->addresses()->count() === 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your only address'
                ], 400);
            }

            return back()->with('error', 'Cannot delete your only address');
        }

        try {
            DB::beginTransaction();

            $wasDefault = $address->is_default;
            $address->delete();

            // If deleted address was default, make another one default
            if ($wasDefault) {
                $user->addresses()->first()?->update(['is_default' => true]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address deleted successfully'
                ]);
            }

            return redirect()->route('addresses.index')->with('success', 'Address deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Address deletion error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete address'
                ], 500);
            }

            return back()->with('error', 'Failed to delete address');
        }
    }

    /**
     * Set an address as default
     */
    public function setDefault(Request $request, $id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        try {
            DB::beginTransaction();

            // Unset all other defaults
            $user->addresses()->update(['is_default' => false]);
            
            // Set this as default
            $address->update(['is_default' => true]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Default address updated'
                ]);
            }

            return redirect()->route('addresses.index')->with('success', 'Default address updated');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Set default address error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update default address'
                ], 500);
            }

            return back()->with('error', 'Failed to update default address');
        }
    }

    /**
     * Get provinces for address form (if you have location data)
     */
    public function getProvinces()
    {
        // This would typically come from a provinces table or external API
        $provinces = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi',
            'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung',
            'Kepulauan Riau', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah',
            'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali', 'Nusa Tenggara Barat',
            'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
            'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan',
            'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat', 'Maluku',
            'Maluku Utara', 'Papua', 'Papua Barat'
        ];

        return response()->json([
            'success' => true,
            'provinces' => $provinces
        ]);
    }

    /**
     * Normalize phone number to Indonesian format
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);
        
        // Convert to +62 format
        if (strpos($phone, '62') === 0) {
            return '+' . $phone;
        } elseif (strpos($phone, '0') === 0) {
            return '+62' . substr($phone, 1);
        } else {
            return '+62' . $phone;
        }
    }
}