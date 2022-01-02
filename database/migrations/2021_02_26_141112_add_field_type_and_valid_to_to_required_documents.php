<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldTypeAndValidToToRequiredDocuments extends Migration
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
                ADD `type` VARCHAR(255) NULL COMMENT 'Tipo mime del archivo',
                ADD `valid_to` DATE NULL COMMENT 'Fecha de validez del documento';"
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
            "ALTER TABLE `required_documents` 
                DROP `type`;"
        );
        
        DB::statement(
            "ALTER TABLE `required_documents` 
                DROP `valid_to`;"
        );
    }
}
