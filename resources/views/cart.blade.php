<x-layout>
    <section class="pt-24 pb-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Keranjang Belanja</h1>

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6" id="cart-container">
                        @if(empty($cart))
                            <div class="text-center py-12">
                                <i class="fas fa-shopping-basket text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Keranjang Kosong</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6">Silakan tambahkan produk ke keranjang terlebih dahulu.</p>
                                <a href="{{ route('store.index') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-md transition font-medium">
                                    Mulai Belanja
                                </a>
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach($cart as $id => $item)
                                    <div data-id="{{ $id }}" class="flex flex-col sm:flex-row items-center gap-4 pb-6 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                                        <img src="{{ asset($item['image'] ?? '') }}" alt="{{ $item['name'] ?? '' }}" class="w-24 h-24 object-cover rounded-md shadow-sm border border-gray-200 dark:border-gray-700">

                                        <div class="flex-1 text-center sm:text-left w-full">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $item['name'] ?? '' }}</h3>
                                            <div class="text-amber-600 font-semibold mb-2">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</div>
                                            <button type="button" onclick="removeItem('{{ $id }}')" class="text-red-500 hover:text-red-700 text-sm transition flex items-center gap-1 mx-auto sm:mx-0">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </div>

                                        <div class="flex items-center gap-3 border border-gray-300 dark:border-gray-600 rounded-md p-1">
                                            <button type="button" onclick="updateQty('{{ $id }}', -1)" class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition">-</button>
                                            <span class="w-8 text-center font-semibold text-gray-900 dark:text-white">{{ $item['quantity'] ?? 1 }}</span>
                                            <button type="button" onclick="updateQty('{{ $id }}', 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition">+</button>
                                        </div>

                                        <div class="text-right sm:w-32 mt-2 sm:mt-0 w-full sm:block text-center">
                                            <p class="text-sm text-gray-500 dark:text-gray-400 sm:hidden">Total Produk:</p>
                                            <p class="font-bold text-gray-900 dark:text-white text-lg">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-3">Ringkasan Pesanan</h2>

                        <div class="flex justify-between mb-3 text-gray-700 dark:text-gray-300">
                            <span>Subtotal</span>
                            <span id="summary-subtotal" class="font-semibold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-6 text-gray-700 dark:text-gray-300">
                            <span>Pajak & Layanan</span>
                            <span class="font-semibold text-green-600">Gratis</span>
                        </div>
                        <hr class="my-4 border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between mb-6 text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total Tagihan</span>
                            <span id="summary-total" class="text-amber-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <button type="button" onclick="window.location.href='{{ route('checkout.index') }}'" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-md font-semibold transition shadow-md flex justify-center items-center gap-2">
                            <i class="fas fa-lock"></i> Lanjut ke Pembayaran
                        </button>
                        <a href="{{ route('store.index') }}" class="block text-center mt-4 text-amber-600 hover:text-amber-700 dark:text-amber-400 text-sm font-medium transition">
                            Back to Store
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        async function updateQty(id, change) {
            const container = document.querySelector(`[data-id="${id}"]`);
            const qtyDisplay = container?.querySelector('span.w-8');
            const currentQty = parseInt(qtyDisplay?.textContent || '1');
            const qty = Math.max(1, currentQty + change);

            const response = await fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: id, qty }),
            });
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        }

        async function removeItem(id) {
            const response = await fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: id }),
            });
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        }
    </script>
</x-layout>