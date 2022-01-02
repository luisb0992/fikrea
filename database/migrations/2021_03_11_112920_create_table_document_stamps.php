<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableDocumentStamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `document_stamps` 
            ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT, 
                `document_id` BIGINT NOT NULL, 
                `stamp` MEDIUMBLOB NOT NULL, 
                `page` INT NOT NULL, 
                `x` INT NOT NULL, 
                `y` INT NOT NULL, 
                `created_at` TIMESTAMP NULL, 
                `updated_at` TIMESTAMP NULL, 
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
        Schema::dropIfExists('document_stamps');
    }
}
