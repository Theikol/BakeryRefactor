{{--
=======================================================================
  resources/views/dashboard.blade.php
  Lumineè Bakery — Admin Dashboard
=======================================================================
--}}

<x-layout>
    <section class="pt-24 pb-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4">
            <div class="mb-10 text-center">
                <p class="text-sm uppercase tracking-[0.3em] text-amber-600 font-semibold">Admin Dashboard</p>
                <h1 class="mt-3 text-3xl sm:text-4xl font-bold text-gray-900">Laporan Ringkas Lumineè Bakery</h1>
                <p class="mt-3 max-w-2xl mx-auto text-gray-600">Pantau performa toko, pesanan terbaru, dan produk terlaris dengan tampilan yang konsisten seperti halaman lain.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4 mb-8">
                <div class="rounded-[28px] border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-gray-500">Pendapatan Hari Ini</h2>
                            <p class="mt-4 text-3xl font-bold text-gray-900">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-amber-100 text-amber-700">
                            <i class="fas fa-coins text-xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Performa penjualan hari ini.</p>
                </div>

                <div class="rounded-[28px] border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-gray-500">Pesanan Hari Ini</h2>
                            <p class="mt-4 text-3xl font-bold text-gray-900">{{ $stats['orders_today'] ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-100 text-sky-700">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Pesanan masuk pada hari ini.</p>
                </div>

                <div class="rounded-[28px] border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-gray-500">Pesanan Pending</h2>
                            <p class="mt-4 text-3xl font-bold text-gray-900">{{ $stats['pending_count'] ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-yellow-100 text-yellow-700">
                            <i class="fas fa-hourglass-half text-xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Perlu konfirmasi segera.</p>
                </div>

                <div class="rounded-[28px] border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-gray-500">Produk Aktif</h2>
                            <p class="mt-4 text-3xl font-bold text-gray-900">{{ $stats['active_products'] ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-emerald-100 text-emerald-700">
                            <i class="fas fa-box-open text-xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Jumlah produk aktif.</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.45fr_0.95fr]">
                <div class="rounded-[28px] border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Pesanan Terbaru</h2>
                                <p class="mt-1 text-sm text-gray-500">Kelola pesanan terbaru dengan cepat.</p>
                            </div>
                            <input type="search" placeholder="Cari pesanan..." class="w-full sm:w-72 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 px-4 py-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition" />
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase tracking-[0.2em] text-gray-500">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Pelanggan</th>
                                    <th class="px-6 py-4">Total</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentOrders as $order)
                                    <tr id="row-{{ $order->id }}">
                                        <td class="px-6 py-4 font-medium text-gray-900">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">{{ $order->customer_name ?? 'Tamu' }}</div>
                                            <div class="mt-1 text-xs text-gray-500">{{ $order->orderItems->take(2)->map(fn($i) => $i->quantity.'x '.$i->product->name)->join(', ') }}@if($order->orderItems->count() > 2) +{{ $order->orderItems->count() - 2 }} lagi @endif</div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-amber-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $badgeClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'paid' => 'bg-sky-100 text-sky-800',
                                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            @if($order->status === 'pending')
                                                <button class="inline-flex items-center gap-2 rounded-full bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700 transition" onclick="updateOrderStatus({{ $order->id }}, 'paid', this)">
                                                    <i class="fas fa-check"></i> Konfirmasi
                                                </button>
                                                <button class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-white text-red-600 px-4 py-2 text-xs font-semibold hover:bg-red-50 transition" onclick="updateOrderStatus({{ $order->id }}, 'cancelled', this)">
                                                    Batal
                                                </button>
                                            @elseif($order->status === 'paid')
                                                <button class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition" onclick="updateOrderStatus({{ $order->id }}, 'completed', this)">
                                                    <i class="fas fa-check-double"></i> Selesaikan
                                                </button>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-100 px-4 py-2 text-xs text-gray-500">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada pesanan terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[28px] border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Produk Terlaris</h2>
                                <p class="mt-1 text-sm text-gray-500">Top 5 produk berdasarkan penjualan.</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @forelse($bestSellers as $item)
                                <div class="rounded-3xl border border-gray-100 bg-gray-50 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $item->product->name ?? 'Produk' }}</p>
                                            <p class="mt-1 text-sm text-gray-500">{{ number_format($item->total_sold, 0, ',', '.') }} terjual</p>
                                        </div>
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">TOP</span>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-3xl border border-gray-100 bg-gray-50 p-6 text-center text-gray-500">Belum ada data produk terlaris.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-xl font-semibold text-gray-900">Ringkasan Bulanan</h2>
                            <p class="mt-1 text-sm text-gray-500">Laporan penjualan tahun {{ now()->year }}.</p>
                        </div>
                        <div class="space-y-3">
                            @forelse($monthlySales as $month => $data)
                                <div class="flex items-center justify-between rounded-3xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <span class="text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</span>
                                    <span class="text-sm font-semibold text-amber-600">Rp {{ number_format($data->total, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="rounded-3xl border border-gray-100 bg-gray-50 p-6 text-center text-gray-500">Data bulanan belum tersedia.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const UPDATE_STATUS_URL = "{{ route('orders.updateStatus', ':id') }}";

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 right-6 z-50 rounded-3xl px-5 py-3 text-sm font-semibold shadow-xl shadow-black/10 transition duration-300 ${type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        window.updateOrderStatus = function(orderId, newStatus, btn) {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            const url = UPDATE_STATUS_URL.replace(':id', orderId);
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus }),
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal memperbarui status');
                return res.json();
            })
            .then(() => {
                showToast(`Pesanan #${String(orderId).padStart(4, '0')} diperbarui`, 'success');
                window.location.reload();
            })
            .catch(err => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                showToast(err.message || 'Gagal memperbarui status', 'error');
            });
        };
    </script>
</x-layout>
