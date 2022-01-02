<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeTableFileSharingHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::statement(
                "ALTER TABLE `file_sharing_histories` DROP `latitude`"
            );
        } catch (\Exception $e) {
            // No hacer nada
        }

        try {
            DB::statement(
                "ALTER TABLE `file_sharing_histories` DROP `longitude`"
            );
        } catch (\Exception $e) {
            // No hacer nada
        }

        DB::statement(
            "ALTER TABLE `file_sharing_histories` CHANGE `starts_at` `starts_at` DATETIME NULL;"
        );

        try {
            DB::statement(
                "ALTER TABLE `file_sharing_histories` DROP `ends_at`"
            );
        } catch (\Exception $e) {
            // No hacer nada
        }

        DB::statement(
            "ALTER TABLE `file_sharing_histories` DROP `contact_token`"
        );

        DB::statement(
            "ALTER TABLE `file_sharing_histories` 
                CHANGE `downloaded_at` `downloaded_at` DATETIME NULL DEFAULT NULL;"
        );
        
        DB::statement(
            "ALTER TABLE `file_sharing_histories` ADD `recipient` VARCHAR(255) NULL AFTER `user_agent`;"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
