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
        Schema::create('payment_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who reported
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who resolved
            $table->enum('status', ['pending', 'investigating', 'resolved', 'rejected'])->default('pending');
            $table->enum('dispute_type', ['non_receipt', 'duplicate', 'unauthorized', 'amount_mismatch', 'refund_request', 'other'])->default('other');
            $table->text('reason');
            $table->text('resolution_notes')->nullable();
            $table->decimal('disputed_amount', 10, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional dispute details
            $table->timestamps();
        });

        // Add reconciliation fields to subscription_payments table
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('status');
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->onDelete('set null')->after('is_reconciled');
            $table->timestamp('reconciled_at')->nullable()->after('reconciled_by');
            $table->text('reconciliation_notes')->nullable()->after('reconciled_at');
            $table->boolean('has_dispute')->default(false)->after('reconciliation_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove reconciliation fields from subscription_payments table
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropColumn(['is_reconciled', 'reconciled_by', 'reconciled_at', 'reconciliation_notes', 'has_dispute']);
        });

        Schema::dropIfExists('payment_disputes');
    }
};
