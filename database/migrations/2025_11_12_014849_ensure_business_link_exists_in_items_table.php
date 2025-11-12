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
        // Check if business_link column exists, if not, add it
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'business_link')) {
                $table->string('business_link')->after('sub_category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'business_link')) {
                $table->dropColumn('business_link');
            }
        });
    }
};
