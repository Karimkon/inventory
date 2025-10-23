<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Complete Payment - SN General Hardware</title>
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
                <h1 class="text-3xl font-bold mb-2">💳 Complete Payment</h1>
                <p class="text-blue-200">Activation Fee: {{ $plan['name'] }}</p>
                <a href="{{ route('onboarding.show') }}" class="text-cyan-300 hover:text-cyan-100 text-sm mt-4 inline-block">
                    ← Back to Application
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-600 text-white p-4 rounded-lg mb-6 text-center">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Application Summary -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 mb-6">
                <h3 class="text-xl font-bold mb-4">Application Summary</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Business:</span>
                        <div class="font-semibold">{{ $application->business_name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Owner:</span>
                        <div class="font-semibold">{{ $application->owner_name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Plan:</span>
                        <div class="font-semibold">{{ $plan['name'] }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Reference:</span>
                        <div class="font-semibold">{{ $application->reference }}</div>
                    </div>
                </div>
            </div>

            <!-- Payment Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20">
                <div class="text-center mb-6">
                    <div class="text-4xl font-bold text-green-400 mb-2">
                        UGX {{ number_format($application->activation_fee) }}
                    </div>
                    <div class="text-gray-300">One-time Activation Fee</div>
                </div>

                <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">📞</div>
                        <div>
                            <div class="font-bold">After Payment:</div>
                            <div class="text-sm">Call <strong>+256-XXX-XXXXXX</strong> for activation</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('onboarding.process-payment', $application) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 py-4 rounded-lg font-bold text-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                        <span>Pay with Pesapal</span>
                        <span class="ml-2">→</span>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <div class="text-sm text-gray-400">Secure payment processed by Pesapal</div>
                    <div class="text-xs text-gray-500 mt-2">Reference: {{ $application->reference }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>