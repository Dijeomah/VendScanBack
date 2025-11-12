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
        Schema::create('server_table_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('users')->onDelete('cascade'); // Server (user with role='server')
            $table->foreignId('table_id')->constrained('table_link_qr_data')->onDelete('cascade'); // Restaurant table
            $table->foreignId('business_link_id')->constrained('business_links')->onDelete('cascade'); // Business context
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Composite unique: prevent duplicate assignments
            $table->unique(['server_id', 'table_id'], 'unique_server_table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_table_assignments');
    }
};
