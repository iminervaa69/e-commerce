<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();
        return view('admin.pages.stores.index', compact('stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:10',
            'day_of_week' => 'required|integer',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
        ]);
        Store::create($request->all());
        return redirect()->route('stores.index')->with('success','Store created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:10',
            'day_of_week' => 'required|integer',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
        ]);
        $store = Store::find($id);
        $store->update($request->all());
        return redirect()->route('stores.index')->with('success', 'Store updated successfully.');
    }

    public function destroy(string $id)
    {
        $store = Store::find($id);
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
    }

    public function create()
    {
        return view('admin.pages.stores.create');
    }
    public function edit(string $id)
    {
        $store = Store::find($id);
        $products = Product::where('store_id', $id)->get();
        return view('admin.pages.stores.edit', compact('store', 'products'));
    }
}
