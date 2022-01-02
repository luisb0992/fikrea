<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddFieldValiityUnitToRequiredDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `required_documents` 
                ADD `validity_unit` INT NOT NULL DEFAULT '1' 
                COMMENT 'Unidad del periodo de validación: 1 = días, 30 = meses, 365 = años' AFTER `validity`; "
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
