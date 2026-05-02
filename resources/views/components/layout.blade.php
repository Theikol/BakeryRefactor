<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lumineè Bakery</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // Mengaktifkan fitur dark mode manual
            theme: {
                extend: {
                    colors: {
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200 font-sans">
    
    <nav class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-amber-600 dark:text-amber-500 flex items-center">
                <i class=""></i>Lumineè Bakery
            </a>
            
            <div class="hidden md:flex space-x-6">
                <a href="/" class="font-medium hover:text-amber-600 dark:hover:text-amber-400 transition">Home</a>
                <a href="{{ url('/') }}#products" onclick="window.location.href='{{ url('/') }}#products'">
                    <i class=""></i> Products
                </a>
                <a href="{{ url('/') }}#about" onclick="window.location.href='{{ url('/') }}#about'">
                    <i class=""></i> About
                </a>
                <a href="{{ url('/') }}#contact" onclick="window.location.href='{{ url('/') }}#contact'">
                    <i class=""></i> Contact
                </a>
            </div>

            <div class="flex items-center space-x-4">
                <button onclick="document.documentElement.classList.toggle('dark')" class="text-gray-500 dark:text-gray-400 hover:text-amber-600 focus:outline-none">
                    <i class="fas fa-moon text-xl"></i>
                </button>
                <button onclick="window.location.href='{{ route('cart') }}'" class="text-gray-500 dark:text-gray-400 hover:text-amber-600 focus:outline-none relative">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-amber-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </button>

                @if (Route::has('login'))
                    <div class="hidden md:flex items-center space-x-4 border-l pl-4 border-gray-300 dark:border-gray-700">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-amber-600 dark:text-gray-300 dark:hover:text-amber-400">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-amber-600 dark:text-gray-300 dark:hover:text-amber-400">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md font-semibold transition shadow-sm">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <main class="min-h-screen pb-12">
        {{ $slot }}
    </main>

    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-600 dark:text-gray-400 font-medium">
                &copy; {{ date(2025) }} Lumineè Bakery. All rights reserved.
            </p>
        </div>
    </footer>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>