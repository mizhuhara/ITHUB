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
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_status', 30)->default('trial')->after('status');
            $table->string('membership_plan', 50)->nullable()->default('trial_1_month')->after('membership_status');
            $table->timestamp('membership_started_at')->nullable()->after('membership_plan');
            $table->timestamp('membership_expires_at')->nullable()->after('membership_started_at');
            $table->boolean('is_premium_active')->default(true)->after('membership_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'membership_status',
                'membership_plan',
                'membership_started_at',
                'membership_expires_at',
                'is_premium_active',
            ]);
        });
    }
};
