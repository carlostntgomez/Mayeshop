<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('tienda', [ShopController::class, 'index'])->name('shop.index');
Route::get('blog', [HomeController::class, 'indexBlog'])->name('blog.index');
Route::get('categoria/{blogCategory:slug}', [HomeController::class, 'showBlogCategory'])->name('blog.category.show');
Route::get('categoria/{blogCategory:slug}/post/{post:slug}', [HomeController::class, 'showBlogDetails'])->name('blog.show');
Route::get('etiquetas/{tag:slug}', [HomeController::class, 'showBlogTag'])->name('blog.tag.show');
Route::get('producto/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('acerca-de', [HomeController::class, 'showAboutPage'])->name('about');
Route::get('contacto', [ContactController::class, 'index'])->name('contact');
Route::get('preguntas-frecuentes', [FaqController::class, 'index'])->name('faq');
Route::get('carrito', [HomeController::class, 'showCartPage'])->name('cart.index');
Route::get('checkout', [HomeController::class, 'showCheckoutPage'])->name('checkout.index');

// Nuevas páginas informativas
Route::get('terminos-y-condiciones', [HomeController::class, 'showTermsPage'])->name('terms');
Route::get('politica-de-privacidad', [HomeController::class, 'showPrivacyPage'])->name('privacy');
Route::get('politica-de-reembolso', [HomeController::class, 'showRefundPage'])->name('refund');

Route::get('{productType:slug}', [ProductTypeController::class, 'show'])->name('product_type.show');
Route::get('{productType:slug}/{category:slug}', [CategoryController::class, 'show'])->name('category.show')->scopeBindings();
Route::post('products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

// Cart Routes
Route::post('cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

// Checkout Routes
Route::post('checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

// Las rutas del panel de administración (Filament) se registran automáticamente.

Route::get('/check-filament', [App\Http\Controllers\HomeController::class, 'checkFilamentResources']);