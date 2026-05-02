<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ─── Helpers ──────────────────────────────────────────────

    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    private function cartTotal(array $cart): int
    {
        return (int) array_sum(array_map(function ($i) {
            $qty = $i['quantity'] ?? 1;
            return $i['price'] * $qty;
        }, $cart));
    }

    private function calculateCartCount(array $cart): int
    {
        return (int) array_sum(array_map(function ($i) {
            return $i['quantity'] ?? 1;
        }, $cart));
    }

    // ─── Add to Cart ──────────────────────────────────────────

    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = $this->getCart();
        $id = $product->id;

        $qty = max(1, (int) $request->qty);

        // Cek stock
        $currentQtyInCart = $cart[$id]['quantity'] ?? 0;
        if ($product->stock < ($currentQtyInCart + $qty)) {
            return response()->json([
                'success' => false,
                'message' => "Stock {$product->name} tidak cukup! Tersedia: {$product->stock}"
            ], 400);
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = [
                'id'       => $id,
                'name'     => $product->name,
                'price'    => (int) $product->price,
                'image'    => $product->image,
                'quantity' => $qty,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => "{$product->name} Successfully added to cart!",
            'product' => $cart[$id],
            'cart_count' => $this->calculateCartCount($cart),
            'cart_total' => $this->cartTotal($cart),
        ]);
    }

    // ─── Update Qty ───────────────────────────────────────────

    public function update(Request $request)
    {
        $cart = $this->getCart();
        $id = $request->product_id;
        $qty = (int) $request->qty;

        if (isset($cart[$id])) {
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                // Cek stock
                $product = Product::find($id);
                if ($product && $product->stock < $qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock {$product->name} tidak cukup! Tersedia: {$product->stock}"
                    ], 400);
                }

                $cart[$id]['quantity'] = $qty;
            }
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'total'   => $this->cartTotal($cart),
            'count'   => $this->calculateCartCount($cart),
        ]);
    }

    // ─── Remove ───────────────────────────────────────────────

    public function remove(Request $request)
    {
        $cart = $this->getCart();

        unset($cart[$request->product_id]);

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'total'   => $this->cartTotal($cart),
            'count'   => $this->calculateCartCount($cart),
        ]);
    }

    // ─── Cart Page ────────────────────────────────────────────

    public function index()
    {
        $cart = $this->getCart();
        $total = $this->cartTotal($cart);

        return view('cart', compact('cart', 'total'));
    }

    // ─── Buy Now ──────────────────────────────────────────────

    public function buyNow(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $qty = max(1, (int) $request->qty);

        // Cek stock
        if ($product->stock < $qty) {
            return response()->json([
                'success' => false,
                'message' => "Stock {$product->name} tidak cukup! Tersedia: {$product->stock}"
            ], 400);
        }

        session()->put('buy_now', [
            $product->id => [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (int) $product->price,
                'image'    => $product->image,
                'quantity' => $qty,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lanjut ke checkout...",
            'redirect' => route('checkout.index', ['mode' => 'buy_now'])
        ]);
    }

    // ─── Checkout Page ────────────────────────────────────────

    public function checkout(Request $request)
    {
        $mode = $request->mode ?? 'cart';

        $items = $mode === 'buy_now'
            ? session()->get('buy_now', [])
            : $this->getCart();

        if (empty($items)) {
            return redirect()->route('store.index')
                ->with('error', 'Keranjang kosong!');
        }

        // Validasi stock
        foreach ($items as $id => $item) {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk tidak ditemukan!');
            }

            $requestedQty = $item['quantity'] ?? 1;
            if ($product->stock < $requestedQty) {
                return redirect()->back()->with('error', "Stock {$product->name} tidak cukup! Tersedia: {$product->stock}, diminta: {$requestedQty}");
            }
        }

        $total = $this->cartTotal($items);

        return view('checkout', compact('items', 'total', 'mode'));
    }

    // ─── Process Checkout ─────────────────────────────────────

    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'required|email|max:100',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string',
            'payment_method'   => 'required|in:transfer_bca,transfer_bni,transfer_mandiri,cod,qris',
            'mode'             => 'required|in:cart,buy_now',
        ]);

        $items = $request->mode === 'buy_now'
            ? session()->get('buy_now', [])
            : $this->getCart();

        if (empty($items)) {
            return redirect()->route('store.index')
                ->with('error', 'Keranjang kosong!');
        }

        // Validasi stock sebelum checkout
        foreach ($items as $id => $item) {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk tidak ditemukan!');
            }

            $requestedQty = $item['quantity'] ?? 1;
            if ($product->stock < $requestedQty) {
                return redirect()->back()->with('error', "Stock {$product->name} tidak cukup! Tersedia: {$product->stock}, diminta: {$requestedQty}");
            }
        }

        $total = $this->cartTotal($items);

        $order = Order::create([
            'order_number'     => 'LMN-' . strtoupper(uniqid()),
            'customer_name'    => $request->customer_name,
            'customer_email'   => $request->customer_email,
            'customer_phone'   => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'total_price'      => $total,
            'status'           => $request->payment_method === 'cod' ? 'processing' : 'pending',
            'payment_method'   => $request->payment_method,
        ]);

        foreach ($items as $item) {
            $qty = $item['quantity'] ?? 1;

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['id'],
                'quantity'   => $qty,
                'price'      => $item['price'],
                'subtotal'   => $item['price'] * $qty,
            ]);

            // Kurangi stock produk
            $product = Product::find($item['id']);
            if ($product) {
                $product->decrement('stock', $qty);
            }
        }

        // clear session
        $request->mode === 'cart'
            ? session()->forget('cart')
            : session()->forget('buy_now');

        // FIX: pakai order_number
        if ($request->payment_method === 'cod') {
            return redirect()->route('order.success', $order->order_number);
        }

        return redirect()->route('payment.upload', $order->order_number);
    }

    // ─── Payment Upload ───────────────────────────────────────

    public function paymentUpload(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('status', 'pending')
            ->firstOrFail();

        return view('payment-upload', compact('order'));
    }

    public function paymentStore(Request $request, string $orderNumber)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $path = $request->file('payment_proof')
            ->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'waiting_confirmation',
        ]);

        return redirect()->route('order.success', $orderNumber);
    }

    // ─── Success Page ─────────────────────────────────────────

    public function orderSuccess(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items.product')
            ->firstOrFail();

        return view('order-success', compact('order'));
    }

    // ─── Get Cart Count (AJAX) ────────────────────────────────

    public function cartCount()
    {
        $cart = $this->getCart();
        $count = (int) array_sum(array_map(function ($i) {
            return $i['quantity'] ?? 1;
        }, $cart));

        return response()->json(['count' => $count]);
    }

    // ─── Update Checkout Qty ──────────────────────────────────

    public function updateCheckoutQty(Request $request)
    {
        $mode = $request->mode ?? 'cart';
        $items = $mode === 'buy_now'
            ? session()->get('buy_now', [])
            : $this->getCart();

        $id = $request->product_id;
        $qty = (int) $request->qty;

        if (isset($items[$id])) {
            if ($qty <= 0) {
                unset($items[$id]);
            } else {
                // Cek stock
                $product = Product::find($id);
                if ($product && $product->stock < $qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock {$product->name} tidak cukup! Tersedia: {$product->stock}"
                    ], 400);
                }

                $items[$id]['quantity'] = $qty;
            }
        }

        $mode === 'buy_now'
            ? session()->put('buy_now', $items)
            : session()->put('cart', $items);

        $subtotal = isset($items[$id]) ? $items[$id]['price'] * $items[$id]['quantity'] : 0;

        return response()->json([
            'success'  => true,
            'subtotal' => $subtotal,
            'total'    => $this->cartTotal($items),
        ]);
    }

    // ─── Cancel Checkout ──────────────────────────────────────

    public function cancelCheckout(Request $request)
    {
        session()->forget('cart');
        session()->forget('buy_now');

        return response()->json([
            'success' => true,
            'redirect' => route('store.index'),
        ]);
    }
}