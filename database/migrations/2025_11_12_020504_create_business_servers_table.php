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
        Schema::create('business_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_link_id')->constrained('business_links')->onDelete('cascade');
            $table->foreignId('server_id')->constrained('users')->onDelete('cascade'); // server is a user with role='server'
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade'); // vendor who created this server
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Unique constraint: a server can only be assigned once per business
            $table->unique(['business_link_id', 'server_id'], 'unique_server_per_business');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_servers');
    }
};
