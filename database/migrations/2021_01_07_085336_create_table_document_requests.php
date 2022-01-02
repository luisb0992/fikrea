<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableDocumentRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `document_requests` 
                ( 
                    `id` BIGINT NOT NULL AUTO_INCREMENT, 
                    `user_id` BIGINT NOT NULL,
                    `comment` TEXT NULL, 
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
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
        Schema::dropIfExists('document_requests');
    }
}
