<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldsCommentAndMaxSizeToRequiredDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `required_documents` ADD `comment` TEXT NULL AFTER `name`"
        );

        DB::statement(
            "ALTER TABLE `required_documents` ADD `maxsize` BIGINT NULL AFTER `valid_to`"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            "ALTER TABLE `required_documents` DROP `comment`"
        );

        DB::statement(
            "ALTER TABLE `required_documents` DROP `maxsize`"
        );
    }
}
