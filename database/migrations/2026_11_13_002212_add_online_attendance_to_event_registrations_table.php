<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('meeting_token', 64)->nullable()->after('ticket_code');
            $table->timestamp('joined_at')->nullable()->after('meeting_token');
        });

        DB::table('event_registrations')
            ->whereNull('meeting_token')
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $id) {
                DB::table('event_registrations')->where('id', $id)->update([
                    'meeting_token' => 'MTG-'.Str::upper(Str::random(40)),
                ]);
            });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unique('meeting_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique(['meeting_token']);
            $table->dropColumn(['meeting_token', 'joined_at']);
        });
    }
};
