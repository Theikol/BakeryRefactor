<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
// routes/web.php — tambahkan ini

// routes/web.php

use App\Http\Controllers\CartController;

// Cart
Route::get('/cart',           [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add',      [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update',   [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove',   [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/buy-now',  [CartController::class, 'buyNow'])->name('cart.buy-now');

// Checkout
Route::get('/checkout',             [CartController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout',            [CartController::class, 'processCheckout'])->name('checkout.process');
Route::post('/checkout/cancel',     [CartController::class, 'cancelCheckout'])->name('checkout.cancel');
Route::post('/checkout/update-qty', [CartController::class, 'updateCheckoutQty'])->name('checkout.update-qty');

// Payment
Route::get('/payment/{orderCode}',  [CartController::class, 'paymentUpload'])->name('payment.upload');
Route::post('/payment/{orderCode}', [CartController::class, 'paymentStore'])->name('payment.store');

// Order Success
Route::get('/order/success/{orderCode}', [CartController::class, 'orderSuccess'])->name('order.success');