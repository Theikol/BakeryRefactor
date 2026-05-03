<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stat cards
        $stats = [
            'revenue'        => Order::whereDate('created_at', today())
                                     ->whereNotIn('status', ['cancelled'])
                                     ->sum('total_price'),
            'orders_today'   => Order::whereDate('created_at', today())->count(),
            'pending_count'  => Order::where('status', 'pending')->count(),
            'active_products' => Product::where('is_active', true)->count(),
            'revenue_growth' => $this->getRevenueGrowth(),
        ];

        // 10 pesanan terbaru beserta relasi
        $recentOrders = Order::with([
                'orderItems.product:id,name',
            ])
            ->latest()
            ->take(10)
            ->get();

        // Produk terlaris (top 5)
        $bestSellers = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Penjualan per bulan (tahun ini) untuk chart opsional ke depan
        $monthlySales = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_price) as total')
            )
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

       return view('dashboard', [
    'orders' => Order::all(),
    'products' => Product::all(),
    'stats' => $stats,
    'recentOrders' => $recentOrders,
    'bestSellers' => $bestSellers,
    'monthlySales' => $monthlySales,
]);
    }

    /**
     * Update status pesanan via AJAX (PATCH /orders/{order}/status)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,processing,waiting_confirmation,paid,completed,cancelled'],
        ]);

        // Validasi transisi status yang diizinkan
        $allowed = [
            'pending'   => ['paid', 'waiting_confirmation', 'cancelled', 'processing'],
            'processing'   => ['completed', 'cancelled'],
            'waiting_confirmation' => ['processing', 'paid', 'cancelled'],
            'paid'      => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $currentStatus = $order->status;
        $newStatus     = $request->status;

        if (! in_array($newStatus, $allowed[$currentStatus] ?? [])) {
            return response()->json([
                'message' => "Tidak bisa mengubah status dari {$currentStatus} ke {$newStatus}.",
            ], 422);
        }

        $order->update(['status' => $newStatus]);

        // Opsional: catat log aktivitas
        // activity()->performedOn($order)->log("Status diubah ke {$newStatus}");

        return response()->json([
            'message' => 'Status berhasil diperbarui.',
            'order'   => $order->fresh(['customer', 'orderItems.product']),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────

    private function getRevenueGrowth(): float
    {
        $thisMonth = Order::whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->whereNotIn('status', ['cancelled'])
                          ->sum('total_price');

        $lastMonth = Order::whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year)
                          ->whereNotIn('status', ['cancelled'])
                          ->sum('total_price');

        if ($lastMonth <= 0) return 0.0;

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}