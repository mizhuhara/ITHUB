<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('institution_type', 30);
            $table->string('plan', 50)->default('1_month'); // 1_month, 1_year
            $table->unsignedBigInteger('amount');
            $table->string('sender_bank', 100);
            $table->string('sender_name', 150);
            $table->string('sender_account_number', 100)->nullable();
            $table->string('payment_proof');
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_payments');
    }
};
