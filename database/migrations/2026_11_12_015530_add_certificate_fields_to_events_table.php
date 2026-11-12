<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('certificate_enabled')->default(false)->after('is_verified');
            $table->string('certificate_template')->nullable()->after('certificate_enabled');
            $table->decimal('certificate_name_x', 5, 2)->default(50)->after('certificate_template');
            $table->decimal('certificate_name_y', 5, 2)->default(50)->after('certificate_name_x');
            $table->unsignedSmallInteger('certificate_name_font_size')->default(48)->after('certificate_name_y');
            $table->string('certificate_name_color', 20)->default('#111827')->after('certificate_name_font_size');
            $table->timestamp('completed_at')->nullable()->after('certificate_name_color');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_enabled',
                'certificate_template',
                'certificate_name_x',
                'certificate_name_y',
                'certificate_name_font_size',
                'certificate_name_color',
                'completed_at',
            ]);
        });
    }
};
