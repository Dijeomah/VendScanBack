<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Pro, Enterprise
            $table->string('slug')->unique(); // free, pro, enterprise
            $table->decimal('price', 10, 2)->default(0); // Monthly price
            $table->integer('max_businesses')->nullable(); // null = unlimited
            $table->integer('max_tables')->nullable(); // null = unlimited
            $table->integer('max_servers')->nullable(); // null = unlimited
            $table->integer('max_items')->nullable(); // null = unlimited
            $table->boolean('has_analytics')->default(false);
            $table->boolean('has_custom_branding')->default(false);
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('subscription_plan_id');
            $table->index('status');
        });

        // Add subscription_plan_id to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'subscription_plan_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('subscription_plan_id')->nullable()->after('role')->constrained('subscription_plans')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'subscription_plan_id')) {
                $table->dropForeign(['subscription_plan_id']);
                $table->dropColumn('subscription_plan_id');
            }
        });

        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
