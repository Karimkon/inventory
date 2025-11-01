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
        $monthOptions = $this->getMonthOptions();
        return view('public.onboarding', compact('monthOptions'));
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
        'months' => 'required|integer|min:1|max:48',
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

    // FOR ONBOARDING: Only charge activation fee, monthly fees will be paid later
    $totalAmount = $plan['activation_fee']; // Only activation fee

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
        'months_paid' => $request->months, // Save selected months for future reference
        'total_amount' => $totalAmount, // Only activation fee
        'status' => 'pending',
    ]);

    Log::info('Application created - Onboarding (Activation Fee Only)', [
        'application_id' => $application->id,
        'months_selected' => $request->months,
        'activation_fee' => $plan['activation_fee'],
        'monthly_fee' => $plan['monthly_fee'],
        'total_charged' => $totalAmount
    ]);

    return redirect()->route('onboarding.payment', $application)
        ->with('success', 'Application submitted! Please pay the activation fee to proceed.');
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
        $monthOptions = $this->getMonthOptions();
        return view('public.payment', compact('application', 'plan', 'monthOptions'));
    }

   /**
 * Process Pesapal payment
 */
public function processPayment(Request $request, OnboardingApplication $application)
{
    Log::info('=== ONBOARDING PAYMENT STARTED ===', [
        'application_id' => $application->id,
        'application_data' => $application->toArray()
    ]);

    if ($application->status === 'paid') {
        Log::warning('Payment already completed', ['application_id' => $application->id]);
        return redirect()->route('onboarding.status', ['reference' => $application->reference])
            ->with('info', 'Payment already completed.');
    }

    try {
        $reference = 'PAY-' . Str::uuid()->toString();
        $plan = self::PLANS[$application->business_type];
        
        $orderData = [
            "id" => Str::uuid()->toString(),
            "currency" => "UGX",
            "amount" => $application->activation_fee, // Only activation fee
            "description" => "Shop Activation Fee - {$plan['name']} (Subscription: {$application->months_paid} months)",
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

        Log::info('Sending activation fee to Pesapal', [
            'activation_fee' => $application->activation_fee,
            'description' => $orderData['description'],
            'future_subscription' => $application->months_paid . ' months'
        ]);

        $response = $this->pesapalService->submitOrder($orderData);
        
        Log::info('Pesapal API Response', ['response' => $response]);

        if (isset($response['order_tracking_id'])) {
            // Update application with payment info
            $application->update([
                'pesapal_tracking_id' => $response['order_tracking_id'],
                'payment_reference' => $reference,
                'status' => 'processing_payment',
            ]);

            // Store in session as backup
            session([
                'pending_application_id' => $application->id,
                'pending_payment_reference' => $reference,
            ]);

            Log::info('Activation payment initiated', [
                'application_id' => $application->id,
                'tracking_id' => $response['order_tracking_id'],
                'amount' => $application->activation_fee,
                'future_subscription' => $application->months_paid . ' months'
            ]);

            return redirect()->away($response['redirect_url']);
        }

        Log::error('Pesapal response missing tracking ID', ['response' => $response]);
        throw new \Exception('Failed to get tracking ID from Pesapal. Response: ' . json_encode($response));

    } catch (\Exception $e) {
        Log::error('Activation Payment Error: ' . $e->getMessage(), [
            'application_id' => $application->id,
            'trace' => $e->getTraceAsString()
        ]);
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
        'all_params' => $request->all(),
        'session_data' => session()->all()
    ]);

    // Try multiple ways to find the application
    $application = null;

    // 1. First try session (for immediate redirects)
    $applicationId = session('pending_application_id');
    if ($applicationId) {
        $application = OnboardingApplication::find($applicationId);
        Log::info('Found application via session', ['application_id' => $applicationId]);
    }

    // 2. Try by payment reference (fallback)
    if (!$application && $orderMerchantReference) {
        $application = OnboardingApplication::where('payment_reference', $orderMerchantReference)->first();
        Log::info('Trying to find application by payment reference', ['reference' => $orderMerchantReference]);
    }

    // 3. Try by Pesapal tracking ID (another fallback)
    if (!$application && $orderTrackingId) {
        $application = OnboardingApplication::where('pesapal_tracking_id', $orderTrackingId)->first();
        Log::info('Trying to find application by tracking ID', ['tracking_id' => $orderTrackingId]);
    }

    if (!$application) {
        Log::error('Application not found in callback', [
            'session_id' => $applicationId,
            'merchant_ref' => $orderMerchantReference,
            'tracking_id' => $orderTrackingId
        ]);
        
        return view('public.payment-status')
            ->with('error', 'Application not found. Please contact support with your reference number.')
            ->with('reference', $orderMerchantReference);
    }

    try {
        // Verify payment status with Pesapal
        $statusResponse = $this->pesapalService->getTransactionStatus($orderTrackingId);
        
        Log::info('Pesapal Status Response', [
            'application_id' => $application->id,
            'status_response' => $statusResponse
        ]);

        if ($statusResponse && isset($statusResponse['payment_status_description'])) {
            $paymentStatus = strtolower($statusResponse['payment_status_description']);
            
            Log::info('Payment Status Determined', [
                'application_id' => $application->id,
                'status' => $paymentStatus
            ]);

            if ($paymentStatus === 'completed') {
                // Update application as paid (pending admin approval)
                $application->update([
                    'status' => 'paid',
                    'payment_reference' => $orderMerchantReference,
                    'paid_at' => now(),
                    'pesapal_tracking_id' => $orderTrackingId,
                ]);

                // Clear session
                session()->forget(['pending_application_id', 'pending_payment_reference']);

                Log::info('Payment marked as completed', ['application_id' => $application->id]);

                return view('public.payment-status')
                    ->with('success', 'Payment completed successfully! Please call admin for activation.')
                    ->with('reference', $application->reference)
                    ->with('application', $application);

            } else {
                // Payment is pending or failed
                $application->update([
                    'status' => 'payment_failed',
                    'admin_notes' => 'Payment status: ' . $paymentStatus,
                ]);

                Log::warning('Payment not completed', [
                    'application_id' => $application->id,
                    'status' => $paymentStatus
                ]);

                return view('public.payment-status')
                    ->with('error', 'Payment status: ' . $paymentStatus . '. Please try again.')
                    ->with('reference', $application->reference)
                    ->with('application', $application);
            }
        }

        // If we can't get status, check if payment was already recorded
        if ($application->status === 'paid') {
            Log::info('Application already marked as paid', ['application_id' => $application->id]);
            
            return view('public.payment-status')
                ->with('success', 'Payment already completed! Please call admin for activation.')
                ->with('reference', $application->reference)
                ->with('application', $application);
        }

        Log::warning('Unable to verify payment status', ['application_id' => $application->id]);

        return view('public.payment-status')
            ->with('error', 'Unable to verify payment status immediately. Please check your application status in a few minutes.')
            ->with('reference', $application->reference)
            ->with('application', $application);

    } catch (\Exception $e) {
        Log::error('Pesapal Callback Error: ' . $e->getMessage(), [
            'application_id' => $application->id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return view('public.payment-status')
            ->with('error', 'Error verifying payment: ' . $e->getMessage())
            ->with('reference', $application->reference)
            ->with('application', $application);
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
 * Pesapal IPN handler - For server-to-server notifications
 */
public function pesapalIPN(Request $request)
{
    Log::info('Pesapal IPN Received', $request->all());

    $orderTrackingId = $request->input('OrderTrackingId');
    $orderNotificationType = $request->input('OrderNotificationType');

    Log::info('IPN Processing', [
        'tracking_id' => $orderTrackingId,
        'notification_type' => $orderNotificationType
    ]);

    if ($orderNotificationType == 'IPN') {
        $application = OnboardingApplication::where('pesapal_tracking_id', $orderTrackingId)->first();

        if ($application) {
            try {
                // Verify payment status with Pesapal
                $statusResponse = $this->pesapalService->getTransactionStatus($orderTrackingId);
                
                Log::info('IPN Status Check', [
                    'application_id' => $application->id,
                    'status_response' => $statusResponse
                ]);

                if ($statusResponse && isset($statusResponse['payment_status_description'])) {
                    $paymentStatus = strtolower($statusResponse['payment_status_description']);
                    
                    Log::info('IPN Payment Status', [
                        'application_id' => $application->id,
                        'status' => $paymentStatus
                    ]);

                    if ($paymentStatus === 'completed') {
                        $application->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        Log::info('IPN: Payment marked as completed', ['application_id' => $application->id]);
                    } elseif (in_array($paymentStatus, ['failed', 'cancelled'])) {
                        $application->update([
                            'status' => 'payment_failed',
                            'admin_notes' => 'Payment failed via IPN: ' . $paymentStatus,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('IPN Processing Error: ' . $e->getMessage());
            }
        } else {
            Log::error('IPN: Application not found for tracking ID: ' . $orderTrackingId);
        }
    }

    return response()->json(['status' => 'OK']);
}

    /**
     * Show status with reference
     */
    public function showStatus($reference)
    {
        $application = OnboardingApplication::where('reference', $reference)->firstOrFail();
        return view('public.status', compact('application'));
    }

     /**
     * Get month options for dropdown
     */
    private function getMonthOptions()
    {
        $options = [];
        for ($i = 1; $i <= 48; $i++) {
            $months = $i;
            $years = floor($i / 12);
            $remainingMonths = $i % 12;
            
            if ($years > 0) {
                $label = $years . ' year' . ($years > 1 ? 's' : '');
                if ($remainingMonths > 0) {
                    $label .= ' ' . $remainingMonths . ' month' . ($remainingMonths > 1 ? 's' : '');
                }
            } else {
                $label = $months . ' month' . ($months > 1 ? 's' : '');
            }
            
            $options[$i] = $label;
        }
        return $options;
    }

}