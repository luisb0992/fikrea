<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddTableDocumentRequestFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `document_request_files`
            ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT ,
                `user_id` BIGINT NOT NULL , 
                `document_request_id` BIGINT NOT NULL , 
                `required_document_id` BIGINT NOT NULL ,
                `signer_id` BIGINT NOT NULL , 
                `name` TEXT NOT NULL , 
                `path` TEXT NOT NULL , 
                `type` TEXT NOT NULL , 
                `size` BIGINT NOT NULL , 
                `ip` VARCHAR(64) NULL , 
                `user_agent` TEXT NULL , 
                `latitude` DOUBLE NULL , 
                `longitude` DOUBLE NULL , 
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
                `updated_at` DATETIME NOT NULL, 
                PRIMARY KEY (`id`))
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
