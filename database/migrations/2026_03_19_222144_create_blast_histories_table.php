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
        Schema::create('blast_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained();
            $table->enum('message_type', ['join_reminder', 'draft_reminder', 'submit_reminder', 'accepted_reminder']);
            $table->longText('message_content');
            $table->integer('total_affiliate');
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['pending', 'sending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blast_histories');
    }
};
