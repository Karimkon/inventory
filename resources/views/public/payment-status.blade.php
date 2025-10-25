<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Status - Redvers Shopflow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-bg {
            background: linear-gradient(-45deg, #0f2027, #203a43, #2c5364, #0f2027);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="gradient-bg text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold mb-2">
                    @if(session('success') || ($success ?? false)) ✅ @else ❌ @endif
                    Payment Status
                </h1>
                <a href="{{ route('onboarding.show') }}" class="text-cyan-300 hover:text-cyan-100 text-sm mt-4 inline-block">
                    ← Back to Application
                </a>
            </div>

            <!-- Status Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20 text-center">
                @if(session('success') || ($success ?? false))
                    <!-- Success State -->
                    <div class="text-6xl mb-4">🎉</div>
                    <h2 class="text-2xl font-bold text-green-400 mb-4">Payment Successful!</h2>
                    
                    <div class="bg-green-500/20 border border-green-500/50 rounded-lg p-4 mb-6">
                        <p class="font-semibold">{{ session('success') ?? $success ?? 'Your payment has been processed successfully.' }}</p>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Reference Number</div>
                            <div class="font-mono font-bold text-lg">{{ $reference ?? $application->reference ?? 'N/A' }}</div>
                        </div>
                        
                        @if(isset($application))
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Business Name</div>
                            <div class="font-semibold">{{ $application->business_name }}</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Application Status</div>
                            <div class="font-semibold capitalize">{{ $application->status }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Important Next Steps -->
                    <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div class="text-2xl mr-3">📞</div>
                            <div class="text-left">
                                <div class="font-bold mb-2">Next Step: Call for Activation</div>
                                <div class="text-sm">
                                    Please call our admin at <strong>+256-707208954</strong> to complete your account setup.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Check Info -->
                    <div class="bg-blue-500/20 border border-blue-500/50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="text-2xl mr-3">ℹ️</div>
                            <div class="text-left">
                                <div class="font-bold mb-2">What's Next?</div>
                                <div class="text-sm">
                                    Your application is now <strong>paid</strong> and pending admin approval. 
                                    You can check your application status anytime using your reference number.
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- Error/Failed State -->
                    <div class="text-6xl mb-4">😞</div>
                    <h2 class="text-2xl font-bold text-red-400 mb-4">Payment Issue</h2>
                    
                    <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 mb-6">
                        <p class="font-semibold">{{ session('error') ?? $error ?? 'Payment failed or was cancelled' }}</p>
                    </div>

                    @if(isset($reference) || isset($application))
                    <div class="bg-white/5 rounded-lg p-4 mb-6">
                        <div class="text-sm text-gray-400">Reference Number</div>
                        <div class="font-mono font-bold">{{ $reference ?? $application->reference ?? 'N/A' }}</div>
                    </div>
                    @endif

                    @if(isset($application) && $application->status === 'processing_payment')
                    <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div class="text-2xl mr-3">⏳</div>
                            <div class="text-left">
                                <div class="font-bold mb-2">Payment Processing</div>
                                <div class="text-sm">
                                    Your payment is still being processed. This can take a few minutes. 
                                    Please check your application status again in a few moments.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Retry Options -->
                    <div class="space-y-3">
                        @if(isset($application) && $application->status !== 'paid')
                        <a href="{{ route('onboarding.payment', $application) }}" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg font-semibold transition-colors">
                            Try Payment Again
                        </a>
                        @else
                        <a href="{{ route('onboarding.show') }}" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg font-semibold transition-colors">
                            Start New Application
                        </a>
                        @endif
                        
                        <div class="text-sm text-gray-400">
                            or contact support if the problem persists
                        </div>
                    </div>
                @endif
            </div>

            <!-- Additional Information -->
            <div class="grid md:grid-cols-2 gap-6 mt-8 text-center">
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">📋</div>
                    <h3 class="font-bold mb-2">Check Status</h3>
                    <p class="text-sm text-gray-300 mb-3">Track your application progress</p>
                    @if(isset($application))
                    <a href="{{ route('onboarding.status.show', $application->reference) }}" class="text-cyan-300 hover:text-cyan-100 text-sm">
                        View Status →
                    </a>
                    @else
                    <a href="{{ route('onboarding.status') }}" class="text-cyan-300 hover:text-cyan-100 text-sm">
                        View Status →
                    </a>
                    @endif
                </div>
                <div class="bg-white/10 p-5 rounded-lg shadow-lg">
                    <div class="text-3xl mb-3">🛠️</div>
                    <h3 class="font-semibold mb-3 text-xl">Need Assistance?</h3>
                    <p class="text-sm text-gray-300">Reach out to our support team:</p>
                    <div class="flex flex-col mt-2">
                        <span class="text-sm text-gray-200">Phone: <a href="tel:+256707208954" class="text-blue-400">+256-707208954</a></span>
                        <span class="text-sm text-gray-200">Alt: <a href="tel:+256788135150" class="text-blue-400">+256-788135150</a></span>
                        <span class="text-xs text-gray-400 mt-1">Email: <a href="mailto:redvers@gmail.com" class="text-blue-400">redvers@gmail.com</a></span>
                    </div>
                </div>
            </div>

            <!-- Debug Info (Remove in production) -->
            @if(isset($application) && app()->environment('local'))
            <div class="mt-8 p-4 bg-black/20 rounded-lg">
                <details>
                    <summary class="cursor-pointer text-sm text-gray-400">Debug Info</summary>
                    <div class="mt-2 text-left text-xs">
                        <div><strong>Application ID:</strong> {{ $application->id }}</div>
                        <div><strong>Status:</strong> {{ $application->status }}</div>
                        <div><strong>Payment Reference:</strong> {{ $application->payment_reference }}</div>
                        <div><strong>Tracking ID:</strong> {{ $application->pesapal_tracking_id }}</div>
                        <div><strong>Paid At:</strong> {{ $application->paid_at }}</div>
                    </div>
                </details>
            </div>
            @endif
        </div>
    </div>
</body>
</html>