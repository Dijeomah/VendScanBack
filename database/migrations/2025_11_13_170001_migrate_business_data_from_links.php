<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migrate existing business data from business_links to business_data
        $businessLinks = DB::table('business_links')->get();

        foreach ($businessLinks as $business) {
            DB::table('business_data')->insert([
                'business_link_id' => $business->id,
                'business_name' => $business->business_name,
                'business_type' => $business->business_type ?? null,
                'phone_number' => $business->phone_number ?? null,
                'address' => $business->address ?? null,
                'latitude' => $business->latitude ?? null,
                'longitude' => $business->longitude ?? null,
                'geofence_radius' => $business->geofence_radius ?? 100,
                'geofence_enabled' => $business->geofence_enabled ?? false,
                'created_at' => $business->created_at,
                'updated_at' => $business->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete migrated data
        DB::table('business_data')->truncate();
    }
};
