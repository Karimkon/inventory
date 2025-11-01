<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionRenewal;
use App\Models\Shop;
use App\Services\PesapalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;


class SubscriptionController extends Controller
{
    protected $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        $this->pesapalService = $pesapalService;
    }

     /**
     * Show renewal form
     */
    public function showRenewal()
    {
        $shop = Auth::user()->shop;
        $activeSubscription = $shop->activeSubscription;
        
        if (!$activeSubscription) {
            return redirect()->route('shop.subscription.create')
                ->with('error', 'You need an active subscription first.');
        }

        return view('shop.subscription.renew', compact('shop', 'activeSubscription'));
    }

    /**
     * Process renewal payment
     */
   /**
 * Process renewal payment
 */
/**
 * Process renewal payment
 */
public function processRenewal(Request $request)
{
    $request->validate([
        'months' => 'required|integer|min:1|max:12'
    ]);

    \Log::info('=== RENEWAL PROCESS STARTED ===', $request->all());

    $shop = Auth::user()->shop;
    $activeSubscription = $shop->activeSubscription;

    if (!$activeSubscription) {
        return back()->with('error', 'No active subscription found.');
    }

    $months = (int) $request->months;
    $monthlyFee = $activeSubscription->monthly_fee;
    $totalAmount = $monthlyFee * $months;

    \Log::info('Renewal details', [
        'shop_id' => $shop->id,
        'subscription_id' => $activeSubscription->id,
        'months' => $months,
        'amount' => $totalAmount
    ]);

    try {
        // Process payment via Pesapal
        $user = Auth::user();
        $reference = 'RENEW-' . Str::uuid()->toString();
        
        $orderData = [
            "id" => Str::uuid()->toString(),
            "currency" => "UGX",
            "amount" => $totalAmount,
            "description" => "Subscription Renewal - {$months} month(s)",
            "callback_url" => route('shop.subscription.renewal-callback'),
            "notification_id" => config('pesapal.notification_id'),
            "merchant_reference" => $reference,
            "billing_address" => [
                "email_address" => $user->email,
                "phone_number" => $user->phone ?? '256700000000',
                "first_name" => explode(' ', $user->name)[0],
                "last_name" => explode(' ', $user->name)[1] ?? '',
                "line_1" => $shop->name,
                "city" => "Kampala",
                "state" => "Central",
                "postal_code" => "256",
                "zip_code" => "256",
                "country_code" => "UG"
            ]
        ];

        \Log::info('Sending to Pesapal', $orderData);

        $response = $this->pesapalService->submitOrder($orderData);

        if (!$response || !isset($response['redirect_url'])) {
    \Log::error('Invalid Pesapal response', ['response' => $response]);
    return back()->with('error', 'Payment gateway failed. Response: ' . json_encode($response));
}


        if (isset($response['order_tracking_id'])) {
            // Store renewal info in session for callback
            session([
                'pending_renewal_subscription_id' => $activeSubscription->id,
                'pending_renewal_months' => $months,
                'pending_renewal_amount' => $totalAmount,
                'pending_renewal_reference' => $reference,
                'pending_pesapal_tracking_id' => $response['order_tracking_id'],
            ]);

            \Log::info('Redirecting to Pesapal');
            return redirect()->away($response['redirect_url']);
        }

        throw new \Exception('Failed to get tracking ID from Pesapal. Response: ' . json_encode($response));

    } catch (\Exception $e) {
        \Log::error('Renewal Payment Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Renewal processing failed: ' . $e->getMessage());
    }
}

   /**
 * Renewal callback from Pesapal
 */
