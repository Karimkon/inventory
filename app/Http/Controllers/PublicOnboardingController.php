<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnboardingApplication;
use App\Services\PesapalService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PublicOnboardingController extends Controller
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
            'activation_fee' => 100000,
            'monthly_fee' => 15000,
            'features' => ['Advanced Inventory', 'Bulk Operations', 'Supplier Management']
        ],
        'hardware' => [
            'name' => 'Hardware Store',
            'activation_fee' => 150000,
            'monthly_fee' => 15000,
            'features' => ['Hardware Specific Features', 'Contractor Accounts', 'Project Tracking']
        ],
        'supermarket' => [
            'name' => 'Supermarket',
            'activation_fee' => 300000,
            'monthly_fee' => 30000,
            'features' => ['Multi-department', 'Barcode Support', 'Advanced Analytics']
        ]
    ];

    /**
     * Show onboarding form
     */
    public function showOnboarding()
    {
        return view('public.onboarding');
    }

    /**
     * Process onboarding application
     */
    public function submitApplication(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'business_type' => 'required|in:retail,wholesale,hardware,supermarket',
            'location' => 'required|string|max:255',
        ]);

        // Check if email already has pending application
        $existingApplication = OnboardingApplication::where('email', $request->email)
            ->whereIn('status', ['pending', 'paid', 'pending_approval'])
            ->first();

        if ($existingApplication) {
            return redirect()->route('onboarding.status', ['reference' => $existingApplication->reference])
                ->with('info', 'You already have a pending application. Check status below.');
        }

        $plan = self::PLANS[$request->business_type];
        $reference = 'APP-' . Str::uuid()->toString();

        // Create onboarding application
        $application = OnboardingApplication::create([
            'reference' => $reference,
            'business_name' => $request->business_name,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'business_type' => $request->business_type,
            'location' => $request->location,
            'activation_fee' => $plan['activation_fee'],
            'monthly_fee' => $plan['monthly_fee'],
            'status' => 'pending',
        ]);

        return redirect()->route('onboarding.payment', $application)
            ->with('success', 'Application submitted! Please complete payment to proceed.');
    }

    /**
     * Show payment page
     */
    public function showPayment(OnboardingApplication $application)
    {
        if ($application->status === 'paid') {
            return redirect()->route('onboarding.status', ['reference' => $application->reference])
                ->with('info', 'Payment already completed for this application.');
        }

        $plan = self::PLANS[$application->business_type];
        return view('public.payment', compact('application', 'plan'));
    }

    /**
     * Process Pesapal payment
     */
    public function processPayment(OnboardingApplication $application)
    {
        if ($application->status === 'paid') {
            return redirect()->route('onboarding.status', ['reference' => $application->reference])
                ->with('info', 'Payment already completed.');
        }

        try {
            $reference = 'PAY-' . Str::uuid()->toString();
            $plan = self::PLANS[$application->business_type];
            
            $orderData = [
                "id" => Str::uuid()->toString(),
                "currency" => "UGX",
                "amount" => $application->activation_fee,
                "description" => "Shop Activation Fee - " . $plan['name'],
                "callback_url" => route('onboarding.pesapal-callback'),
                "notification_id" => config('pesapal.notification_id'),
                "merchant_reference" => $reference,
                "billing_address" => [
                    "email_address" => $application->email,
                    "phone_number" => $application->phone,
                    "first_name" => explode(' ', $application->owner_name)[0],
                    "last_name" => explode(' ', $application->owner_name)[1] ?? '',
                    "line_1" => $application->business_name,
                    "city" => "Kampala",
                    "state" => "Central",
                    "postal_code" => "256",
                    "zip_code" => "256",
                    "country_code" => "UG"
                ]
            ];

            $response = $this->pesapalService->submitOrder($orderData);
            
            if (isset($response['order_tracking_id'])) {
                // Update application with tracking info
                $application->update([
                    'pesapal_tracking_id' => $response['order_tracking_id'],
                    'payment_reference' => $reference,
                    'status' => 'processing_payment',
                ]);

                // Store in session for callback
                session([
                    'pending_application_id' => $application->id,
                    'pending_payment_reference' => $reference,
                ]);

                return redirect()->away($response['redirect_url']);
            }

            throw new \Exception('Failed to get tracking ID from Pesapal');

        } catch (\Exception $e) {
            Log::error('Pesapal Payment Error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
 * Pesapal callback handler
 */
public function pesapalCallback(Request $request)
{
    $orderTrackingId = $request->query('OrderTrackingId');
    $orderMerchantReference = $request->query('OrderMerchantReference');

    Log::info('Pesapal Callback Received', [
        'tracking_id' => $orderTrackingId,
        'merchant_ref' => $orderMerchantReference,
        'all_params' => $request->all()
    ]);

    // Get application from session or database
    $applicationId = session('pending_application_id');
    $application = OnboardingApplication::find($applicationId);

    if (!$application) {
        Log::error('Application not found in callback', ['application_id' => $applicationId]);
        return view('public.payment-status')
            ->with('error', 'Application not found. Please contact support with your reference number.');
    }

    try {
        // Verify payment status with Pesapal
        $statusResponse = $this->pesapalService->getTransactionStatus($orderTrackingId);
        
        if ($statusResponse && isset($statusResponse['payment_status_description'])) {
            $paymentStatus = strtolower($statusResponse['payment_status_description']);
            
            if ($paymentStatus === 'completed') {
                // Update application as paid (pending admin approval)
                $application->update([
                    'status' => 'paid',
                    'payment_reference' => $orderMerchantReference,
                    'paid_at' => now(),
                ]);

                // Clear session
                session()->forget(['pending_application_id', 'pending_payment_reference']);

                return view('public.payment-status')
                    ->with('success', true)
                    ->with('message', 'Payment completed successfully! Please call admin for activation.')
                    ->with('reference', $application->reference)
                    ->with('application', $application);

            } else {
                $application->update([
                    'status' => 'payment_failed',
                ]);

                return view('public.payment-status')
                    ->with('error', 'Payment failed or was cancelled. Please try again.')
                    ->with('reference', $application->reference);
            }
        }

        return view('public.payment-status')
            ->with('error', 'Unable to verify payment status. Please contact support.')
            ->with('reference', $application->reference);

    } catch (\Exception $e) {
        Log::error('Pesapal Callback Error: ' . $e->getMessage());
        return view('public.payment-status')
            ->with('error', 'Error verifying payment: ' . $e->getMessage())
            ->with('reference', $application->reference);
    }
}
    /**
     * Check application status
     */
    public function checkStatus(Request $request)
    {
        $reference = $request->query('reference');
        
        if ($reference) {
            $application = OnboardingApplication::where('reference', $reference)->first();
            return view('public.status', compact('application'));
        }

        return view('public.status');
    }

    /**
     * Show status with reference
     */
    public function showStatus($reference)
    {
        $application = OnboardingApplication::where('reference', $reference)->firstOrFail();
        return view('public.status', compact('application'));
    }
}