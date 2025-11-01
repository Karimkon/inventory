<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Shop;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $subscriptions = Subscription::with('shop')
            ->when($status !== 'all', function($query) use ($status) {
                if ($status === 'active') {
                    return $query->where('is_active', true)
                                ->where('expires_at', '>', Carbon::now());
                } elseif ($status === 'pending_approval') {
                    return $query->where('payment_status', 'paid')
                                ->where('is_active', false);
                } elseif ($status === 'expired') {
                    return $query->where('expires_at', '<=', Carbon::now());
                } elseif ($status === 'pending_payment') {
                    return $query->where('payment_status', 'pending');
                }
                return $query->where('payment_status', $status);
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('is_active', true)
                        ->where('expires_at', '>', Carbon::now())->count(),
            'pending_approval' => Subscription::where('payment_status', 'paid')
                                ->where('is_active', false)->count(),
            'pending_payment' => Subscription::where('payment_status', 'pending')->count(),
            'expired' => Subscription::where('expires_at', '<=', Carbon::now())->count(),
        ];

        // Calculate total revenue
        $totalRevenue = Subscription::where('payment_status', 'paid')->sum('total_amount');

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'status', 'totalRevenue'));
    }

    /**
     * Display subscription analytics
     */
    public function analytics()
    {
        // Revenue by month for the last 6 months
        $revenueByMonth = Subscription::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Subscriptions by plan type
        $subscriptionsByPlan = Subscription::selectRaw('plan_type, COUNT(*) as count')
            ->groupBy('plan_type')
            ->get();

        // Recent payments
        $recentPayments = Subscription::with('shop')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        // Total statistics
        $totalStats = [
            'total_revenue' => Subscription::where('payment_status', 'paid')->sum('total_amount'),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('is_active', true)
                                ->where('expires_at', '>', Carbon::now())->count(),
            'pending_approval' => Subscription::where('payment_status', 'paid')
                                ->where('is_active', false)->count(),
        ];

        return view('admin.subscriptions.analytics', compact(
            'revenueByMonth', 
            'subscriptionsByPlan', 
            'recentPayments',
            'totalStats'
        ));
    }

    /**
     * Display revenue report
     */
    public function revenueReport()
    {
        $revenueData = Subscription::where('payment_status', 'paid')
            ->selectRaw('plan_type, SUM(total_amount) as revenue, COUNT(*) as count')
            ->groupBy('plan_type')
            ->get();

        $monthlyRevenue = Subscription::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.subscriptions.revenue', compact('revenueData', 'monthlyRevenue'));
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load('shop.users');
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Approve and activate a subscription.
     */
    public function approve(Subscription $subscription)
    {
        if ($subscription->payment_status !== 'paid') {
            return back()->with('error', 'Cannot approve subscription with unpaid payment.');
        }

        if ($subscription->is_active) {
            return back()->with('info', 'Subscription is already active.');
        }

        \DB::transaction(function() use ($subscription) {
            // Deactivate any other active subscriptions for this shop
            Subscription::where('shop_id', $subscription->shop_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Activate this subscription
            $subscription->update([
                'is_active' => true,
                'activated_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonth(),
            ]);

            // Update shop status
            $subscription->shop->update([
                'subscription_status' => 'active',
                'activated_at' => Carbon::now(),
            ]);
        });

        return back()->with('success', 'Subscription activated successfully! Shop can now access all features.');
    }

    /**
     * Reject a subscription.
     */
    public function reject(Request $request, Subscription $subscription)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $subscription->update([
            'is_active' => false,
            'admin_notes' => $request->admin_notes,
        ]);

        $subscription->shop->update([
            'subscription_status' => 'rejected'
        ]);

        return back()->with('success', 'Subscription rejected. Shop owner has been notified.');
    }

    /**
     * Extend subscription expiry.
     */
   public function extend(Request $request, Subscription $subscription)
{
    $request->validate([
        'extension_days' => 'required|integer|min:1|max:365'
    ]);

    // Convert to integer to ensure it's not a string
    $extensionDays = (int) $request->extension_days;
    
    $newExpiry = Carbon::parse($subscription->expires_at)->addDays($extensionDays);
    
    $subscription->update([
        'expires_at' => $newExpiry,
        'admin_notes' => $subscription->admin_notes . "\nExtended by {$extensionDays} days on " . Carbon::now()->format('Y-m-d'),
    ]);

    return back()->with('success', "Subscription extended by {$extensionDays} days. New expiry: {$newExpiry->format('M d, Y')}");
}
    /**
     * Deactivate subscription.
     */
    public function deactivate(Subscription $subscription)
    {
        $subscription->update([
            'is_active' => false,
        ]);

        $subscription->shop->update([
            'subscription_status' => 'inactive'
        ]);

        return back()->with('success', 'Subscription deactivated. Shop access has been restricted.');
    }
}