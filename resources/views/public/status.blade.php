<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Application Status - Redvers Shopflow Uganda</title>
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
                <h1 class="text-3xl font-bold mb-2">📊 Application Status</h1>
                <p class="text-blue-200">Check your onboarding application progress</p>
                <a href="{{ route('onboarding.show') }}" class="text-cyan-300 hover:text-cyan-100 text-sm mt-4 inline-block">
                    ← Back to Application
                </a>
            </div>

            <!-- Search Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 mb-6">
                <form action="{{ route('onboarding.status') }}" method="GET" class="flex gap-4">
                    <input type="text" name="reference" 
                           placeholder="Enter your reference number (e.g., APP-...)"
                           class="flex-1 px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-400"
                           value="{{ request('reference') }}">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg font-semibold transition-colors">
                        Check Status
                    </button>
                </form>
            </div>

            @if(isset($application))
                <!-- Application Status -->
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <h3 class="text-xl font-bold mb-4">Application Details</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Reference</div>
                            <div class="font-mono font-bold">{{ $application->reference }}</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Business</div>
                            <div class="font-semibold">{{ $application->business_name }}</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Owner</div>
                            <div class="font-semibold">{{ $application->owner_name }}</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="text-sm text-gray-400">Applied On</div>
                            <div class="font-semibold">{{ $application->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <!-- Status Display -->
                    @php
                        $statusConfig = [
                            'pending' => ['color' => 'yellow', 'icon' => '⏳', 'message' => 'Waiting for payment'],
                            'processing_payment' => ['color' => 'blue', 'icon' => '🔄', 'message' => 'Processing payment'],
                            'paid' => ['color' => 'green', 'icon' => '✅', 'message' => 'Payment received - Pending activation'],
                            'approved' => ['color' => 'green', 'icon' => '🎉', 'message' => 'Approved - Shop created'],
                            'rejected' => ['color' => 'red', 'icon' => '❌', 'message' => 'Application rejected'],
                            'payment_failed' => ['color' => 'red', 'icon' => '💥', 'message' => 'Payment failed'],
                        ];
                        $config = $statusConfig[$application->status] ?? ['color' => 'gray', 'icon' => '📄', 'message' => 'Unknown status'];
                    @endphp

                    <div class="bg-{{ $config['color'] }}-500/20 border border-{{ $config['color'] }}-500/50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="text-2xl mr-3">{{ $config['icon'] }}</div>
                            <div>
                                <div class="font-bold text-{{ $config['color'] }}-300 capitalize">
                                    {{ str_replace('_', ' ', $application->status) }}
                                </div>
                                <div class="text-sm">{{ $config['message'] }}</div>
                            </div>
                        </div>
                    </div>

                    @if($application->status === 'paid')
                        <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4 mt-4">
                            <div class="flex items-start">
                                <div class="text-2xl mr-3">📞</div>
                                <div>
                                    <div class="font-bold">Next Step: Call for Activation</div>
                                    <div class="text-sm mt-1">
                                        Please call our admin at <strong>+256-741613506</strong> to complete your account setup.
                                        Your reference number is: <code class="bg-black/30 px-2 py-1 rounded">{{ $application->reference }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($application->admin_notes)
                        <div class="bg-white/5 rounded-lg p-4 mt-4">
                            <div class="text-sm text-gray-400">Admin Notes</div>
                            <div class="font-semibold">{{ $application->admin_notes }}</div>
                        </div>
                    @endif
                </div>
            @elseif(request()->has('reference'))
                <!-- No application found -->
                <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-6 text-center">
                    <div class="text-4xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold mb-2">Application Not Found</h3>
                    <p class="text-gray-300">No application found with reference: <code>{{ request('reference') }}</code></p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>