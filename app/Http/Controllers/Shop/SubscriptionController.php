<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Shop;
use App\Services\PesapalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    protected $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        $this->pesapalService = $pesapalService;
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