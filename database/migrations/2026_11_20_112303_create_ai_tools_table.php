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
        Schema::create('ai_tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('tagline')->nullable();
            $table->string('pricing')->nullable();
            $table->string('url')->nullable();
            $table->string('icon_type')->default('code');
            $table->string('icon_bg')->nullable();
            $table->text('description')->nullable();
            $table->longText('overview')->nullable();
            $table->text('what_it_does')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('use_cases')->nullable();
            $table->text('target_audience')->nullable();
            $table->string('action_type')->default('detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tools');
    }
};
