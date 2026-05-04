<!-- CHATBOT BUTTON -->
<button onclick="chatbotCs()"
    class="fixed bottom-5 right-5 z-40 bg-amber-600 hover:bg-amber-700 text-white p-4 rounded-full shadow-lg transition">
    <i class="fas fa-comments"></i>
</button>

<!-- CHATBOT MODAL -->
<div id="chatbot" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/50" onclick="chatbotCs()"></div>

    <div class="absolute bottom-5 right-5 w-full max-w-sm h-[520px] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="font-semibold text-gray-800 dark:text-white">Luma Dumb :)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tanya produk, harga, atau stok</p>
            </div>

            <button onclick="chatbotCs()" class="text-gray-500 hover:text-red-500 text-xl leading-none">
                ✕
            </button>
        </div>

        <!-- Chat body -->
        <div id="chat-body" class="flex-1 p-3 overflow-y-auto space-y-3 bg-gray-50 dark:bg-gray-950"></div>

        <!-- Input -->
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex gap-2 bg-white dark:bg-gray-900">
            <input id="chat-input" type="text"
                class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                placeholder="Ketik pesan..." />

            <button onclick="handleSendChat()"
                class="bg-amber-600 hover:bg-amber-700 text-white px-4 rounded-lg text-sm font-medium">
                Kirim
            </button>
        </div>
    </div>
</div>
<x-layout>

    {{-- ======================== HERO ======================== --}}
    <section
        id="home"
        class="h-screen relative flex items-center"
        style="background-image: url('{{ asset('images/bg.jpg') }}'); background-size: cover; background-position: center;"
    >
        <div class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-2xl">
                <h2 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-lg">
                    Lumineè Bakery, Freshly Baked, Delivered Daily
                </h2>
                <p class="text-lg text-gray-200 mb-8">
                    Rasakan roti otentik yang dibuat dari bahan pilihan, setiap hari.
                    Pengiriman cepat dan kualitas tetap terjaga untuk setiap pesanan.
                </p>
                <div class="flex gap-4">
                    <a href="#products"
                       class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-md font-semibold shadow-lg transition">
                        Shop Now
                    </a>
                    <a href="#about"
                       class="border border-amber-400 text-amber-400 hover:bg-amber-400 hover:text-white px-6 py-3 rounded-md font-semibold transition">
                        Our Story
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================== FEATURED ======================== --}}
    <section class="mb-12" id="featured">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-8"></h1>
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-8">Featured Today</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($featuredProducts ?? [] as $p)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden flex flex-col">
                        <img src="{{ asset($p->image) }}" alt="{{ $p->name }}" class="h-48 w-full object-cover">
                        <div class="p-4 flex-grow">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $p->name }}</h4>
                            <div class="text-amber-600 font-semibold my-2">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                {{ Str::limit($p->description, 80) }}
                            </p>
                        </div>
                       <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex gap-2">
    <a href="{{ route('product.show', $p->id) }}"
       class="flex-1 text-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
        View
    </a>
    <button onclick="addToCart({{ $p->id }}, 1)" class="flex-1 border border-amber-600 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 py-2 rounded transition text-sm">
        <i class="fas fa-shopping-cart mr-1"></i>
    </button>
    <button onclick="buyNow({{ $p->id }}, 1)" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2 rounded transition text-sm">
        Beli
    </button>
</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================== PRODUCTS / BESTSELLERS ======================== --}}
    <section class="mb-12" id="products">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-8">Our Bestsellers</h2>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-8">
                {{-- ✅ Beri id="search-form" agar reset button lebih spesifik --}}
                <form id="search-form" method="GET" action="{{ route('store.index') }}" class="flex flex-col md:flex-row gap-4">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products..."
                        class="flex-1 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md px-4 py-2 focus:ring-amber-500 focus:border-amber-500"
                    >
                    <select name="category" class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md px-4 py-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">All Categories</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ Str::title(str_replace('-', ' ', $category)) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="bg-gray-800 dark:bg-gray-600 hover:bg-gray-900 dark:hover:bg-gray-500 text-white px-6 py-2 rounded-md transition">
                        Search
                    </button>
                    {{-- ✅ Reset menggunakan getElementById agar tidak salah target form --}}
                    <button type="button"
                            onclick="document.getElementById('search-form').reset(); window.location.href='{{ route('store.index') }}';"
                            class="bg-gray-800 dark:bg-gray-600 hover:bg-gray-900 dark:hover:bg-gray-500 text-white px-6 py-2 rounded-md transition">
                        Clear
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    @forelse ($products ?? [] as $product)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition flex flex-col">
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-48 w-full object-cover">
            <div class="p-4 flex-grow">
                <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">
                    {{ Str::title(str_replace('-', ' ', $product->category)) }}
                </span>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $product->name }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                    {{ Str::limit($product->description, 60) }}
                </p>
                <div class="font-bold text-gray-900 dark:text-gray-100 mt-3">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="p-3 border-t border-gray-100 dark:border-gray-700 grid grid-cols-3 gap-2">
                {{-- View --}}
                <a href="{{ route('product.show', $product->id) }}"
                   class="col-span-1 text-center border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-xs font-medium">
                    <i class=""></i>
                    View
                </a>

                {{-- Keranjang --}}
                <button onclick="addToCart({{ $product->id }}, 1)" class="col-span-1 w-full border border-amber-500 text-amber-600 dark:text-amber-400 py-2 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition text-xs font-medium">
                    <i class="fas fa-shopping-cart block mb-0.5"></i>
                </button>

                {{-- Beli --}}
                <button onclick="buyNow({{ $product->id }}, 1)" class="col-span-1 w-full bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg transition text-xs font-medium">
                    <i class=""></i>
                    Checkout
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
            Mungkin produk yang anda inginkan belum tersedia yaaa... <br>
            Coba cari dengan kata kunci lain atau cek kategori lainnya :)
        </div>
    @endforelse
