<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableRequiredDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `required_documents` 
            ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `document_request_id` BIGINT NOT NULL COMMENT 'El id de la solicitud',
                `name` VARCHAR(255) NOT NULL COMMENT 'El nombre del documento',
                PRIMARY KEY (`id`)
            )"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_documents');
    }
}
