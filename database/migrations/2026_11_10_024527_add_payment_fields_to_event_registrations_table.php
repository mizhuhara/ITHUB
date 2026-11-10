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
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unsignedInteger('amount')->default(0)->after('checked_in_at');
            $table->string('payment_status', 30)->default('confirmed')->after('amount');
            $table->string('payment_proof')->nullable()->after('payment_status');
            $table->string('sender_bank', 100)->nullable()->after('payment_proof');
            $table->string('sender_name', 150)->nullable()->after('sender_bank');
            $table->text('payment_notes')->nullable()->after('sender_name');
            $table->timestamp('confirmed_at')->nullable()->after('payment_notes');
            $table->foreignId('confirmed_by')->nullable()->after('confirmed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('confirmed_by');
            $table->foreignId('rejected_by')->nullable()->after('rejected_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'amount',
                'payment_status',
                'payment_proof',
                'sender_bank',
                'sender_name',
                'payment_notes',
                'confirmed_at',
                'confirmed_by',
                'rejected_at',
                'rejected_by',
                'rejection_reason',
            ]);
        });
    }
};
