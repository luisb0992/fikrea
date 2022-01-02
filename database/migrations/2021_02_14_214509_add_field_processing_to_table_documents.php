<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldProcessingToTableDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE 
                `documents` 
             ADD 
                    `processing` TINYINT NOT NULL DEFAULT '0' 
                COMMENT 
                    'Vale 1 si el documento está siendo procesado en este momento, 0 en caso contrario' 
             AFTER
                `sent_at`
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
        DB::statement(
            "ALTER TABLE 
                `documents` 
             DROP 
                `processing`
            "
        );
    }
}
