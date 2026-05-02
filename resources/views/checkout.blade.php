<x-layout>
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-clipboard-list mr-2 text-amber-600"></i> Checkout
    </h1>

    {{-- Ringkasan Pesanan --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-5">
        <h2 class="font-bold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h2>
        <div class="space-y-4">
            @foreach($items as $item)
            <div class="flex gap-4 items-center">
                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                     class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                <div class="flex-grow">
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $item['name'] }}</p>
                    <p class="text-amber-600 text-sm">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>

                    @if($mode === 'buy_now')
                    <div class="flex items-center gap-2 mt-1">
                        <button type="button" onclick="coUpdateQty({{ $item['id'] }}, -1)"
                                class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 font-bold text-xs flex items-center justify-center">−</button>
                        <span id="co-qty-{{ $item['id'] }}" class="w-6 text-center text-sm font-semibold text-gray-900 dark:text-white">{{ $item['quantity'] }}</span>
                        <button type="button" onclick="coUpdateQty({{ $item['id'] }}, 1)"
                                class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 font-bold text-xs flex items-center justify-center">+</button>
                    </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Qty: {{ $item['quantity'] }}</p>
                    @endif
                </div>
                <p class="font-bold text-gray-900 dark:text-white text-sm flex-shrink-0"
                   id="co-sub-{{ $item['id'] }}">
                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4 flex justify-between items-center">
            <span class="font-bold text-gray-900 dark:text-white">Total</span>
            <span id="co-total" class="font-bold text-amber-600 text-lg">
                Rp {{ number_format($total, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Form Checkout --}}
    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">

        {{-- Data Pengiriman --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-5">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-map-marker-alt mr-2 text-amber-600"></i>Data Pengiriman
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                           placeholder="Nama lengkap penerima" required
                           class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                           placeholder="Email penerima" required
                           class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP / WhatsApp</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                           placeholder="08xx-xxxx-xxxx" required
                           class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Lengkap</label>
                    <textarea name="customer_address" rows="3" required
                              placeholder="Jl. ..., RT/RW, Kelurahan, Kecamatan, Kota"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('customer_address') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-5">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-credit-card mr-2 text-amber-600"></i>Metode Pembayaran
            </h2>
            <div class="space-y-3">

                {{-- Transfer BCA --}}
                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-amber-400 transition peer-checked:border-amber-500 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20">
                    <input type="radio" name="payment_method" value="transfer_bca" class="accent-amber-600" required>
                    <div class="flex items-center gap-3 flex-grow">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs font-black">BCA</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">Transfer BCA</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">No. Rek: 1234567890 a/n Lumineè Bakery</p>
                        </div>
                    </div>
                </label>

                {{-- Transfer BNI --}}
                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20">
                    <input type="radio" name="payment_method" value="transfer_bni" class="accent-amber-600">
                    <div class="flex items-center gap-3 flex-grow">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs font-black">BNI</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">Transfer BNI</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">No. Rek: 0987654321 a/n Lumineè Bakery</p>
                        </div>
                    </div>
                </label>

                {{-- Transfer Mandiri --}}
                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20">
                    <input type="radio" name="payment_method" value="transfer_mandiri" class="accent-amber-600">
                    <div class="flex items-center gap-3 flex-grow">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs font-black">MDR</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">Transfer Mandiri</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">No. Rek: 1122334455 a/n Lumineè Bakery</p>
                        </div>
                    </div>
                </label>

                {{-- COD --}}
                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20">
                    <input type="radio" name="payment_method" value="cod" class="accent-amber-600">
                    <div class="flex items-center gap-3 flex-grow">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-motorcycle text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">Bayar di Tempat (COD)</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">Bayar saat pesanan tiba</p>
                        </div>
                        
                    </div>
                </label>
                 {{-- Qris --}}
                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20">
                    <input type="radio" name="payment_method" value="qris" class="accent-amber-600">
                   <div class="flex items-center gap-3 flex-grow">
        <div class="w-10 h-10 bg-white border border-gray-300 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-qrcode text-black text-sm"></i>
        </div>

        <div>
            <p class="font-semibold text-gray-900 dark:text-white text-sm">QRIS</p>
            <p class="text-gray-500 dark:text-gray-400 text-xs">Scan & Pay</p>
        </div>
    </div>
                </label>

            </div>
            @error('payment_method')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex gap-3">
            <button type="button" onclick="cancelCheckout()"
                    class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition font-semibold">
                <i class="fas fa-times mr-2"></i>Batalkan
            </button>
            <button type="submit" onclick="return confirmCheckout()"
                    class="flex-[2] bg-amber-600 hover:bg-amber-700 text-white py-3 px-6 rounded-xl font-bold transition">
                <i class="fas fa-lock mr-2"></i>Lanjut Pembayaran
            </button>
        </div>
    </form>
</div>

<script>
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const mode = '{{ $mode }}';

    @if($mode === 'buy_now')
    function coUpdateQty(id, delta) {
        const el  = document.getElementById('co-qty-' + id);
        const qty = Math.max(1, parseInt(el.textContent) + delta);
        el.textContent = qty;

        fetch('{{ route("checkout.update-qty") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ product_id: id, qty, mode: '{{ $mode }}' }),
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('co-sub-' + id).textContent =
                    'Rp ' + d.subtotal.toLocaleString('id-ID');
                document.getElementById('co-total').textContent =
                    'Rp ' + d.total.toLocaleString('id-ID');
            }
        });
    }
    @endif

    function cancelCheckout() {
        if (!confirm('Batalkan checkout? Kamu akan kembali ke halaman toko.')) return;

        fetch('{{ route("checkout.cancel") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ mode }),
        })
        .then(() => window.location.href = '{{ route("store.index") }}');
    }

    function confirmCheckout() {
        const itemCount = Object.keys({{ json_encode($items) }}).length;
        return confirm(`Apakah Anda yakin ingin checkout ${itemCount} item?`);
    }
</script>
</x-layout>