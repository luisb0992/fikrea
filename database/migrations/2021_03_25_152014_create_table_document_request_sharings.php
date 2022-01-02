<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableDocumentRequestSharings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `document_request_sharings` 
                ( 
                    `id` BIGINT NOT NULL AUTO_INCREMENT, 
                    `document_request_id` BIGINT NOT NULL,
                    `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `signers` JSON NULL, 
                    `type` TINYINT NOT NULL DEFAULT '0' COMMENT '0 = Envío automático, 1 = Envío manual ',
                     PRIMARY KEY (`id`)
                )
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
        Schema::dropIfExists('document_request_sharings');
    }
}
