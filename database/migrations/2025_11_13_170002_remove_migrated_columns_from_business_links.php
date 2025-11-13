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
        Schema::table('business_links', function (Blueprint $table) {
            // Remove columns that have been moved to business_data
            $table->dropColumn([
                'business_name',
                'business_type',
                'latitude',
                'longitude',
                'geofence_radius',
                'geofence_enabled'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_links', function (Blueprint $table) {
            // Restore the columns if rolled back
            $table->string('business_name')->after('userid');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('geofence_radius')->default(100);
            $table->boolean('geofence_enabled')->default(false);
        });
    }
};
