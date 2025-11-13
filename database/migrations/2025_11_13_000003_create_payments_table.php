<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'mobile', 'simulated'])->default('simulated');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('transaction_reference')->nullable()->unique();
            $table->text('payment_details')->nullable(); // JSON field for additional payment info
            $table->timestamp('payment_at')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
