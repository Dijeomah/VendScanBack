<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SubscriptionPlan;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the free plan (admin doesn't need subscription, but for consistency)
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@vendscan.com'], // Check by email
            [
                'userid' => 'ADMIN001',
                'first_name' => 'VendScan',
                'last_name' => 'Administrator',
                'phone_number' => '+2348000000000',
                'password' => Hash::make('Admin@123456'), // CHANGE THIS IN PRODUCTION
                'role' => 'admin',
                'subscription_plan_id' => $freePlan ? $freePlan->id : null,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->line('Email: admin@vendscan.com');
        $this->command->line('Password: Admin@123456');
        $this->command->warn('⚠️  IMPORTANT: Please change the admin password after first login!');
    }
}
