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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    {{-- 🛒 CART MODAL --}}
    <div id="cart-modal" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 modal-content">
            {{-- Close button --}}
            <button onclick="closeCartModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>

            {{-- Content --}}
            <div class="text-center mb-6">
                <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Ditambahkan ke Keranjang!</h3>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <img src="" alt="" class="modal-product-image w-full h-40 object-cover rounded-md mb-4">
                <h4 class="modal-product-name font-bold text-gray-900 dark:text-white mb-2">Product Name</h4>
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Harga:</span>
                    <span class="modal-product-price font-semibold text-amber-600">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Jumlah:</span>
                    <span class="modal-product-qty font-semibold text-gray-900 dark:text-white">1x</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button onclick="closeCartModal()" class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                    Lanjut Belanja
                </button>
                <a href="{{ route('cart') }}" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-md transition font-medium text-center">
                    Lihat Keranjang
                </a>
            </div>
        </div>
    </div>
</body>
</html>