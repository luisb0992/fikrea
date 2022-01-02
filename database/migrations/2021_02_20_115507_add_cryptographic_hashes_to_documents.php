<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCryptographicHashesToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `documents` ADD `original_md5` VARCHAR(32) NULL AFTER `original_path`"
        );

        DB::statement(
            "ALTER TABLE `documents` ADD `original_sha1` VARCHAR(40) NULL AFTER `original_md5`"
        );

        DB::statement(
            "ALTER TABLE `documents` ADD `signed_md5` VARCHAR(32) NULL AFTER `signed_path`"
        );

        DB::statement(
            "ALTER TABLE `documents` ADD `signed_sha1` VARCHAR(40) NULL AFTER `signed_md5`"
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
