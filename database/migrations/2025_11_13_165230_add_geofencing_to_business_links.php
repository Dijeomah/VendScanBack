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
            $table->decimal('latitude', 10, 8)->nullable()->after('business_qr');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('geofence_radius')->default(100)->comment('Radius in meters'); // Default 100 meters
            $table->boolean('geofence_enabled')->default(false);
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
            $table->dropColumn(['latitude', 'longitude', 'geofence_radius', 'geofence_enabled']);
        });
    }
};
