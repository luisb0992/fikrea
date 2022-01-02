<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldTypeToDocumentSharings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `document_sharings` ADD `type` 
                TINYINT NOT NULL DEFAULT '0' COMMENT '0 = Envío automático, 1 = Envío manual' AFTER `signers`; "
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
