<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update blast_histories table - add cascade delete for campaign_id
        $this->dropForeignKeyIfExists('blast_histories', 'campaign_id');
        DB::statement('ALTER TABLE `blast_histories` ADD CONSTRAINT `blast_histories_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE');

        // Update blast_schedules table - add cascade delete for campaign_id
        $this->dropForeignKeyIfExists('blast_schedules', 'campaign_id');
        DB::statement('ALTER TABLE `blast_schedules` ADD CONSTRAINT `blast_schedules_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert blast_histories table
        $this->dropForeignKeyIfExists('blast_histories', 'campaign_id');
        DB::statement('ALTER TABLE `blast_histories` ADD CONSTRAINT `blast_histories_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`)');

        // Revert blast_schedules table
        $this->dropForeignKeyIfExists('blast_schedules', 'campaign_id');
        DB::statement('ALTER TABLE `blast_schedules` ADD CONSTRAINT `blast_schedules_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`)');
    }

    /**
     * Helper method to drop foreign key by column name
     */
    private function dropForeignKeyIfExists($table, $column)
    {
        // Query to find the actual foreign key constraint name
        $results = DB::select(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$table, $column]
        );

        if (!empty($results)) {
            $constraintName = $results[0]->CONSTRAINT_NAME;
            try {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraintName}`");
            } catch (\Exception $e) {
                // Constraint might already be dropped, continue
                \Log::warning("Could not drop constraint {$constraintName}: " . $e->getMessage());
            }
        }
    }
};
