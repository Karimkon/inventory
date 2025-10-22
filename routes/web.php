<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;

// Home
Route::get('/', fn () => view('welcome'));

// Admin Login View
Route::get('/admin/login', fn () => view('admin.auth.login'))->name('admin.login');

// Admin Login Submit
Route::post('/admin/login', function(Request $request){
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    if(Auth::attempt($credentials, $request->boolean('remember')) && Auth::user()->email === 'sngeneralhardware@gmail.com'){
        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));
    }

    Auth::logout();
    return redirect()->route('admin.login')->with('error','Only admin can login here.');
})->name('admin.login.submit');

// ----------------------
// Shared logout
// ----------------------
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Admin Protected Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function(){

    // Dashboard
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Products Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class,'index'])->name('index');           // List all products
    Route::get('/create', [ProductController::class,'create'])->name('create');   // Show create form
    Route::post('/store', [ProductController::class,'store'])->name('store');     // Store product
    Route::get('/{product}', [ProductController::class,'show'])->name('show');    // View product
    Route::get('/{product}/edit', [ProductController::class,'edit'])->name('edit');// Edit product
    Route::put('/{product}', [ProductController::class,'update'])->name('update');// Update product
    Route::delete('/{product}', [ProductController::class,'destroy'])->name('destroy');// Delete product
    Route::post('/sell/{product}', [ProductController::class,'sell'])->name('sell');// Sell product
    Route::get('/receipt/{product}/{qty}', [ProductController::class,'receipt'])->name('receipt');

});



Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index'); // HTML view
    Route::get('/pdf', [ReportController::class, 'downloadPDF'])->name('pdf'); // PDF download
});



});
