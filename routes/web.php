<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;

// Models
use App\Models\Order;
use App\Models\OrderItem;

//////////////////////////////////////////////////
// 🏪 STORE
//////////////////////////////////////////////////

Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::get('/product/{id}', [StoreController::class, 'show'])->name('product.show');

//////////////////////////////////////////////////
// 🛒 CART
//////////////////////////////////////////////////

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/cart/count', [CartController::class, 'cartCount'])->name('cart.count');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');

//////////////////////////////////////////////////
// 💳 CHECKOUT
//////////////////////////////////////////////////

Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');
Route::post('/checkout/cancel', [CartController::class, 'cancelCheckout'])->name('checkout.cancel');
Route::post('/checkout/update-qty', [CartController::class, 'updateCheckoutQty'])->name('checkout.update-qty');

//////////////////////////////////////////////////
// 💰 PAYMENT
//////////////////////////////////////////////////

Route::get('/payment/{orderCode}', [CartController::class, 'paymentUpload'])->name('payment.upload');
Route::post('/payment/{orderCode}', [CartController::class, 'paymentStore'])->name('payment.store');

//////////////////////////////////////////////////
// 📦 ORDERS
//////////////////////////////////////////////////

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

// Update status order (AJAX)
Route::post('/orders/update-status/{id}', function (Request $request, $id) {
    $order = Order::findOrFail($id);
    $order->status = $request->status;
    $order->save();

    return response()->json(['success' => true]);
})->middleware('auth')->name('orders.updateStatus');

//////////////////////////////////////////////////
// 📦 PRODUCTS & 👤 CUSTOMERS
//////////////////////////////////////////////////

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
});

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

//////////////////////////////////////////////////
// 📊 REPORTS
//////////////////////////////////////////////////

Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');

//////////////////////////////////////////////////
// ⚙️ SETTINGS
//////////////////////////////////////////////////

Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

//////////////////////////////////////////////////
// ✅ ORDER SUCCESS
//////////////////////////////////////////////////

Route::get('/order/success/{orderCode}', [CartController::class, 'orderSuccess'])->name('order.success');

//////////////////////////////////////////////////
// 📊 DASHBOARD (FIX UTAMA)
//////////////////////////////////////////////////

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//////////////////////////////////////////////////
// 👤 PROFILE
//////////////////////////////////////////////////

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::delete('/orders/{id}', [OrderController::class, 'destroy'])
    ->name('orders.delete');
    Route::patch('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/update-status/{id}', function (Request $request, $id) {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true]);
    })->name('order.updateStatus');
});

//////////////////////////////////////////////////
// 🔐 AUTH
//////////////////////////////////////////////////

require __DIR__.'/auth.php';