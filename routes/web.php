<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Shop\DashboardController as ShopDashboardController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\PosController;

// Home
Route::get('/', fn () => view('welcome'));

// ----------------------
// Login views per role
// ----------------------
Route::get('/admin/login', fn () => view('admin.auth.login'))->name('admin.login');
Route::get('/shop/login', fn () => view('shop.auth.login'))->name('shop.login');

// POS Routes (Public access)
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/login', [PosController::class, 'showLogin'])->name('login'); 
    Route::post('/login', [PosController::class, 'login'])->name('login.submit');
    Route::get('/dashboard', [PosController::class, 'dashboard'])->name('dashboard');
    Route::post('/sell/{product}', [PosController::class, 'sell'])->name('sell');
    Route::get('/receipt/{product}/{qty}', [PosController::class, 'receipt'])->name('receipt');
    Route::post('/expenses', [PosController::class, 'storeExpense'])->name('expenses.store');
    Route::post('/logout', [PosController::class, 'logout'])->name('logout');


    Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('{productId}/add', [PosController::class, 'addToCart'])->name('add');
    Route::put('{productId}/update', [PosController::class, 'updateCart'])->name('update');
    Route::delete('{productId}/remove', [PosController::class, 'removeFromCart'])->name('remove');
    Route::post('clear', [PosController::class, 'clearCart'])->name('clear');
    Route::post('checkout', [PosController::class, 'checkout'])->name('checkout');
    Route::get('data', [PosController::class, 'getCartData'])->name('data');
    Route::get('receipt', [PosController::class, 'unifiedReceipt'])->name('receipt');
});

});

// ----------------------
// Login submit per role
// ----------------------
Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password,
        'role' => 'admin',       // force admin here
    ], $request->boolean('remember'))) {

        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));
    }

    return back()->with('error', 'Only admins can login here.');
})->name('admin.login.submit');

Route::post('/shop/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password,
        'role' => 'shop',       // force shop here
    ], $request->boolean('remember'))) {

        $request->session()->regenerate();
        return redirect()->intended(route('shop.dashboard'));
    }

    return back()->with('error', 'Only shop users can login here.');
})->name('shop.login.submit');

// ----------------------
// Shared logout
// ----------------------
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Public Onboarding Routes
Route::prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PublicOnboardingController::class, 'showOnboarding'])->name('show');
    Route::post('/submit', [\App\Http\Controllers\PublicOnboardingController::class, 'submitApplication'])->name('submit');
    Route::get('/{application}/payment', [\App\Http\Controllers\PublicOnboardingController::class, 'showPayment'])->name('payment');
    Route::post('/{application}/process-payment', [\App\Http\Controllers\PublicOnboardingController::class, 'processPayment'])->name('process-payment');
    Route::get('/pesapal-callback', [\App\Http\Controllers\PublicOnboardingController::class, 'pesapalCallback'])->name('pesapal-callback');

     // ADD THIS IPN ROUTE - Pesapal sends POST requests here
    Route::post('/pesapal-ipn', [\App\Http\Controllers\PublicOnboardingController::class, 'pesapalIPN'])->name('pesapal-ipn');
    
    Route::get('/status', [\App\Http\Controllers\PublicOnboardingController::class, 'checkStatus'])->name('status');
    Route::get('/status/{reference}', [\App\Http\Controllers\PublicOnboardingController::class, 'showStatus'])->name('status.show');
});

// ----------------------
// Admin Protected Routes (System-wide management)
// ----------------------
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){

    // Dashboard
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Products Routes (Admin can manage all shops' products)
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class,'index'])->name('index');
        Route::get('/create', [ProductController::class,'create'])->name('create');
        Route::post('/store', [ProductController::class,'store'])->name('store');
        Route::get('/{product}', [ProductController::class,'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class,'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class,'update'])->name('update');
        Route::delete('/{product}', [ProductController::class,'destroy'])->name('destroy');
        Route::post('/sell/{product}', [ProductController::class,'sell'])->name('sell');
        Route::get('/receipt/{product}/{qty}', [ProductController::class,'receipt'])->name('receipt');
    });

    // Reports (System-wide reports)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/pdf', [ReportController::class, 'downloadPDF'])->name('pdf');
    });

    // Shop Management (Admin manages all shops)
    // Put specific routes BEFORE resource routes
Route::prefix('shops')->name('shops.')->group(function () {
    // Onboarding applications routes FIRST
    Route::get('/onboarding-applications', [ShopController::class, 'onboardingApplications'])->name('onboarding-applications');
    Route::get('/onboarding-applications/{application}', [ShopController::class, 'showApplication'])->name('onboarding-application-show');
    Route::post('/onboarding-applications/{application}/approve', [ShopController::class, 'approveApplication'])->name('approve-application');
    Route::post('/onboarding-applications/{application}/reject', [ShopController::class, 'rejectApplication'])->name('reject-application');
    Route::get('/generate-password', [ShopController::class, 'generatePassword'])->name('generate-password');
    
    // Explicit shop routes (FIXED)
        Route::get('/', [ShopController::class, 'index'])->name('index');
        Route::get('/create', [ShopController::class, 'create'])->name('create');
        Route::post('/', [ShopController::class, 'store'])->name('store');
        Route::get('/{shop}', [ShopController::class, 'show'])->name('show');
        Route::get('/{shop}/edit', [ShopController::class, 'edit'])->name('edit');
        Route::put('/{shop}', [ShopController::class, 'update'])->name('update');
        Route::delete('/{shop}', [ShopController::class, 'destroy'])->name('destroy');
    });

    // In your Admin Protected Routes section, add:
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('index');
    Route::get('/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('show');
    Route::post('/{subscription}/approve', [\App\Http\Controllers\Admin\SubscriptionController::class, 'approve'])->name('approve');
    Route::post('/{subscription}/reject', [\App\Http\Controllers\Admin\SubscriptionController::class, 'reject'])->name('reject');
    Route::post('/{subscription}/extend', [\App\Http\Controllers\Admin\SubscriptionController::class, 'extend'])->name('extend');
    Route::post('/{subscription}/deactivate', [\App\Http\Controllers\Admin\SubscriptionController::class, 'deactivate'])->name('deactivate');
    
    // Analytics routes
    Route::get('/analytics/overview', [\App\Http\Controllers\Admin\SubscriptionController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/revenue', [\App\Http\Controllers\Admin\SubscriptionController::class, 'revenueReport'])->name('revenue');
});

});


