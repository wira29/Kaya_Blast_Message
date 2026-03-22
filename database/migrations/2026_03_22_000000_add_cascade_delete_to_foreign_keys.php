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
        // Update campaigns table - add cascade delete for brand_id
        $this->dropForeignKeyIfExists('campaigns', 'brand_id');
        DB::statement('ALTER TABLE `campaigns` ADD CONSTRAINT `campaigns_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE');

        // Update affiliates table - add cascade delete for campaign_id
        $this->dropForeignKeyIfExists('affiliates', 'campaign_id');
        DB::statement('ALTER TABLE `affiliates` ADD CONSTRAINT `affiliates_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert campaigns table
        $this->dropForeignKeyIfExists('campaigns', 'brand_id');
        DB::statement('ALTER TABLE `campaigns` ADD CONSTRAINT `campaigns_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`)');

        // Revert affiliates table
        $this->dropForeignKeyIfExists('affiliates', 'campaign_id');
        DB::statement('ALTER TABLE `affiliates` ADD CONSTRAINT `affiliates_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`)');
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
