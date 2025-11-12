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
        Schema::table('users', function (Blueprint $table) {
            // Add subscription tier system
            $table->enum('subscription_tier', ['free', 'pro', 'max'])->default('free')->after('role');
            $table->integer('business_limit')->default(3)->after('subscription_tier');

            // Update role enum to include 'server'
            // Note: Laravel doesn't support altering enums directly, so we'll handle this separately if needed
        });

        // Update role enum to include 'server' - using raw SQL for MySQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'vendor', 'user', 'server') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_tier', 'business_limit']);
        });

        // Revert role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'vendor', 'user') DEFAULT 'user'");
    }
};
