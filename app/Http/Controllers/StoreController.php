<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query untuk produk
        $query = Product::query();

        // Jika ada input pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Jika ada filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Ambil data untuk dikirim ke view
        $products = $query->paginate(8); 
        $featuredProducts = Product::orderBy('rating', 'desc')->take(3)->get(); 
        
        // Mengambil daftar kategori unik langsung dari kolom 'category' di tabel produk
        $categories = Product::select('category')->distinct()->pluck('category');

        // Kembalikan ke view publicstore dengan membawa data
        return view('publicstore', compact('products', 'featuredProducts', 'categories'));
    }

   public function show($id)
{
    $product = Product::where('id', $id)->firstOrFail();

    return view('detailprod', compact('product'));
}
}
