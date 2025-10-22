<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    /**
     * Display a listing of the shops.
     */
    public function index()
    {
        $shops = Shop::withCount(['products', 'users'])->latest()->paginate(10);
        return view('admin.shops.index', compact('shops'));
    }

    /**
     * Show the form for creating a new shop.
     */
    public function create()
    {
        return view('admin.shops.create');
    }

    /**
     * Store a newly created shop in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shops',
            'slug' => 'required|string|max:255|unique:shops',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8',
        ]);

        // Create the shop
        $shop = Shop::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        // Create admin user for this shop
        $user = User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'shop_id' => $shop->id,
            'role' => 'shop',
        ]);

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop created successfully! Admin user has been set up.');
    }

    /**
     * Display the specified shop.
     */
    public function show(Shop $shop)
    {
        $shop->load(['users', 'products']);
        return view('admin.shops.show', compact('shop'));
    }

    /**
     * Show the form for editing the specified shop.
     */
    public function edit(Shop $shop)
    {
        return view('admin.shops.edit', compact('shop'));
    }

    /**
     * Update the specified shop in storage.
     */
    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops')->ignore($shop->id)
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops')->ignore($shop->id)
            ],
        ]);

        $shop->update($request->only(['name', 'slug']));

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop updated successfully!');
    }

    /**
     * Remove the specified shop from storage.
     */
    public function destroy(Shop $shop)
    {
        // Prevent deletion if shop has products or users
        if ($shop->products()->count() > 0 || $shop->users()->count() > 0) {
            return redirect()->route('admin.shops.index')
                ->with('error', 'Cannot delete shop that has products or users. Please remove them first.');
        }

        $shop->delete();

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop deleted successfully!');
    }
}