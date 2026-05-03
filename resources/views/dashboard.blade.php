<x-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="flex min-h-screen bg-gray-50">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-white border-r border-gray-200 shadow-sm">

        {{-- Logo --}}
        <div class="flex h-16 items-center gap-3 border-b border-gray-100 px-6">
            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-600 text-white">
                <i class="fas fa-bread-slice text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900 leading-none">Lumineè</p>
                <p class="text-[10px] text-amber-600 font-semibold uppercase tracking-widest">Bakery Admin</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
            <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Menu Utama</p>

            <button onclick="showPanel('dashboard')"
                id="nav-dashboard"
                class="nav-btn active-nav w-full flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-left transition">
                <i class="fas fa-chart-pie w-4 text-center"></i>
                Dashboard
            </button>

            <button onclick="showPanel('orders')"
                id="nav-orders"
                class="nav-btn w-full flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-left transition text-gray-600 hover:bg-gray-50">
                <i class="fas fa-receipt w-4 text-center"></i>
                Laporan Pesanan
            </button>

            <button onclick="showPanel('products')"
                id="nav-products"
                class="nav-btn w-full flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-left transition text-gray-600 hover:bg-gray-50">
                <i class="fas fa-box-open w-4 text-center"></i>
                Produk
            </button>

            <button onclick="showPanel('create-product')"
                id="nav-create-product"
                class="nav-btn w-full flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-left transition text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus-circle w-4 text-center"></i>
                Tambah Produk
            </button>
        </nav>

        {{-- Footer sidebar --}}
        <div class="border-t border-gray-100 px-4 py-4">
            <div class="flex items-center gap-3 rounded-2xl bg-gray-50 px-4 py-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                    A
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-gray-900">Admin</p>
                    <p class="truncate text-xs text-gray-500">Lumineè Bakery</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN WRAPPER ===================== --}}
    <div class="flex flex-1 flex-col pl-64">

        {{-- ============ TOPBAR ============ --}}
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-8 shadow-sm">
            <div>
                <h1 id="topbar-title" class="text-base font-bold text-gray-900">Dashboard</h1>
                <p id="topbar-subtitle" class="text-xs text-gray-500">Selamat datang di panel admin.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    Online
                </span>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-sm font-bold">
                    A
                </div>
            </div>
        </header>

        {{-- ============ CONTENT ============ --}}
        <main class="flex-1 overflow-y-auto p-8 space-y-6">

            {{-- DASHBOARD --}}
            <div id="panel-dashboard">
                {{-- Stat Cards --}}
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4 mb-6">
                    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Pendapatan Hari Ini</p>
                                <p class="mt-3 text-2xl font-bold text-gray-900">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">Performa penjualan hari ini.</p>
                    </div>

                    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Pesanan Hari Ini</p>
                                <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['orders_today'] ?? 0 }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">Pesanan masuk pada hari ini.</p>
                    </div>

                    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Pesanan Pending</p>
                                <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['pending_count'] ?? 0 }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-yellow-100 text-yellow-600">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">Perlu konfirmasi segera.</p>
                    </div>

                    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Produk Aktif</p>
                                <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['active_products'] ?? 0 }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">Jumlah produk aktif.</p>
                    </div>
                </div>

                {{-- Produk Terlaris (tampil di dashboard juga) --}}
                <div class="grid gap-6 lg:grid-cols-2 mb-6">
                    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Produk Terlaris</h2>
                                <p class="text-xs text-gray-400 mt-0.5">Top 5 produk berdasarkan penjualan.</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Top 5</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($bestSellers as $index => $item)
                                <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-5 py-4">
                                    <div class="flex items-center gap-4">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->product->name ?? 'Produk' }}</p>
                                            <p class="text-xs text-gray-400">{{ number_format($item->total_sold, 0, ',', '.') }} terjual</p>
                                        </div>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">TOP</span>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-6 text-center text-sm text-gray-400">
                                    Belum ada data produk terlaris.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Portfolio Chart (Pie/Donut) --}}
                    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm flex flex-col items-center justify-center">
                        <div class="text-center mb-5">
                            <h2 class="text-base font-bold text-gray-900">Analisis Penjualan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Distribusi produk terjual</p>
                        </div>
                        @if($bestSellers->count() > 0)
                            <div class="w-48 h-48">
                                <canvas id="salesChart"></canvas>
                            </div>
                            <div class="mt-6 space-y-2 w-full text-sm">
                                @foreach($bestSellers as $index => $item)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ ['#D97706', '#F59E0B', '#FBBF24', '#FCD34D', '#FEF08A'][$index % 5] }}"></div>
                                            <span class="text-xs text-gray-600">{{ Str::limit($item->product->name, 15) }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-900">{{ $item->total_sold }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-400">
                                <i class="fas fa-chart-pie text-3xl mb-3 block"></i>
                                <p class="text-sm">Belum ada data penjualan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- LAPORAN PESANAN --}}
            <div id="panel-orders" class="hidden">
                <div class="rounded-[24px] border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h2 class="text-base font-bold text-gray-900">Laporan Pesanan</h2>
                        <p class="mt-0.5 text-xs text-gray-400">Kelola transaksi dan status pesanan.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase tracking-[0.15em] text-gray-400 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Pelanggan</th>
                                    <th class="px-6 py-4">Total</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentOrders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-mono text-xs font-medium text-gray-500">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $order->customer_name ?? 'Tamu' }}</td>
                                        <td class="px-6 py-4 font-semibold text-amber-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <select class="text-xs px-3 py-2 rounded-full font-semibold
                                                {{ $order->status == 'paid' ? 'bg-green-100 text-green-700' : 
                                                   ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                                    ($order->status == 'completed' ? 'bg-blue-100 text-blue-700' :
                                                     ($order->status == 'processing' ? 'bg-green-100 text-green-700' :
                                                      ($order->status == 'waiting_confirmation' ? 'bg-blue-100 text-blue-700' :
                                                       'bg-red-100 text-red-700')))) }}"
                                                onchange="updateOrderStatus({{ $order->id }}, this.value)">
                                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="waiting_confirmation" {{ $order->status == 'waiting_confirmation' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button onclick="viewOrderDetail({{ $order->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-semibold inline-block">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </button>
                                            <button onclick="deleteOrder({{ $order->id }})" class="text-red-600 hover:text-red-800 text-xs font-semibold inline-block">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                                            <i class="fas fa-inbox text-2xl block mb-2 text-gray-300"></i>
                                            Belum ada pesanan terbaru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PRODUK --}}
            <div id="panel-products" class="hidden">
                <div class="rounded-[24px] border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h2 class="text-base font-bold text-gray-900">Daftar Produk</h2>
                        <p class="mt-0.5 text-xs text-gray-400">Daftar produk yang tersedia di toko.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase tracking-[0.15em] text-gray-400 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Nama Produk</th>
                                    <th class="px-6 py-4">Harga</th>
                                    <th class="px-6 py-4">Stok</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $product->name }}</td>
                                        <td class="px-6 py-4 text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-full {{ $product->stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }} px-3 py-1 text-xs font-semibold">
                                                {{ $product->stock }} pcs
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                               class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                <i class="fas fa-pen mr-1.5 text-[10px]"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Hapus produk ini?')"
                                                    class="inline-flex items-center rounded-full border border-red-200 bg-white px-4 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                                    <i class="fas fa-trash mr-1.5 text-[10px]"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                                            <i class="fas fa-box-open text-2xl block mb-2 text-gray-300"></i>
                                            Belum ada produk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAMBAH PRODUK --}}
            <div id="panel-create-product" class="hidden">
                <div class="rounded-[24px] border border-gray-200 bg-white p-8 shadow-sm max-w-2xl">
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-gray-900">Tambah Produk Baru</h2>
                        <p class="mt-0.5 text-xs text-gray-400">Isi form di bawah untuk menambahkan produk.</p>
                    </div>

                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="Contoh: Croissant Butter"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('name') border-red-500 @enderror" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <select name="category"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('category') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Roti" {{ old('category') == 'Roti' ? 'selected' : '' }}>Roti</option>
                                    <option value="Kue" {{ old('category') == 'Kue' ? 'selected' : '' }}>Kue</option>
                                    <option value="Pastry" {{ old('category') == 'Pastry' ? 'selected' : '' }}>Pastry</option>
                                    <option value="Donat" {{ old('category') == 'Donat' ? 'selected' : '' }}>Donat</option>
                                    <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">Harga (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="price" value="{{ old('price') }}" min="0"
                                    placeholder="0"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('price') border-red-500 @enderror" required>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">Stok <span class="text-red-500">*</span></label>
                                <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                                    placeholder="0"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('stock') border-red-500 @enderror" required>
                                @error('stock')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">Status</label>
                                <div class="flex items-center pt-3">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-3 text-sm text-gray-900">
                                        Aktif (tampil di toko)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Gambar Produk</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('image') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, Max: 2MB</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Deskripsi</label>
                            <textarea name="description" rows="4"
                                      class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit"
                                class="rounded-full bg-amber-600 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-700 transition shadow-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Produk
                            </button>
                            <button type="button" onclick="showPanel('products')"
                                class="rounded-full border border-gray-200 bg-white px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

