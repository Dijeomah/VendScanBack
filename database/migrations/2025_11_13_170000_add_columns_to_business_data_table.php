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
        Schema::table('business_data', function (Blueprint $table) {
            $table->unsignedBigInteger('business_link_id')->after('id');
            $table->foreign('business_link_id')->references('id')->on('business_links')->onDelete('cascade');

            $table->string('business_name')->after('business_link_id');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('phone_number')->nullable()->after('business_type');
            $table->text('address')->nullable()->after('phone_number');

            // Geofencing columns
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('geofence_radius')->default(100)->comment('Radius in meters')->after('longitude');
            $table->boolean('geofence_enabled')->default(false)->after('geofence_radius');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_data', function (Blueprint $table) {
            $table->dropForeign(['business_link_id']);
            $table->dropColumn([
                'business_link_id',
                'business_name',
                'business_type',
                'phone_number',
                'address',
                'latitude',
                'longitude',
                'geofence_radius',
                'geofence_enabled'
            ]);
        });
    }
};
