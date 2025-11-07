<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Claims Processing (FR-32)
     */
    public function up(): void
    {
        Schema::create('support_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('courier_info_id')->constrained('courier_infos')->onDelete('cascade');
            $table->foreignId('support_issue_id')->nullable()->constrained('support_issues')->onDelete('set null');

            $table->string('claim_type'); // damage, loss, delay_compensation
            $table->decimal('claimed_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->text('claim_details');

            $table->tinyInteger('status')->default(0); // 0=Pending, 1=Under Review, 2=Approved, 3=Rejected, 4=Paid
            $table->text('rejection_reason')->nullable();

            $table->json('evidence')->nullable(); // Photos, invoices, etc.

            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('processing_days')->nullable(); // Track SLA (10 business days)

            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('claim_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_claims');
    }
};
