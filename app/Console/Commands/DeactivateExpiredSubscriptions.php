<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:deactivate-expired';

    protected $description = 'Deactivate subscriptions that have passed their expiry date';

    public function handle()
    {
        $expired = Subscription::where('is_active', true)
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        $count = 0;

        foreach ($expired as $subscription) {
            $subscription->update(['is_active' => false]);

            // Update the shop status if this was their only active subscription
            $shop = $subscription->shop;
            if ($shop) {
                $hasOtherActive = Subscription::where('shop_id', $shop->id)
                    ->where('id', '!=', $subscription->id)
                    ->where('is_active', true)
                    ->where('expires_at', '>', Carbon::now())
                    ->exists();

                if (!$hasOtherActive) {
                    $shop->update(['subscription_status' => 'expired']);
                }
            }

            $count++;
        }

        $this->info("Deactivated {$count} expired subscription(s).");

        return 0;
    }
}
