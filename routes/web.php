<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Shop\DashboardController as ShopDashboardController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;

// Home
Route::get('/', fn () => view('welcome'));

// ----------------------
// Login views per role
// ----------------------
Route::get('/admin/login', fn () => view('admin.auth.login'))->name('admin.login');
Route::get('/shop/login', fn () => view('shop.auth.login'))->name('shop.login');

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
    Route::resource('shops', \App\Http\Controllers\Admin\ShopController::class);
});

// ----------------------
// Shop Protected Routes (Shop-specific operations)
// ----------------------
Route::middleware(['auth','role:shop'])->prefix('shop')->name('shop.')->group(function(){

    // Dashboard (Shop-specific)
    Route::get('/dashboard', [ShopDashboardController::class,'index'])->name('dashboard');

    // Products Routes (Only for their shop)
   Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ShopProductController::class,'index'])->name('index');
    Route::get('/create', [ShopProductController::class,'create'])->name('create'); // ADD THIS
    Route::post('/store', [ShopProductController::class,'store'])->name('store');   // ADD THIS
    Route::post('/sell/{product}', [ShopProductController::class,'sell'])->name('sell');
    Route::get('/receipt/{product}/{qty}', [ShopProductController::class,'receipt'])->name('receipt');
});

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
});

// ----------------------
// Override default login
// ----------------------
Route::get('/login', function () {
    return redirect()->route('shop.login');
})->name('login');