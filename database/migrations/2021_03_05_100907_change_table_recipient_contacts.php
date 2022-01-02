<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeTableRecipientContacts extends Migration
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
                "RENAME TABLE recipient_contacts TO file_sharing_contacts"
            );
        } catch (\Exception $e) {
            //
        }

        DB::statement(
            "ALTER TABLE `file_sharing_contacts` ADD `file_sharing_id` BIGINT NOT NULL AFTER `id`;"
        );

        DB::statement(
            "ALTER TABLE `file_sharing_contacts` DROP `history_type`"
        );

        DB::statement(
            "ALTER TABLE `file_sharing_contacts` DROP `history_id`"
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
