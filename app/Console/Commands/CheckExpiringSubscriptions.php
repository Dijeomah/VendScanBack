<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Mail\SubscriptionExpiringMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring subscriptions and send reminder emails';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for expiring subscriptions...');

        // Check for subscriptions expiring in 7, 3, and 1 days
        $reminderDays = [7, 3, 1];
        $totalNotifications = 0;

        foreach ($reminderDays as $days) {
            $targetDate = now()->addDays($days)->startOfDay();
            $nextDay = now()->addDays($days)->endOfDay();

            $expiringSubscriptions = Subscription::where('status', 'active')
                ->whereNotNull('expires_at')
                ->whereBetween('expires_at', [$targetDate, $nextDay])
                ->with(['user', 'plan'])
                ->get();

            $this->line("Found {$expiringSubscriptions->count()} subscriptions expiring in {$days} days");

            foreach ($expiringSubscriptions as $subscription) {
                try {
                    Mail::to($subscription->user->email)
                        ->send(new SubscriptionExpiringMail($subscription->user, $subscription));

                    $this->info("✓ Sent reminder to {$subscription->user->email} ({$days} days until expiry)");
                    $totalNotifications++;

                    Log::info('Expiry reminder sent', [
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'days_until_expiry' => $days,
                        'expires_at' => $subscription->expires_at,
                    ]);

                } catch (\Exception $e) {
                    $this->error("✗ Failed to send reminder to {$subscription->user->email}: " . $e->getMessage());

                    Log::error('Failed to send expiry reminder', [
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Also mark expired subscriptions
        $expiredCount = $this->markExpiredSubscriptions();

        $this->newLine();
        $this->info("✓ Sent {$totalNotifications} expiry reminder(s)");
        $this->info("✓ Marked {$expiredCount} subscription(s) as expired");
        $this->info('Done!');

        return Command::SUCCESS;
    }

    /**
     * Mark expired subscriptions as expired
     *
     * @return int
     */
    protected function markExpiredSubscriptions(): int
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->markAsExpired();

            // Downgrade user to free plan
            $freePlan = \App\Models\SubscriptionPlan::where('slug', 'free')->first();
            if ($freePlan) {
                $subscription->user->update([
                    'subscription_plan_id' => $freePlan->id,
                ]);

                // Create new free subscription
                Subscription::create([
                    'user_id' => $subscription->user_id,
                    'subscription_plan_id' => $freePlan->id,
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => null,
                ]);
            }

            Log::info('Subscription expired and downgraded', [
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
            ]);

            $count++;
        }

        return $count;
    }
}
