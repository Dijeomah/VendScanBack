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
        Schema::table('table_link_qr_data', function (Blueprint $table) {
            $table->foreignId('business_link_id')->after('id')->constrained('business_links')->onDelete('cascade');
            $table->string('table_number')->after('business_link_id'); // e.g., "Table 1", "T-05", etc.
            $table->string('table_name')->nullable()->after('table_number'); // Optional custom name
            $table->integer('seats')->default(4)->after('table_name'); // Number of seats
            $table->text('table_qr_code')->nullable()->after('seats'); // QR code for this table
            $table->string('qr_code_url')->nullable()->after('table_qr_code'); // Public URL with table context
            $table->enum('status', ['active', 'inactive', 'occupied', 'reserved'])->default('active')->after('qr_code_url');
            $table->text('notes')->nullable()->after('status'); // Any notes about the table

            // Add unique constraint: one table number per business
            $table->unique(['business_link_id', 'table_number'], 'unique_table_per_business');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_link_qr_data', function (Blueprint $table) {
            $table->dropForeign(['business_link_id']);
            $table->dropUnique('unique_table_per_business');
            $table->dropColumn([
                'business_link_id',
                'table_number',
                'table_name',
                'seats',
                'table_qr_code',
                'qr_code_url',
                'status',
                'notes'
            ]);
        });
    }
};
