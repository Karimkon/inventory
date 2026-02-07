<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Only check for shop users
        if (!$user || $user->role !== 'shop') {
            return $next($request);
        }

        $shop = $user->shop;

        if (!$shop) {
            return $next($request);
        }

        // Load active subscription
        $subscription = $shop->activeSubscription;

        // Check if subscription is expired
        $isExpired = false;

        if (!$subscription) {
            // No active subscription at all
            $isExpired = true;
        } elseif ($subscription->expires_at && $subscription->expires_at->isPast()) {
            // Subscription exists but has expired — deactivate it now
            $subscription->update(['is_active' => false]);
            $shop->update(['subscription_status' => 'expired']);
            $isExpired = true;
        }

        if ($isExpired) {
            // Allow access to subscription pages so the user can renew
            $allowedRoutes = [
                'shop.subscription.renew',
                'shop.subscription.process-renewal',
                'shop.subscription.renewal-callback',
                'shop.subscription.status',
                'logout',
            ];

            if (in_array($request->route()?->getName(), $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('shop.subscription.status')
                ->with('error', 'Your subscription has expired. Please renew to continue using the system.');
        }

        return $next($request);
    }
}
