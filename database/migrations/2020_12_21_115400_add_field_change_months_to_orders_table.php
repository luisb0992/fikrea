<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldChangeMonthsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `orders` ADD `change_months` INT NOT NULL DEFAULT '0' AFTER `months`;"
        );

        DB::statement(
            "ALTER TABLE `orders` ADD `aditional_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `tax`;"
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
