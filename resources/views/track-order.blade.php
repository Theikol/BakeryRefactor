<x-layout>
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 dark:from-gray-900 dark:to-gray-800 py-12 px-4">
    <div class="container mx-auto max-w-2xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-location-dot text-amber-600 mr-3"></i>Lacak Pesananmu
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Pantau status pesananmu secara real-time</p>
        </div>

        {{-- Search Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-8">
            <form action="{{ route('track.search') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-hashtag text-amber-600 mr-2"></i>Nomor Pesanan
                    </label>
                    <input type="text" name="order_code" value="{{ old('order_code') }}"
                           placeholder="Contoh: LMN-abc123" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg 
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:ring-2 focus:ring-amber-500 focus:border-transparent
                                  placeholder-gray-500 dark:placeholder-gray-400">
                    @error('order_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-phone text-amber-600 mr-2"></i>Nomor Telepon
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="Nomor HP yang digunakan saat checkout" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg 
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:ring-2 focus:ring-amber-500 focus:border-transparent
                                  placeholder-gray-500 dark:placeholder-gray-400">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold 
                                           py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>Cari Pesanan
                </button>
            </form>

            @if($errors->any() && !$order)
                <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-red-700 dark:text-red-400 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Order Status Display --}}
        @if($order)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-8 md:px-8 text-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-amber-100 text-sm font-semibold uppercase tracking-wide mb-1">Nomor Pesanan</p>
                            <h2 class="text-2xl md:text-3xl font-bold">{{ $order->order_code }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-amber-100 text-sm font-semibold uppercase tracking-wide mb-1">Status</p>
                            <span class="inline-block text-xs font-bold px-4 py-2 rounded-full
                                @if($order->status === 'waiting_confirmation') bg-blue-500 
                                @elseif($order->status === 'processing') bg-green-500
                                @elseif($order->status === 'paid') bg-green-500
                                @elseif($order->status === 'completed') bg-emerald-500
                                @elseif($order->status === 'cancelled') bg-red-500
                                @else bg-gray-500 @endif">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div class="px-6 py-8 md:px-8">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-6">Tahapan Pesanan</h3>
                    
                    @php
                        $statuses = [
                            'pending' => ['icon' => 'clock', 'label' => 'Pending', 'description' => 'Pesanan sedang menunggu proses'],
                            'waiting_confirmation' => ['icon' => 'hourglass', 'label' => 'Menunggu Konfirmasi', 'description' => 'Bukti pembayaran sedang diverifikasi'],
                            'paid' => ['icon' => 'check-circle', 'label' => 'Sudah Dibayar', 'description' => 'Pembayaran sudah dikonfirmasi'],
                            'processing' => ['icon' => 'box-open', 'label' => 'Diproses', 'description' => 'Pesanan sedang dikemas'],
                            'completed' => ['icon' => 'check-double', 'label' => 'Selesai', 'description' => 'Pesanan telah sampai'],
                            'cancelled' => ['icon' => 'times-circle', 'label' => 'Dibatalkan', 'description' => 'Pesanan telah dibatalkan'],
                        ];

                        $statusOrder = ['pending', 'waiting_confirmation', 'paid', 'processing', 'completed'];
                        $currentIndex = array_search($order->status, $statusOrder);
                        if ($currentIndex === false && $order->status === 'cancelled') {
                            $currentIndex = 5;
                        }
                    @endphp

                    <div class="space-y-4">
                        @foreach(['pending', 'waiting_confirmation', 'paid', 'processing', 'completed', 'cancelled'] as $status)
                            @php
                                $statusInfo = $statuses[$status];
                                $statusIndex = in_array($status, $statusOrder) ? array_search($status, $statusOrder) : 5;
                                $isCompleted = ($currentIndex !== false && $statusIndex <= $currentIndex) || ($order->status === $status);
                                $isCurrent = $order->status === $status;
                                $isCancelled = $order->status === 'cancelled';
                            @endphp

                            <div class="flex gap-4">
                                {{-- Timeline Circle --}}
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                                        @if($isCurrent) 
                                            bg-amber-600 text-white ring-4 ring-amber-300 dark:ring-amber-700
                                        @elseif($isCompleted)
                                            bg-green-500 text-white
                                        @elseif($isCancelled && $status === 'cancelled')
                                            bg-red-500 text-white
                                        @else
                                            bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500
                                        @endif">
                                        <i class="fas fa-{{ $statusInfo['icon'] }} text-sm"></i>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-1 h-12 mt-1
                                            @if(($currentIndex !== false && $statusIndex < $currentIndex) || $isCompleted)
                                                bg-green-500
                                            @else
                                                bg-gray-200 dark:bg-gray-700
                                            @endif">
                                        </div>
                                    @endif
                                </div>

                                {{-- Status Info --}}
                                <div class="pt-1 pb-6">
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $statusInfo['label'] }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $statusInfo['description'] }}</p>
                                    @if($isCurrent)
                                        <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mt-1">
                                            <i class="fas fa-arrow-right mr-1"></i>Status Saat Ini
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Details --}}
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-8 md:px-8">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-6">Detail Pesanan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Nama Penerima</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">No. Telepon</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Email</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Metode Pembayaran</p>
                            <p class="font-semibold text-gray-900 dark:text-white uppercase">
                                {{ str_replace('_', ' ', $order->payment_method) }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Alamat Pengiriman</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_address }}</p>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-8 md:px-8">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-6">Produk Pesanan</h3>

                    <div class="space-y-4 mb-6">
                        @forelse($order->items as $item)
                            <div class="flex justify-between items-start pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </p>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada item pesanan</p>
                        @endforelse
                    </div>

                    {{-- Total --}}
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 flex justify-between items-center">
                        <span class="font-bold text-gray-900 dark:text-white">Total Pesanan:</span>
                        <span class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Timeline Info --}}
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-8 md:px-8 bg-gray-50 dark:bg-gray-900">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-4">Catatan Waktu</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Pesanan Dibuat</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $order->created_at->locale('id')->format('d M Y - H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Terakhir Diupdate</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $order->updated_at->locale('id')->format('d M Y - H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="px-6 py-6 md:px-8 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                    <a href="{{ route('store.index') }}" class="flex-1 text-center px-4 py-3 rounded-lg 
                                                               bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white 
                                                               hover:bg-gray-300 dark:hover:bg-gray-600 font-semibold transition">
                        <i class="fas fa-home mr-2"></i>Kembali ke Toko
                    </a>
                    <a href="{{ route('track.order') }}" class="flex-1 text-center px-4 py-3 rounded-lg 
                                                             bg-amber-600 hover:bg-amber-700 text-white 
                                                             font-semibold transition">
                        <i class="fas fa-search mr-2"></i>Lacak Pesanan Lain
                    </a>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="mb-6">
                    <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pesanan Tidak Ditemukan</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Masukkan nomor pesanan dan nomor HP yang Anda gunakan untuk melacak pesanan
                </p>
            </div>
        @endif
    </div>
</div>

<style>
    /* Smooth transitions */
    * {
        @apply transition-colors duration-200;
    }
</style>
</x-layout>
