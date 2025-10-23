<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Status - SN General Hardware</title>
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
                    @if(session('success')) ✅ @else ❌ @endif
                    Payment Status
                </h1>
                <a href="{{ route('onboarding.show') }}" class="text-cyan-300 hover:text-cyan-100 text-sm mt-4 inline-block">
                    ← Back to Application
                </a>
            </div>

            <!-- Status Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20 text-center">
                @if(session('success'))
                    <!-- Success State -->
                    <div class="text-6xl mb-4">🎉</div>
                    <h2 class="text-2xl font-bold text-green-400 mb-4">Payment Successful!</h2>
                    
                    <div class="bg-green-500/20 border border-green-500/50 rounded-lg p-4 mb-6">
                        <p class="font-semibold">Your payment has been processed successfully.</p>
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
                        @endif
                    </div>

                    <!-- Important Next Steps -->
                    <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div class="text-2xl mr-3">📞</div>
                            <div class="text-left">
                                <div class="font-bold mb-2">Next Step: Call for Activation</div>
                                <div class="text-sm">
                                    Please call our admin at <strong>+256-XXX-XXXXXX</strong> to complete your account setup.
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

                    @if(isset($reference))
                    <div class="bg-white/5 rounded-lg p-4 mb-6">
                        <div class="text-sm text-gray-400">Reference Number</div>
                        <div class="font-mono font-bold">{{ $reference }}</div>
                    </div>
                    @endif

                    <!-- Retry Options -->
                    <div class="space-y-3">
                        <a href="{{ route('onboarding.show') }}" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg font-semibold transition-colors">
                            Try Again
                        </a>
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
                    <a href="{{ route('onboarding.status') }}" class="text-cyan-300 hover:text-cyan-100 text-sm">
                        View Status →
                    </a>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">🛠️</div>
                    <h3 class="font-bold mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-300">Contact our support team</p>
                    <div class="text-xs text-gray-400 mt-2">support@sngeneralhardware.com</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>