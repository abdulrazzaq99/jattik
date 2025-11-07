<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Customer Support Issues (FR-28, FR-31, FR-32)
     */
    public function up(): void
    {
        Schema::create('support_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('courier_info_id')->nullable()->constrained('courier_infos')->onDelete('cascade');

            $table->string('issue_type'); // wrong_parcel, damaged, missing, delay, other
            $table->string('subject');
            $table->text('description');
            $table->string('priority')->default('medium'); // low, medium, high, urgent

            $table->tinyInteger('status')->default(0); // 0=Open, 1=In Progress, 2=Resolved, 3=Closed

            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Staff/Manager
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Attachments
            $table->json('attachments')->nullable(); // Photos of damaged items, etc.

            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('issue_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_issues');
    }
};
