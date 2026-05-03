<x-layout>
<div class="container mx-auto px-4 py-8 max-w-lg text-center">

    {{-- Icon --}}
    @if($order->status === 'processing')
        <div class="w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-5xl text-green-500"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Pesanan Diterima!</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Pesanan COD kamu sedang diproses dan akan segera dikirim.</p>
    @else
        <div class="w-24 h-24 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-hourglass-half text-5xl text-amber-500"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Menunggu Konfirmasi!</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Bukti pembayaran kamu sudah kami terima. Pesanan akan diproses setelah pembayaran dikonfirmasi.</p>
    @endif

    {{-- Detail Order --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 text-left mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-gray-900 dark:text-white">Detail Pesanan</h2>
            <span class="text-xs font-semibold px-3 py-1 rounded-full
                @if($order->status === 'waiting_confirmation') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                @elseif($order->status === 'processing') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                @elseif($order->status === 'paid') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                @elseif($order->status === 'completed') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                @elseif($order->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400 @endif">
                {{ $order->status_label }}
            </span>
        </div>

        <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400 mb-4">
            <div class="flex justify-between">
                <span>Kode Order</span>
                <span class="font-bold text-amber-600">{{ $order->order_code }}</span>
            </div>
            <div class="flex justify-between">
                <span>Nama</span>
                <span class="text-gray-900 dark:text-white">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>No. HP</span>
                <span class="text-gray-900 dark:text-white">{{ $order->customer_phone }}</span>
            </div>
            <div class="flex justify-between">
                <span>Metode Bayar</span>
                <span class="text-gray-900 dark:text-white uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</span>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
            @foreach($order->items as $item)
            <div class="flex justify-between text-sm">
                <span class="text-gray-700 dark:text-gray-300">{{ $item->product->name }} × {{ $item->quantity }}</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
            <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-200 dark:border-gray-700">
                <span class="text-gray-900 dark:text-white">Total</span>
                <span class="text-amber-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <a href="{{ route('store.index') }}"
       class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-xl font-bold transition">
        <i class="fas fa-home mr-2"></i>Kembali ke Toko
    </a>

    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
        Simpan kode ordermu: <strong>{{ $order->order_code }}</strong>
    </p>
</div>
</x-layout>