<style>
    .active-nav {
        background-color: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .active-nav i {
        color: #d97706;
    }
</style>

<script>
    const panelMeta = {
        'dashboard':       { title: 'Dashboard',       subtitle: 'Ringkasan performa toko hari ini.' },
        'orders':          { title: 'Laporan Pesanan',  subtitle: 'Kelola transaksi dan status pesanan.' },
        'products':        { title: 'Produk',           subtitle: 'Daftar produk yang tersedia di toko.' },
        'create-product':  { title: 'Tambah Produk',    subtitle: 'Masukkan data produk baru.' },
    };

    function showPanel(panel) {
        // Hide all panels
        ['dashboard', 'orders', 'products', 'create-product'].forEach(p => {
            document.getElementById('panel-' + p).classList.add('hidden');
            document.getElementById('nav-' + p).classList.remove('active-nav');
            document.getElementById('nav-' + p).classList.add('text-gray-600', 'hover:bg-gray-50');
        });

        // Show selected
        document.getElementById('panel-' + panel).classList.remove('hidden');

        // Active nav
        const activeBtn = document.getElementById('nav-' + panel);
        activeBtn.classList.add('active-nav');
        activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-50');

        // Update topbar
        document.getElementById('topbar-title').textContent    = panelMeta[panel].title;
        document.getElementById('topbar-subtitle').textContent = panelMeta[panel].subtitle;
    }

    // Update Order Status via AJAX
    async function updateOrderStatus(orderId, newStatus) {
        try {
            const response = await fetch(`{{ url('/orders') }}/${orderId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (!response.ok) {
                alert('Gagal mengubah status pesanan');
                location.reload();
                return;
            }

            // Show success toast
            const message = `Status pesanan berhasil diubah menjadi ${newStatus}`;
            showToast(message, 'success');
            
            // Optional: reload page after delay
            setTimeout(() => location.reload(), 1500);
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        }
    }

    // Delete Order
    async function deleteOrder(orderId) {
        if (!confirm('Yakin ingin menghapus pesanan ini?')) return;

        try {
            const response = await fetch(`{{ url('/orders') }}/${orderId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                }
            });

            if (!response.ok) {
                alert('Gagal menghapus pesanan');
                return;
            }

            showToast('Pesanan berhasil dihapus', 'success');
            setTimeout(() => location.reload(), 1500);
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus pesanan');
        }
    }

    // Simple Toast Notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.remove(), 3000);
    }

    // View Order Detail
    async function viewOrderDetail(orderId) {
        try {
            const response = await fetch(`{{ url('/admin/orders') }}/${orderId}/detail`);
            if (!response.ok) {
                alert('Gagal memuat detail pesanan');
                return;
            }

            const html = await response.text();
            
            // Create modal container
            const modal = document.createElement('div');
            modal.id = 'order-detail-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
            modal.innerHTML = html;
            modal.onclick = (e) => {
                if (e.target === modal) modal.remove();
            };
            
            document.body.appendChild(modal);

            // Add close button functionality
            modal.querySelector('[data-close-modal]')?.addEventListener('click', () => modal.remove());
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membuka detail pesanan');
        }
    }

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('salesChart');
    if (!canvas) return;

    const labels = @json($bestSellers->pluck('product.name'));
    const data = @json($bestSellers->pluck('total_sold'));

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: ['#D97706', '#F59E0B', '#FBBF24', '#FCD34D', '#FEF08A'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>

</x-layout>