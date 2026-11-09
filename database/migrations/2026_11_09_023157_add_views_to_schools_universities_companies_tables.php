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
        if (Schema::hasTable('schools') && ! Schema::hasColumn('schools', 'views')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0);
            });
        }

        if (Schema::hasTable('universities') && ! Schema::hasColumn('universities', 'views')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0);
            });
        }

        if (Schema::hasTable('companies') && ! Schema::hasColumn('companies', 'views')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('schools') && Schema::hasColumn('schools', 'views')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasTable('universities') && Schema::hasColumn('universities', 'views')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'views')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }
    }
};
