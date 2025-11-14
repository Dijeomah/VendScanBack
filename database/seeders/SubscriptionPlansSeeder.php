<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0.00,
                'max_businesses' => 1,
                'max_tables' => 10,
                'max_servers' => 10,
                'max_items' => 50,
                'has_analytics' => false,
                'has_custom_branding' => false,
                'has_priority_support' => false,
                'has_api_access' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 29.99,
                'max_businesses' => 5,
                'max_tables' => 50,
                'max_servers' => 25,
                'max_items' => null, // unlimited
                'has_analytics' => true,
                'has_custom_branding' => true,
                'has_priority_support' => false,
                'has_api_access' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 99.99,
                'max_businesses' => null, // unlimited
                'max_tables' => null, // unlimited
                'max_servers' => null, // unlimited
                'max_items' => null, // unlimited
                'has_analytics' => true,
                'has_custom_branding' => true,
                'has_priority_support' => true,
                'has_api_access' => true,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            \DB::table('subscription_plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
