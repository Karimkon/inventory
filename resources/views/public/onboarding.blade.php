<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Get Started - RedversShopflow Hardware SaaS</title>
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
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-2">🚀 Get Your Business Online</h1>
            <p class="text-xl text-blue-200">Join Redvers Shopflow SaaS Platform</p>
            <a href="{{ url('/') }}" class="text-cyan-300 hover:text-cyan-100 text-sm mt-4 inline-block">
                ← Back to Home
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6 text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-600 text-white p-4 rounded-lg mb-6 text-center">
                {{ session('info') }}
            </div>
        @endif

        <div class="max-w-4xl mx-auto">
            <!-- Business Plans -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach([
                    'retail' => ['name' => 'Retail Shop', 'price' => '2000', 'color' => 'green'],
                    'wholesale' => ['name' => 'Wholesale Business', 'price' => '120,000', 'color' => 'blue'],
                    'hardware' => ['name' => 'Hardware Store', 'price' => '200,000', 'color' => 'orange'],
                    'supermarket' => ['name' => 'Supermarket', 'price' => '500,000', 'color' => 'purple']
                ] as $type => $plan)
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <h3 class="text-xl font-bold mb-2 text-{{ $plan['color'] }}-300">{{ $plan['name'] }}</h3>
                    <div class="text-2xl font-bold mb-2">UGX {{ $plan['price'] }}</div>
                    <div class="text-sm text-gray-300 mb-4">Activation Fee</div>
                    <ul class="text-sm space-y-1 mb-4">
                        <li>✅ Inventory Management</li>
                        <li>✅ Sales Tracking</li>
                        <li>✅ Basic Reports</li>
                        <li>✅ 24/7 Support</li>
                    </ul>
                    <div class="text-xs text-gray-400">Monthly: UGX 15,000</div>
                </div>
                @endforeach
            </div>

            <!-- Application Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20">
                <h2 class="text-2xl font-bold mb-6 text-center">Business Information</h2>
                
                <form action="{{ route('onboarding.submit') }}" method="POST">
                    @csrf
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Business Details -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Business Name *</label>
                                <input type="text" name="business_name" required 
                                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-400"
                                       placeholder="e.g., City Mart Hardware">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Business Type *</label>
                                <div class="relative">
  <select name="business_type" required
    class="w-full px-4 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg 
           text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-400 
           focus:border-cyan-400 appearance-none cursor-pointer">
      <option value="">Select Business Type</option>
      <option value="retail" class="text-gray-900">Retail Shop (UGX 50,000)</option>
      <option value="wholesale" class="text-gray-900">Wholesale Business (UGX 120,000)</option>
      <option value="hardware" class="text-gray-900">Hardware Store (UGX 200,000)</option>
      <option value="supermarket" class="text-gray-900">Supermarket (UGX 500,000)</option>
  </select>

  <!-- Arrow icon -->
  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-cyan-300">
    ▼
  </div>
</div>



                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Business Location *</label>
                                <input type="text" name="location" required 
                                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-400"
                                       placeholder="e.g., Kampala, City Center">
                            </div>
                        </div>

                        <!-- Owner Details -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Owner Name *</label>
                                <input type="text" name="owner_name" required 
                                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-400"
                                       placeholder="e.g., John Smith">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Email Address *</label>
                                <input type="email" name="email" required 
                                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-400"
                                       placeholder="e.g., john@business.com">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Phone Number *</label>
                                <input type="tel" name="phone" required 
                                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-400"
                                       placeholder="e.g., 256700000000">
                            </div>
                        </div>
                    </div>
<br>
                    <!-- Add this to your onboarding form -->
                         <label class="block text-sm font-medium mb-2">Subscription Period *</label>

<div>
    <div class="relative">
  <select name="months" required
    class="w-full px-4 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg 
           text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-400 
           focus:border-cyan-400 appearance-none cursor-pointer">
      <option value="">Select Duration</option>
      @foreach($monthOptions as $value => $label)
          <option value="{{ $value }}" class="text-gray-900">{{ $label }}</option>
      @endforeach
  </select>

  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-cyan-300">
    ▼
  </div>
</div>

<p class="text-sm text-gray-300 mt-1">
  Choose how many months to pay for (1–48 months)
</p>

</div>

                    <div class="mt-8 text-center">
                        <button type="submit" 
                                class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300 transform hover:scale-105">
                            Continue to Payment →
                        </button>
                        <p class="text-sm text-gray-300 mt-4">
                            You'll pay the activation fee and then call admin for account setup
                        </p>
                    </div>
                </form>
            </div>

            <!-- Process Info -->
            <div class="grid md:grid-cols-3 gap-6 mt-8 text-center">
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">1️⃣</div>
                    <h3 class="font-bold mb-2">Fill Form</h3>
                    <p class="text-sm text-gray-300">Provide your business details</p>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">2️⃣</div>
                    <h3 class="font-bold mb-2">Pay Online</h3>
                    <p class="text-sm text-gray-300">Secure payment via Pesapal</p>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-2xl mb-2">3️⃣</div>
                    <h3 class="font-bold mb-2">Call Admin</h3>
                    <p class="text-sm text-gray-300">Get your login credentials</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>