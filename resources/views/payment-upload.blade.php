<x-layout>
<div class="container mx-auto px-4 py-8 max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
        <i class="fas fa-upload mr-2 text-amber-600"></i>Upload Bukti Pembayaran
    </h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Kode Order: <strong class="text-amber-600">{{ $order->order_code }}</strong></p>

    {{-- Info Rekening --}}
    @php
        $bankInfo = [
            'transfer_bca'     => ['name'=>'BCA',     'no'=>'1234567890', 'color'=>'bg-blue-600'],
            'transfer_bni'     => ['name'=>'BNI',     'no'=>'0987654321', 'color'=>'bg-orange-500'],
            'transfer_mandiri' => ['name'=>'Mandiri', 'no'=>'1122334455', 'color'=>'bg-yellow-500'],
        ];
        $bank = $bankInfo[$order->payment_method] ?? null;
    @endphp

    @if($bank)
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-5 mb-6">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Transfer ke rekening berikut:</p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 {{ $bank['color'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-black">{{ $bank['name'] }}</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-widest">{{ $bank['no'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">a/n <strong>Lumineè Bakery</strong></p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-amber-200 dark:border-amber-700 flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Jumlah Transfer</span>
            <span class="text-xl font-bold text-amber-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
    </div>
    @endif

    {{-- Upload Form --}}
    <form action="{{ route('payment.store', $order->order_number) }}" method="POST" enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
        @csrf

        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Foto Bukti Transfer
        </label>

        {{-- Drop Zone --}}
        <div id="drop-zone"
             class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center cursor-pointer hover:border-amber-400 transition mb-4"
             onclick="document.getElementById('proof-input').click()">
            <div id="dz-placeholder">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Klik atau drag foto bukti transfer disini</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">JPG, PNG — Maks. 2MB</p>
            </div>
            <img id="preview-img" src="" alt="Preview" class="hidden max-h-48 mx-auto rounded-lg">
        </div>

        <input type="file" id="proof-input" name="payment_proof" accept="image/*" class="hidden" required
               onchange="previewImage(this)">

        @error('payment_proof')
            <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-xl font-bold transition">
            <i class="fas fa-paper-plane mr-2"></i>Kirim Bukti Pembayaran
        </button>
    </form>

    <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">
        Pesanan akan dikonfirmasi dalam 1×24 jam setelah bukti diterima.
    </p>
</div>

<script>
    function previewImage(input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('dz-placeholder').classList.add('hidden');
            const img = document.getElementById('preview-img');
            img.src = e.target.result;
            img.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>
</x-layout>