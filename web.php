<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Http\Controllers\Dashboard\Admin\AnalyticsController;

// Dashboard Route ------
Route::get('/', function () {
    return view('dashboard.home');
})->name('home');

// Analytics Routes
Route::middleware(['auth', 'role:admin'])->prefix('analytics')->group(function () {
    Route::get('/sales', [AnalyticsController::class, 'getSalesAnalytics'])->name('analytics.sales');
    Route::get('/orders', [AnalyticsController::class, 'getOrderAnalytics'])->name('analytics.orders');
    Route::get('/products', [AnalyticsController::class, 'getProductAnalytics'])->name('analytics.products');
    Route::get('/reviews', [AnalyticsController::class, 'getReviewAnalytics'])->name('analytics.reviews');
    Route::get('/dashboard', [AnalyticsController::class, 'getDashboardOverview'])->name('analytics.dashboard');
});

//Products Route -----------------------------------------------------------
// index
Route::get('/products', function () {
    $products = Product::paginate(10);
    return view('dashboard.products.index', compact('products'));
})->name('products.index');

//create
Route::get('/products/create', function () {
    $categories = Category::orderBy('name')->get();
    return view('dashboard.products.create', compact('categories'));
})->name('products.create');

//show
Route::get('/products/{product}', function (Product $product) {
    return view('dashboard.products.show', compact('product'));
})->name('products.show');

//store
Route::post('/products', function () {
    return redirect()->route('products.index');
})->name('products.store');

Route::get('/products/{product}/edit', function (Product $product) {
    return view('dashboard.products.create', compact('product'));
})->name('products.edit');
