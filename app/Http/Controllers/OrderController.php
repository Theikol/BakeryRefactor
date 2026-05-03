<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            // OPTIONAL (kalau ada relasi item)
            if ($order->orderItems()) {
                $order->orderItems()->delete();
            }

            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        
        return view('order-detail-modal', compact('order'))->render();
    }
}