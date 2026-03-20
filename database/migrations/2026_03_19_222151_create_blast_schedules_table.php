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
        Schema::create('blast_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained();
            $table->enum('message_type', ['join_reminder', 'draft_reminder', 'submit_reminder', 'accepted_reminder']);
            $table->longText('message_content');
            $table->enum('frequency', ['hourly', 'daily', 'weekly', 'monthly', 'once'])->default('once');
            $table->string('schedule_time')->nullable(); // Format HH:MM
            $table->string('schedule_day')->nullable(); // For weekly: MON, TUE, etc; For monthly: 1-31
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blast_schedules');
    }
};
