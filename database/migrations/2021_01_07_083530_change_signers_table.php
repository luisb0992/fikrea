<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeSignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `signers` CHANGE `document_id` `document_id` BIGINT NULL;"
        );

        DB::statement(
            "ALTER TABLE `signers` ADD `document_request_id` BIGINT NULL AFTER `document_id`;"
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
