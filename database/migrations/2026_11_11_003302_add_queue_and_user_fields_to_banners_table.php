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
        Schema::table('banners', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('status', 30)->default('active')->after('sort_order');
            $table->unsignedInteger('queue_position')->nullable()->after('status');
            $table->timestamp('started_at')->nullable()->after('queue_position');
            $table->timestamp('expires_at')->nullable()->after('started_at');
            $table->text('admin_notes')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'status',
                'queue_position',
                'started_at',
                'expires_at',
                'admin_notes',
            ]);
        });
    }
};