// ----------------------
// Shop Protected Routes (Shop-specific operations)
// ----------------------
// ----------------------
// Shop Protected Routes (Shop-specific operations)
// ----------------------

// ----------------------
// Shop Protected Routes (Shop-specific operations)
// ----------------------
Route::middleware(['auth','role:shop'])->prefix('shop')->name('shop.')->group(function(){

    // Dashboard (Shop-specific)
    Route::get('/dashboard', [ShopDashboardController::class,'index'])->name('dashboard');
    
    // Loans
    Route::resource('loans', \App\Http\Controllers\Shop\LoanController::class); 
    Route::post('/loans/{loan}/record-payment', [\App\Http\Controllers\Shop\LoanController::class, 'recordPayment'])
        ->name('loans.record-payment');

    // Products routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ShopProductController::class,'index'])->name('index');
        Route::get('/create', [ShopProductController::class,'create'])->name('create');
        Route::post('/', [ShopProductController::class,'store'])->name('store');
        Route::get('/{product}/edit', [ShopProductController::class,'edit'])->name('edit');
        Route::put('/{product}', [ShopProductController::class,'update'])->name('update');
        Route::post('/{product}/update-stock', [ShopProductController::class,'updateStock'])->name('update-stock');
        Route::post('/{product}/sell', [ShopProductController::class,'sell'])->name('sell');
        Route::get('/receipt/{product}/{qty}', [ShopProductController::class,'receipt'])->name('receipt');
        // Add this to your shop routes
Route::get('/history', [ShopProductController::class, 'salesHistory'])
    ->name('sales-history');
    });

    // Cart routes - OUTSIDE products prefix
    Route::post('/cart/{productId}/add', [ShopProductController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/{productId}/update', [ShopProductController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{productId}/remove', [ShopProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [ShopProductController::class, 'clearCart'])->name('cart.clear');
    Route::post('/cart/checkout', [ShopProductController::class, 'checkout'])->name('cart.checkout');
    Route::get('/cart/data', [ShopProductController::class, 'getCartData'])->name('cart.data');

    // Unified receipt route
    Route::get('/receipt/unified', [ShopProductController::class, 'unifiedReceipt'])->name('receipt.unified');

    // Shop Expenses
    Route::resource('expenses', \App\Http\Controllers\Shop\ExpenseController::class);
    Route::get('/expenses/analytics', [\App\Http\Controllers\Shop\ExpenseController::class, 'analytics'])
        ->name('expenses.analytics');
    Route::get('/expenses/analytics/pdf', [\App\Http\Controllers\Shop\ExpenseController::class, 'downloadAnalyticsPDF'])
        ->name('expenses.analytics.pdf');

    // Shop-specific reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Shop\ReportController::class, 'index'])->name('index');
        Route::get('/profit-loss', [\App\Http\Controllers\Shop\ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/balance-sheet', [\App\Http\Controllers\Shop\ReportController::class, 'balanceSheet'])->name('balance-sheet');
    });

    // Depreciation
    Route::prefix('depreciation')->name('depreciation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Shop\DepreciationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Shop\DepreciationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Shop\DepreciationController::class, 'store'])->name('store');
        Route::get('/{depreciation}/edit', [\App\Http\Controllers\Shop\DepreciationController::class, 'edit'])->name('edit');
        Route::put('/{depreciation}', [\App\Http\Controllers\Shop\DepreciationController::class, 'update'])->name('update');
        Route::delete('/{depreciation}', [\App\Http\Controllers\Shop\DepreciationController::class, 'destroy'])->name('destroy');
        Route::get('/financial-analysis', [\App\Http\Controllers\Shop\DepreciationController::class, 'financialAnalysis'])->name('financial-analysis');
        Route::post('/recalculate-values', [\App\Http\Controllers\Shop\DepreciationController::class, 'recalculateValues'])->name('recalculate-values');
    });

    // Subscription Renewal Routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/renew', [\App\Http\Controllers\Shop\SubscriptionController::class, 'showRenewal'])->name('renew');
        Route::post('/renew', [\App\Http\Controllers\Shop\SubscriptionController::class, 'processRenewal'])->name('process-renewal');
        Route::get('/renewal-callback', [\App\Http\Controllers\Shop\SubscriptionController::class, 'renewalCallback'])->name('renewal-callback');
        Route::get('/status', [\App\Http\Controllers\Shop\SubscriptionController::class, 'status'])->name('status');
    });
});

// ----------------------
// Override default login
// ----------------------
Route::get('/login', function () {
    return redirect()->route('shop.login');
})->name('login');