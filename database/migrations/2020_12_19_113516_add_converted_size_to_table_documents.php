<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddConvertedSizeToTableDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `documents` CHANGE `size` `size` BIGINT NOT NULL;"
        );

        DB::statement(
            "ALTER TABLE `documents` ADD `converted_size` BIGINT NOT NULL DEFAULT '0' AFTER `size`;"
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