</div>

            {{-- ✅ Gunakan null-safe operator ?-> agar tidak error saat $products null --}}
            <div class="mt-8">
                {{ $products?->links() ?? '' }}
            </div>
        </div>
    </section>

    {{-- ======================== ABOUT ======================== --}}
    <section class="bg-white dark:bg-gray-800 py-12 rounded-xl shadow-sm mb-12" id="about">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center gap-8">
            <div class="md:w-1/2">
                <img src="{{ asset('images/about-bakery.jpg') }}" alt="Dapur Lumineè Bakery" class="rounded-lg shadow-md w-full">
            </div>
            <div class="md:w-1/2 text-gray-700 dark:text-gray-300">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">About Bakery Store</h2>
                <p class="mb-4">Lumineè merupakan franchise bakery yang telah berdiri sejak tahun 1899 di bawah naungan Robert Van Fajar Sidiqq. Dengan semangat dan penuh harapan yang dimulai dari goesan gerobakan kecil, Lumineè terus berinovasi untuk memberikan pengalaman terbaik kepada pelanggannya. Sebagai satu-satunya Raksasa bakery di Denpasar, Bali, Lumineè berhasil menjadi pionir yang mampu menghadirkan standar baru dalam industri bakery modern.</p>
                <p class="mb-4">Dengan lebih dari satu abad pengalaman, Lumineè telah mengukir sejarah panjang dalam menyajikan roti berkualitas tinggi yang dibuat dengan resep tradisional dan bahan-bahan pilihan. Kami bangga menjadi bagian dari komunitas Denpasar dan terus berkomitmen untuk memberikan produk terbaik kepada pelanggan setia kami.</p>
                <p class="mb-6">Kami berkomitmen untuk menyajikan roti segar setiap hari dengan harga yang tetap terjangkau, sehingga pelanggan dapat menikmati kualitas yang terbaik yang kami sajikan.</p>
                <ul class="space-y-2">
                    <li><i class="fas fa-check-circle text-amber-500 mr-2"></i> Bahan Berkualitas Tinggi</li>
                    <li><i class="fas fa-check-circle text-amber-500 mr-2"></i> Roti Segar Setiap Hari</li>
                    <li><i class="fas fa-check-circle text-amber-500 mr-2"></i> Pengiriman Cepat</li>
                    <li><i class="fas fa-check-circle text-amber-500 mr-2"></i> Harga Terjangkau</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ======================== CONTACT ======================== --}}
    <section class="mb-12" id="contact">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Hubungi Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-700 dark:text-gray-300">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <i class="fas fa-phone text-3xl text-amber-500 mb-4"></i>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Contact Me</h3>
                    <p>King Fajar (+62) 838-7135-9565</p>
                    <p>Nuralya S (+62) 896-7366-4274</p>
                    <p>Amanda (+62) 823-2478-0051</p>
                    <p>Cyntia (+62) 858-8133-4099</p>
                    <p>Mr. Ricky P (+62) 812-1244-5191</p>
                    <p>Adrian H (+62) 878-1900-7098</p>
                    <p>Dwi Fitri (+62) 822-1188-7359</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <i class="fas fa-envelope text-3xl text-amber-500 mb-4"></i>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Email</h3>
                    <p>fajar.sidik@students.paramadina.ac.id</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <i class="fas fa-map-marker-alt text-3xl text-amber-500 mb-4"></i>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Alamat</h3>
                    <p>Jl. Udayana No.1, Dauh Puri Kangin, Kec. Denpasar Bar., Kota Denpasar, Bali 80112</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ✅ Script dipindah ke DALAM x-layout agar ikut di-render oleh layout --}}
    @if(request()->has('search') || request()->has('category'))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById("products")?.scrollIntoView({ behavior: "smooth" });
            });
        </script>
    @endif
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    async function addToCart(productId) {
        try {
            const response = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, qty: 1 }),
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message || 'Produk ditambahkan ke keranjang!');
            } else {
                alert('Gagal menambahkan produk ke keranjang.');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan saat menambahkan ke keranjang.');
        }
    }

    async function openBuyNow(productId) {
        try {
            const response = await fetch('{{ route('cart.buy-now') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, qty: 1 }),
            });
            const data = await response.json();
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                alert('Gagal memproses pembelian cepat.');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan saat memproses pembelian cepat.');
        }
    }
</script>
</x-layout>