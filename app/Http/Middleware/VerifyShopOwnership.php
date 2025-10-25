<?php
// app/Http/Middleware/VerifyShopOwnership.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyShopOwnership
{
    public function handle(Request $request, Closure $next)
    {
        // If user is admin, allow access to everything
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // For shop users, verify they only access their own shop data
        if (Auth::check() && Auth::user()->role === 'shop') {
            $userShopId = Auth::user()->shop_id;
            
            // Check product access
            if ($request->route('product')) {
                $product = $request->route('product');
                if ($product->shop_id != $userShopId) {
                    abort(403, 'Unauthorized access to product.');
                }
            }
            
            // Check expense access
            if ($request->route('expense')) {
                $expense = $request->route('expense');
                if ($expense->shop_id != $userShopId) {
                    abort(403, 'Unauthorized access to expense.');
                }
            }
        }

        return $next($request);
    }
}