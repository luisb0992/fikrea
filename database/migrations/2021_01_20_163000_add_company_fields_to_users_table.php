<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCompanyFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `users` 
                ADD `company` VARCHAR(255) NULL COMMENT 'Compañía, en el caso de cuenta de empresa' AFTER `country`, 
                ADD `position` VARCHAR(255) NULL COMMENT 'cargo, en el caso de una cuenta de empresa' AFTER `company`;
            "
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
