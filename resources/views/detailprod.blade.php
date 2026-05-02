<x-layout>

<section class="py-16" style="background-image: url('{{ asset('') }}'); background-size: cover; background-position: center;">
    
    <div class="container mx-auto px-4">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">

            <!-- Gambar -->
            <div>
                <img src="{{ asset($product->image) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full rounded-lg shadow-lg object-cover">
            </div>

            <!-- Detail -->
            <div>
                <span class="text-sm text-amber-600 font-semibold uppercase">
                    {{ Str::title(str_replace('-', ' ', $product->category)) }}
                </span>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-4">
                    {{ $product->name }}
                </h1>

                <div class="text-2xl font-bold text-amber-600 mb-6">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>

                <div class="text-2xl font-bold text-black-600 mb-6">
                    Stok : {{ number_format($product->stock, 0, ',', '.') }}
                </div>
                <div class="text-2xl font-bold text-black-600 mb-6">
                    Rating : {{ number_format($product->rating, 1, ',', '.') }} / 5
                </div>

                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                   Released on {{ $product->created_at }}
                </p>
                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ $product->description }}
                </p>

                <!-- Button -->
                <div class="flex gap-4">
                    <button onclick="addToCart({{ $product->id }})"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-md font-semibold transition">
                        <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                    </button>
                    <button onclick="buyNow({{ $product->id }})"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-md font-semibold transition">
                        <i class="fas fa-credit-card mr-2"></i>Buy Now
                    </button>

                    <a href="{{ route('store.index') }}#products"
                       class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Back
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

</x-layout>