<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Complete Payment - Redvers Shopflow</title>
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
                <p class="text-blue-200">{{ $plan['name'] }} - {{ $application->months_paid ?? 1 }} month(s) subscription</p>
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
                        <span class="text-gray-400">Duration:</span>
                        <div class="font-semibold">{{ $months }} month(s)</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Reference:</span>
                        <div class="font-semibold">{{ $application->reference }}</div>
                    </div>
                </div>
            </div>

            <!-- Payment Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20">
                @php
                    $months = $application->months_paid ?? 1;
                    $subscriptionTotal = $application->monthly_fee * $months;
                @endphp

                <!-- Payment Amount -->
                <div class="text-center mb-6">
                    <div class="text-4xl font-bold text-green-400 mb-2">
                        UGX {{ number_format($subscriptionTotal) }}
                    </div>
                    <div class="text-gray-300">{{ $months }} Month(s) Subscription</div>
                </div>

                <!-- Cost Breakdown -->
                <div class="bg-white/5 rounded-lg p-4 mb-6">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold">Monthly Fee</span>
                                <div class="text-xs text-gray-400">UGX {{ number_format($application->monthly_fee) }} / month</div>
                            </div>
                            <span class="text-gray-300">× {{ $months }} month(s)</span>
                        </div>

                        <div class="flex justify-between items-center border-t border-white/20 pt-3">
                            <div>
                                <span class="font-bold text-lg">Total to Pay Now</span>
                            </div>
                            <span class="text-green-400 font-bold text-lg">UGX {{ number_format($subscriptionTotal) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="text-2xl mr-3">📞</div>
                        <div>
                            <div class="font-bold mb-2">Important: Call After Payment</div>
                            <div class="text-sm">
                                After completing payment, please call our admin at
                                <a href="tel:+256741613506" class="underline font-semibold text-white">+256-741613506</a>
                                to complete your account setup and get your login credentials.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('onboarding.process-payment', $application) }}" method="POST">
                    @csrf
                    <!-- Hidden field for months -->
                    <input type="hidden" name="months" value="{{ $application->months_paid }}">
                    
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 py-4 rounded-lg font-bold text-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                        <span>Pay Subscription - UGX {{ number_format($subscriptionTotal) }}</span>
                        <span class="ml-2">→</span>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <div class="text-sm text-gray-400">Secure payment processed by Pesapal</div>
                    <div class="text-xs text-gray-500 mt-2">Reference: {{ $application->reference }}</div>
                </div>
            </div>

            <!-- Process Info -->
            <div class="grid md:grid-cols-3 gap-6 mt-8 text-center">
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">1️⃣</div>
                    <h3 class="font-bold mb-2">Pay Subscription</h3>
                    <p class="text-sm text-gray-300">Pay your monthly subscription</p>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">2️⃣</div>
                    <h3 class="font-bold mb-2">Call Admin</h3>
                    <p class="text-sm text-gray-300">Get your login credentials</p>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">3️⃣</div>
                    <h3 class="font-bold mb-2">Start Using</h3>
                    <p class="text-sm text-gray-300">Access all features immediately</p>
                </div>
            </div>

            <!-- Subscription Details -->
            <div class="bg-white/5 rounded-xl p-6 border border-white/20 mt-8">
                <h3 class="text-xl font-bold mb-4 text-center">Your {{ $months }}-Month Subscription Includes:</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    @foreach($plan['features'] as $feature)
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                        <span>24/7 Customer Support</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                        <span>Regular System Updates</span>
                    </div>
                </div>
                
                <!-- Renewal Notice -->
                <div class="mt-4 p-3 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                    <div class="text-center text-sm text-blue-300">
                        <strong>Renewal:</strong> UGX {{ number_format($application->monthly_fee) }} per month after your current period expires
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.querySelector('button[type="submit"]');
            
            payButton.addEventListener('click', function(e) {
                // Optional: Add confirmation for better UX
                if (!confirm('Confirm payment of UGX {{ number_format($subscriptionTotal) }} subscription fee?')) {
                    e.preventDefault();
                }
            });

            // Add smooth scrolling for better user experience
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>