<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableStamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `stamps` ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT NULL,
                `name` VARCHAR(255) NOT NULL,
                `path` LONGTEXT NULL, 
                `stamp` MEDIUMBLOB NULL,
                `thumb` MEDIUMBLOB NULL,
                `width` INT NULL,
                `height` INT NULL,
                `type` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
        Schema::dropIfExists('stamps');
    }
}
