<x-app-layout>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-eye mr-2 text-amber-600"></i> Detail Produk
                </h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Gambar Produk -->
                <div>
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                    @else
                        <div class="w-full h-64 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-6xl text-gray-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Informasi Produk -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h2>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $product->category }}
                            </span>
                            @if($product->is_active)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-times mr-1"></i> Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Harga</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Stock</div>
                            <div class="text-2xl font-bold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock }}
                            </div>
                        </div>
                    </div>

                    @if($product->description)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Deskripsi</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $product->description }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Dibuat:</span>
                            <span class="text-gray-900 dark:text-white">{{ $product->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Diupdate:</span>
                            <span class="text-gray-900 dark:text-white">{{ $product->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>