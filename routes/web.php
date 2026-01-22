<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Image manipulation
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');

// PDF generation (Test with: https://wonky.ch.test/pdf/invoice/fca8c27b-c389-40d4-a6b1-38759877cbc3)
Route::get('/pdf/invoice/{order:uuid}', [PdfController::class, 'generateInvoice'])->name('pdf.invoice');

Route::get('/', [LandingController::class, 'index'])->name('page.landing');
Route::get('/produkte', [ProductController::class, 'index'])->name('page.products');
Route::get('/produkt/{product}', [ProductController::class, 'showDirect'])->name('page.product.direct');

Route::view('/bestellung/warenkorb', 'pages.checkout.basket')->name('page.checkout.basket');
Route::middleware(['ensure.cart.not.empty'])->group(function () {
    Route::view('/bestellung/rechnungsadresse', 'pages.checkout.invoice-address')->middleware('ensure.correct.order.step:1')->name('page.checkout.invoice-address');
    Route::view('/bestellung/lieferadresse', 'pages.checkout.delivery-address')->middleware('ensure.correct.order.step:2')->name('page.checkout.delivery-address');
    Route::view('/bestellung/zahlung', 'pages.checkout.payment')->middleware('ensure.correct.order.step:3')->name('page.checkout.payment');
    Route::view('/bestellung/zusammenfassung', 'pages.checkout.summary')->middleware('ensure.correct.order.step:4')->name('page.checkout.summary');
});
Route::view('/bestellung/bestaetigung', 'pages.checkout.confirmation')->name('page.checkout.confirmation');

// Payment callbacks (Stripe)
Route::get('/payment/success/{reference}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel/{reference}', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

// Newsletter
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/{category}', [CategoryController::class, 'get'])->name('page.category');
Route::get('/{category}/{product}', [ProductController::class, 'show'])->name('page.product')->scopeBindings();
