<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('business_link_id')->constrained('business_links')->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained('table_link_qr_data')->onDelete('set null');
            $table->foreignId('server_id')->nullable()->constrained('users')->onDelete('set null'); // Server who served
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade'); // Business owner
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2');
            $table->text('notes')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('payment_method', ['pay_before', 'pay_after'])->default('pay_after');
            $table->timestamp('payment_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'served', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            
            $table->index(['business_link_id', 'created_at']);
            $table->index(['vendor_id', 'created_at']);
            $table->index(['server_id', 'created_at']);
            $table->index('payment_status');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