public function renewalCallback(Request $request)
{
    $orderTrackingId = $request->query('OrderTrackingId');
    $orderMerchantReference = $request->query('OrderMerchantReference');

    \Log::info('Renewal Callback Received', [
        'tracking_id' => $orderTrackingId,
        'merchant_ref' => $orderMerchantReference,
    ]);

    // Get renewal info from session
    $subscriptionId = session('pending_renewal_subscription_id');
    $renewalMonths = session('pending_renewal_months');
    $renewalAmount = session('pending_renewal_amount');
    
    $subscription = Subscription::find($subscriptionId);

    if (!$subscription) {
        \Log::error('Subscription not found in callback', ['subscription_id' => $subscriptionId]);
        return redirect()->route('shop.subscription.status')
            ->with('error', 'Subscription not found. Please contact support.');
    }

    try {
        $statusResponse = $this->pesapalService->getTransactionStatus($orderTrackingId);
        
        \Log::info('Pesapal status response', $statusResponse);

        if ($statusResponse && isset($statusResponse['payment_status_description'])) {
            $paymentStatus = strtolower($statusResponse['payment_status_description']);
            
            if ($paymentStatus === 'completed') {
                // Calculate new expiry date by adding months to current expiry
                $currentExpiry = Carbon::parse($subscription->expires_at);
                $newExpiry = $currentExpiry->copy()->addMonths($renewalMonths);
                
                \Log::info('Extending subscription', [
                    'current_expiry' => $currentExpiry,
                    'renewal_months' => $renewalMonths,
                    'new_expiry' => $newExpiry
                ]);

                // Update subscription with new expiry date
                $subscription->update([
                    'expires_at' => $newExpiry,
                    'total_amount' => $subscription->total_amount + $renewalAmount,
                    'admin_notes' => $subscription->admin_notes . "\nRenewed for {$renewalMonths} month(s) - UGX " . number_format($renewalAmount) . " on " . now()->format('Y-m-d H:i:s') . ". New expiry: " . $newExpiry->format('Y-m-d'),
                ]);

                // Clear session
                session()->forget([
                    'pending_renewal_subscription_id',
                    'pending_renewal_months', 
                    'pending_renewal_amount',
                    'pending_renewal_reference',
                    'pending_pesapal_tracking_id'
                ]);

                \Log::info('Renewal completed successfully');

                return redirect()->route('shop.subscription.status')
                    ->with('success', "Renewal successful! Subscription extended by {$renewalMonths} month(s). New expiry: {$newExpiry->format('M d, Y')}");
            } else {
                \Log::warning('Renewal payment failed', ['status' => $paymentStatus]);
                return redirect()->route('shop.subscription.status')
                    ->with('error', 'Renewal payment failed. Please try again.');
            }
        }

        \Log::error('Unable to verify payment status', $statusResponse);
        return redirect()->route('shop.subscription.status')
            ->with('error', 'Unable to verify renewal payment status.');

    } catch (\Exception $e) {
        \Log::error('Renewal Callback Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return redirect()->route('shop.subscription.status')
            ->with('error', 'Error verifying renewal payment: ' . $e->getMessage());
    }
}


    // Subscription plans configuration
    const PLANS = [
        'retail' => [
            'name' => 'Retail Shop',
            'activation_fee' => 50000,
            'monthly_fee' => 15000,
            'features' => ['Basic Inventory', 'Sales Tracking', 'Basic Reports']
        ],
        'wholesale' => [
            'name' => 'Wholesale Business',
            'activation_fee' => 120000,
            'monthly_fee' => 15000,
            'features' => ['Advanced Inventory', 'Bulk Operations', 'Supplier Management']
        ],
        'hardware' => [
            'name' => 'Hardware Store',
            'activation_fee' => 200000,
            'monthly_fee' => 15000,
            'features' => ['Hardware Specific Features', 'Contractor Accounts', 'Project Tracking']
        ],
        'supermarket' => [
            'name' => 'Supermarket',
            'activation_fee' => 500000,
            'monthly_fee' => 15000,
            'features' => ['Multi-department', 'Barcode Support', 'Advanced Analytics']
        ]
    ];

    public function create()
    {
        $shop = Auth::user()->shop;
        
        // Check if shop already has active subscription
        if ($shop->has_active_subscription) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Your shop already has an active subscription.');
        }

        return view('shop.subscription.create', compact('shop'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|in:retail,wholesale,hardware,supermarket'
        ]);

        $shop = Auth::user()->shop;
        $plan = self::PLANS[$request->plan_type];

        // Create subscription (pending payment)
        $subscription = Subscription::create([
            'shop_id' => $shop->id,
            'plan_type' => $request->plan_type,
            'activation_fee' => $plan['activation_fee'],
            'monthly_fee' => $plan['monthly_fee'],
            'is_active' => false,
            'payment_status' => 'pending',
        ]);

        // Update shop business type
        $shop->update([
            'business_type' => $request->plan_type,
            'subscription_status' => 'pending_payment'
        ]);

        return redirect()->route('shop.subscription.payment', $subscription)
            ->with('success', 'Subscription plan selected! Please complete payment to activate your account.');
    }

    public function payment(Subscription $subscription)
    {
        // Verify subscription belongs to user's shop
        if ($subscription->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        if ($subscription->payment_status === 'paid') {
            return redirect()->route('shop.subscription.status')
                ->with('info', 'Payment already completed for this subscription.');
        }

        return view('shop.subscription.payment', compact('subscription'));
    }

    public function processPayment(Request $request, Subscription $subscription)
    {
        // Verify subscription belongs to user's shop
        if ($subscription->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        if ($subscription->payment_status === 'paid') {
            return redirect()->route('shop.subscription.status')
                ->with('info', 'Payment already completed.');
        }

        try {
            $user = Auth::user();
            $reference = 'SUB-' . Str::uuid()->toString();
            
            $orderData = [
                "id" => Str::uuid()->toString(),
                "currency" => "UGX",
                "amount" => $subscription->activation_fee,
                "description" => "Shop Activation Fee - " . $subscription->plan_details['name'],
                "callback_url" => route('shop.subscription.pesapal-callback'),
                "notification_id" => config('pesapal.notification_id'),
                "merchant_reference" => $reference,
                "billing_address" => [
                    "email_address" => $user->email,
                    "phone_number" => $user->phone ?? '256700000000',
                    "first_name" => explode(' ', $user->name)[0],
                    "last_name" => explode(' ', $user->name)[1] ?? '',
                    "line_1" => $subscription->shop->name,
                    "city" => "Kampala",
                    "state" => "Central",
                    "postal_code" => "256",
                    "zip_code" => "256",
                    "country_code" => "UG"
                ]
            ];

            $response = $this->pesapalService->submitOrder($orderData);
            
            if (isset($response['order_tracking_id'])) {
                // Update subscription with tracking info
                $subscription->update([
                    'pesapal_tracking_id' => $response['order_tracking_id'],
                    'payment_reference' => $reference,
                ]);

                // Store in session for callback
                session([
                    'pending_subscription_id' => $subscription->id,
                    'pending_payment_reference' => $reference,
                ]);

                return redirect()->away($response['redirect_url']);
            }

            throw new \Exception('Failed to get tracking ID from Pesapal');

        } catch (\Exception $e) {
            \Log::error('Pesapal Payment Error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function pesapalCallback(Request $request)
    {
        $orderTrackingId = $request->query('OrderTrackingId');
        $orderMerchantReference = $request->query('OrderMerchantReference');

        \Log::info('Pesapal Callback Received', [
            'tracking_id' => $orderTrackingId,
            'merchant_ref' => $orderMerchantReference,
            'all_params' => $request->all()
        ]);

        // Get subscription from session or database
        $subscriptionId = session('pending_subscription_id');
        $subscription = Subscription::find($subscriptionId);

        if (!$subscription) {
            \Log::error('Subscription not found in callback', ['subscription_id' => $subscriptionId]);
            return redirect()->route('shop.subscription.status')
                ->with('error', 'Subscription not found. Please contact support.');
        }

        try {
            // Verify payment status with Pesapal
            $statusResponse = $this->pesapalService->getTransactionStatus($orderTrackingId);
            
            if ($statusResponse && isset($statusResponse['payment_status_description'])) {
                $paymentStatus = strtolower($statusResponse['payment_status_description']);
                
                if ($paymentStatus === 'completed') {
                    // Update subscription as paid (pending admin approval)
                    $subscription->update([
                        'payment_status' => 'paid',
                        'payment_reference' => $orderMerchantReference,
                    ]);

                    // Update shop status to pending approval
                    $subscription->shop->update([
                        'subscription_status' => 'pending_approval'
                    ]);

                    // Clear session
                    session()->forget(['pending_subscription_id', 'pending_payment_reference']);

                    return redirect()->route('shop.subscription.status')
                        ->with('success', 'Payment completed successfully! Your account is pending admin approval. You will be activated shortly.');
                } else {
                    $subscription->update([
                        'payment_status' => 'failed',
                    ]);

                    return redirect()->route('shop.subscription.status')
                        ->with('error', 'Payment failed or was cancelled. Please try again.');
                }
            }

            return redirect()->route('shop.subscription.status')
                ->with('error', 'Unable to verify payment status. Please contact support.');

        } catch (\Exception $e) {
            \Log::error('Pesapal Callback Error: ' . $e->getMessage());
            return redirect()->route('shop.subscription.status')
                ->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }

    public function status()
    {
        $shop = Auth::user()->shop;
        $subscription = $shop->subscriptions()->latest()->first();
        $activeSubscription = $shop->activeSubscription;
        
        return view('shop.subscription.status', compact('shop', 'subscription', 'activeSubscription'));
    }

    public function manualPayment(Subscription $subscription)
    {
        // For manual/bank payment instructions
        return view('shop.subscription.manual-payment', compact('subscription'));
    }
}