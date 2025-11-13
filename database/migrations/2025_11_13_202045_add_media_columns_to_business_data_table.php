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
            $table->string('header_image')->nullable()->after('address');
            $table->string('logo_image')->nullable()->after('header_image');
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
            $table->dropColumn(['header_image', 'logo_image']);
        });
    }
};
