<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Console\Command;

class AssignFreePlanToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:free-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign free subscription plan to all existing vendor users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to assign free plan to existing vendor users...');

        // Get the free plan
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        if (!$freePlan) {
            $this->error('Free plan not found! Please run the SubscriptionPlansSeeder first.');
            return Command::FAILURE;
        }

        // Get all vendor users without a subscription plan
        $vendorsWithoutPlan = User::where('role', 'vendor')
            ->whereNull('subscription_plan_id')
            ->get();

        $this->info("Found {$vendorsWithoutPlan->count()} vendors without a subscription plan.");

        $bar = $this->output->createProgressBar($vendorsWithoutPlan->count());
        $bar->start();

        $assignedCount = 0;

        foreach ($vendorsWithoutPlan as $vendor) {
            try {
                // Assign free plan to user
                $vendor->update([
                    'subscription_plan_id' => $freePlan->id
                ]);

                // Create active subscription record
                Subscription::create([
                    'user_id' => $vendor->id,
                    'subscription_plan_id' => $freePlan->id,
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => null, // Free plan never expires
                ]);

                $assignedCount++;
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to assign plan to user {$vendor->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully assigned free plan to {$assignedCount} vendor users!");

        return Command::SUCCESS;
    }
}
