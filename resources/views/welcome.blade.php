<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SN General Hardware - Admin Login Only</title>
    
    <!-- Tailwind Setup -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }
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
        .role-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .role-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .logo-bounce { animation: logo-bounce 2s ease-in-out infinite; }
        @keyframes logo-bounce {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-10px) scale(1.05); }
        }
        .text-glow { text-shadow: 0 0 20px rgba(59,130,246,0.5); }
    </style>
</head>
<body class="gradient-bg text-white transition-all duration-300">

    <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-8 py-12 relative z-10">

        <!-- Main Content -->
        <div class="text-center max-w-4xl w-full">

            <!-- Logo Section -->
            <div class="mb-8 fade-in-up">
                

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold mb-4 text-glow">
                    <span class="bg-gradient-to-r from-blue-400 via-cyan-500 to-indigo-500 bg-clip-text text-transparent">
                        SN General Hardware
                    </span>
                </h1>

                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold mb-6 text-blue-200">
                    Quality Building & Hardware Supplies
                </h2>
            </div>

            <!-- Description -->
            <div class="fade-in-up mb-12">
                <p class="text-lg sm:text-xl text-gray-300 mb-4 leading-relaxed max-w-2xl mx-auto">
                    Supplying quality tools, construction materials & hardware products for your projects. 🛠️🏠
                </p>
                <p class="text-base sm:text-lg text-cyan-300 font-medium">
                    Reliable service, competitive prices ✨
                </p>
            </div>

            <!-- Only Admin Login Card -->
            <div class="fade-in-up mb-12">
                <h3 class="text-xl sm:text-2xl font-semibold mb-8 text-blue-200">
                    Admin Access
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-1 gap-6 max-w-2xl mx-auto">

                    <!-- Admin Card -->
                    <a href="{{ url('admin/login') }}" class="role-card group p-6 rounded-2xl text-center relative overflow-hidden">
                        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">👑</div>
                        <h4 class="text-xl font-bold mb-2 text-purple-300">Admin</h4>
                        <p class="text-sm text-gray-300 mb-4">Manage inventory & system settings</p>
                        <div class="bg-purple-600 hover:bg-purple-500 px-4 py-2 rounded-lg font-semibold transition-colors duration-300">
                            Admin Login
                        </div>
                    </a>
                    <a href="{{ url('shop/login') }}" class="role-card group p-6 rounded-2xl text-center relative overflow-hidden">
                        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">🛒</div>
                        <h4 class="text-xl font-bold mb-2 text-green-300">Shopistrator</h4>
                        <p class="text-sm text-gray-300 mb-4">Handle sales & customer service</p>
                        <div class="bg-green-600 hover:bg-green-500 px-4 py-2 rounded-lg font-semibold transition-colors duration-300">
                            Shopistrator Login
                        </div>
                    </a>

                </div>
            </div>

            <!-- Footer -->
            <div class="fade-in-up text-center">
                <div class="text-sm text-gray-400 mb-2">
                    © 2025 SN General Hardware. All rights reserved.
                </div>
                <div class="text-xs text-gray-500">
                    Building better projects with trusted supplies! 😊
                </div>
            </div>

        </div>
    </div>

</body>
</html>
