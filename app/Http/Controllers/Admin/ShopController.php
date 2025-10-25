<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\OnboardingApplication;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShopWelcomeEmail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Display a listing of the shops.
     */
    public function index()
    {
        $shops = Shop::withCount(['products', 'users'])->latest()->paginate(10);
        
        // Get onboarding statistics
        $onboardingStats = [
            'pending' => OnboardingApplication::where('status', 'paid')->count(),
            'total' => OnboardingApplication::count(),
            'approved' => OnboardingApplication::where('status', 'approved')->count(),
        ];

        return view('admin.shops.index', compact('shops', 'onboardingStats'));
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

        // Generate random PIN
    $randomPin = \App\Models\Shop::generateRandomPin();

        // Create the shop
        $shop = Shop::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'pos_pin' => $randomPin, 
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
        ->with('success', "Shop created successfully! Admin user has been set up. POS PIN: $randomPin");
    }

    /**
     * Display the specified shop.
     */
    public function show(Shop $shop)
    {
        $shop->load(['users', 'products', 'subscriptions']);
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
            'pos_pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        $shop->update($request->only(['name', 'slug', 'pos_pin']));

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

    /**
     * Show onboarding applications
     */
    public function onboardingApplications(Request $request)
    {
        $status = $request->query('status', 'paid');
        
        $applications = OnboardingApplication::when($status !== 'all', function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'all' => OnboardingApplication::count(),
            'paid' => OnboardingApplication::where('status', 'paid')->count(),
            'approved' => OnboardingApplication::where('status', 'approved')->count(),
            'pending' => OnboardingApplication::where('status', 'pending')->count(),
            'rejected' => OnboardingApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.shops.onboarding-applications', compact('applications', 'stats', 'status'));
    }

    /**
     * Show onboarding application details
     */
    public function showApplication(OnboardingApplication $application)
    {
        return view('admin.shops.onboarding-application-show', compact('application'));
    }


/**
 * Approve onboarding application and create shop
 */
public function approveApplication(Request $request, OnboardingApplication $application)
{
    $request->validate([
        'admin_email' => 'required|email|unique:users,email',
        'admin_password' => 'required|min:8',
    ]);

    // Check if application is already approved
    if ($application->status === 'approved') {
        return back()->with('error', 'This application has already been approved.');
    }

    // Check if payment is completed
    if ($application->status !== 'paid') {
        return back()->with('error', 'Cannot approve application that hasn\'t been paid.');
    }

    $adminPassword = $request->admin_password;

    // Generate random PIN
    $randomPin = \App\Models\Shop::generateRandomPin();

    // FIX: Pass $randomPin to the transaction closure
    $result = \DB::transaction(function() use ($application, $request, $adminPassword, $randomPin) {
        // Generate shop slug from business name
        $slug = Str::slug($application->business_name);
        $counter = 1;
        $originalSlug = $slug;
        
        // Ensure slug is unique
        while (Shop::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create the shop
        $shop = Shop::create([
            'name' => $application->business_name,
            'slug' => $slug,
            'business_type' => $application->business_type,
            'subscription_status' => 'active',
            'activated_at' => now(),
            'pos_pin' => $randomPin, // Now $randomPin is available
        ]);

        // Create admin user for this shop
        $user = User::create([
            'name' => $application->owner_name,
            'email' => $request->admin_email,
            'password' => Hash::make($adminPassword),
            'shop_id' => $shop->id,
            'role' => 'shop',
        ]);

        // Create subscription for the shop
        $subscription = Subscription::create([
            'shop_id' => $shop->id,
            'plan_type' => $application->business_type,
            'activation_fee' => $application->activation_fee,
            'monthly_fee' => $application->monthly_fee,
            'is_active' => true,
            'payment_status' => 'paid',
            'activated_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        // Update application status
        $application->update([
            'status' => 'approved',
            'admin_notes' => "Approved and shop created on " . now()->format('Y-m-d H:i') . 
                           ". Shop: {$shop->name}, User: {$request->admin_email}, POS PIN: $randomPin",
        ]);

        // Return the created objects
        return [
            'shop' => $shop,
            'user' => $user,
            'pin' => $randomPin,
        ];
    });

    // Extract shop and user from result
    $shop = $result['shop'];
    $user = $result['user'];
    $pin = $result['pin'];

    // Send welcome email outside transaction to avoid issues
    try {
        \Log::info('Attempting to send welcome email', [
            'to' => $user->email,
            'shop' => $shop->name
        ]);

        // FIX: Pass the PIN to the email
        Mail::to($user->email)->queue(new ShopWelcomeEmail($shop, $user, $adminPassword, $pin));
        
        \Log::info('Welcome email sent successfully', [
            'shop_id' => $shop->id,
            'user_email' => $user->email,
            'application_id' => $application->id
        ]);

        $emailMessage = 'Welcome email sent to ' . $user->email;

    } catch (\Exception $e) {
        \Log::error('Failed to send welcome email: ' . $e->getMessage(), [
            'shop_id' => $shop->id,
            'user_email' => $user->email,
            'error' => $e->getMessage()
        ]);
        
        $emailMessage = 'Welcome email failed to send: ' . $e->getMessage();
    }

    return redirect()->route('admin.shops.onboarding-applications')
        ->with('success', "Application approved successfully! Shop and admin user created. POS PIN: $pin. " . $emailMessage);
}
    /**
     * Reject onboarding application
     */
    public function rejectApplication(Request $request, OnboardingApplication $application)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $request->rejection_reason . " - Rejected on " . now()->format('Y-m-d H:i'),
        ]);

        return back()->with('success', 'Application rejected successfully.');
    }

    /**
     * Generate random password for shop admin
     */
    public function generatePassword()
    {
        $password = Str::random(12);
        return response()->json(['password' => $password]);
    }
}