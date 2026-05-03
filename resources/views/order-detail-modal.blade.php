<div class="max-w-2xl max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-y-auto">
    {{-- Header --}}
    <div class="sticky top-0 bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-5 text-white flex justify-between items-center border-b border-amber-700">
        <div>
            <h2 class="text-xl font-bold">Detail Pesanan</h2>
            <p class="text-sm text-amber-100 mt-1">{{ $order->order_code }}</p>
        </div>
        <button type="button" data-close-modal class="text-white hover:text-amber-100 transition text-2xl leading-none">
            ×
        </button>
    </div>

    <div class="p-6 space-y-6">
        {{-- Order Info Row --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-gray-500 mb-1">Nomor Pesanan</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->order_code }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-gray-500 mb-1">Status</p>
                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                    @if($order->status === 'waiting_confirmation') bg-blue-100 text-blue-700
                    @elseif($order->status === 'processing') bg-green-100 text-green-700
                    @elseif($order->status === 'paid') bg-green-100 text-green-700
                    @elseif($order->status === 'completed') bg-emerald-100 text-emerald-700
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="border-t border-gray-200 pt-6">
            <h3 class="font-bold text-gray-900 mb-4">
                <i class="fas fa-user text-amber-600 mr-2"></i>Informasi Pelanggan
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Email</p>
                    <p class="font-semibold text-gray-900">{{ $order->customer_email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">No. Telepon</p>
                    <p class="font-semibold text-gray-900">{{ $order->customer_phone }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Metode Pembayaran</p>
                    <p class="font-semibold text-gray-900 uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-gray-500 mb-1 text-sm">Alamat Pengiriman</p>
                <p class="font-semibold text-gray-900">{{ $order->customer_address }}</p>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="border-t border-gray-200 pt-6">
            <h3 class="font-bold text-gray-900 mb-4">
                <i class="fas fa-box text-amber-600 mr-2"></i>Detail Produk
            </h3>
            <div class="space-y-3">
                @forelse($order->items as $item)
                    <div class="flex justify-between items-start pb-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-bold text-gray-900 ml-4">
                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Tidak ada item pesanan</p>
                @endforelse
            </div>

            {{-- Total --}}
            <div class="flex justify-between items-center pt-4 mt-4 border-t-2 border-gray-200">
                <span class="font-bold text-gray-900 text-lg">Total:</span>
                <span class="font-bold text-amber-600 text-xl">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Payment Proof --}}
        @if($order->status === 'waiting_confirmation' || $order->status === 'paid' || $order->status === 'processing' || $order->status === 'completed')
            <div class="border-t border-gray-200 pt-6">
                <h3 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-image text-amber-600 mr-2"></i>Bukti Pembayaran
                </h3>
                @if($order->payment_proof)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran" 
                             class="max-w-full h-auto rounded-lg mx-auto max-h-80 cursor-pointer"
                             onclick="window.open(this.src, '_blank')">
                        <p class="text-xs text-gray-500 mt-2">Klik gambar untuk memperbesar</p>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada bukti pembayaran</p>
                @endif
            </div>
        @endif

        {{-- Order Timeline --}}
        <div class="border-t border-gray-200 pt-6">
            <h3 class="font-bold text-gray-900 mb-4">
                <i class="fas fa-clock text-amber-600 mr-2"></i>Waktu Pesanan
            </h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Dibuat</span>
                    <span class="font-semibold">{{ $order->created_at->locale('id')->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Terakhir Diupdate</span>
                    <span class="font-semibold">{{ $order->updated_at->locale('id')->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex gap-2 justify-end">
        <button type="button" data-close-modal 
                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-900 hover:bg-gray-300 font-semibold transition">
            Tutup
        </button>
        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank"
           @if(!$order->payment_proof) style="display:none" @endif
           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-semibold transition">
            <i class="fas fa-download mr-2"></i>Download Bukti
        </a>
    </div>
</div>
