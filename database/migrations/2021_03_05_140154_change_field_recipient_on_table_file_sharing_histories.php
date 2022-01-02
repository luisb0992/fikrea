<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeFieldRecipientOnTableFileSharingHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB:: statement(
            "ALTER TABLE `file_sharing_histories` CHANGE `recipient` `file_sharing_contact_id` BIGINT NULL;"
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
